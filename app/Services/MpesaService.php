<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpesaService
{
    protected $consumerKey;
    protected $consumerSecret;
    protected $shortCode;
    protected $passkey;
    protected $env;
    protected $baseUrl;

    public function __construct()
    {
        $this->consumerKey = env('MPESA_CONSUMER_KEY');
        $this->consumerSecret = env('MPESA_CONSUMER_SECRET');
        $this->shortCode = env('MPESA_BUSINESS_SHORTCODE');
        $this->passkey = env('MPESA_PASSKEY');
        $this->env = env('MPESA_ENV', 'sandbox');

        $this->baseUrl = $this->env === 'production' 
            ? 'https://api.safaricom.co.ke' 
            : 'https://sandbox.safaricom.co.ke';
    }

    public function getAccessToken()
    {
        try {
            // 🌟 SAFETY NET: Retry once if it fails, give up after 10 seconds so the app doesn't freeze
            $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
                ->retry(2, 100) 
                ->timeout(10)   
                ->get("{$this->baseUrl}/oauth/v1/generate?grant_type=client_credentials");

            if ($response->successful()) {
                return $response->json()['access_token'] ?? null;
            }

            Log::error('M-Pesa Token Error: ' . $response->body());
            return null;
            
        } catch (\Exception $e) {
            Log::error('M-Pesa Connection Down (Token): ' . $e->getMessage());
            return null;
        }
    }

    public function stkPush($phoneNumber, $amount, $reference)
    {
        $token = $this->getAccessToken();
        if (!$token) return ['success' => false, 'message' => 'System could not connect to Safaricom. Please try again later.'];

        $timestamp = date('YmdHis');
        $password = base64_encode($this->shortCode . $this->passkey . $timestamp);

        $payload = [
            "BusinessShortCode" => $this->shortCode,
            "Password" => $password,
            "Timestamp" => $timestamp,
            "TransactionType" => "CustomerPayBillOnline",
            "Amount" => (int)round($amount), 
            "PartyA" => $phoneNumber,
            "PartyB" => $this->shortCode,
            "PhoneNumber" => $phoneNumber,
            "CallBackURL" => "https://mydomain.com/api/callback", 
            "AccountReference" => "KCACafe",
            "TransactionDesc" => "Payment for Food"
        ];

        try {
            // 🌟 SAFETY NET: 15-second strict timeout for the STK prompt
            $response = Http::withToken($token)
                ->timeout(15)
                ->post("{$this->baseUrl}/mpesa/stkpush/v1/processrequest", $payload);

            if ($response->successful()) {
                $res = $response->json();
                
                // Safaricom sometimes returns "Success" HTTP codes but embeds an error code (ResponseCode != 0) inside
                if(isset($res['ResponseCode']) && $res['ResponseCode'] == '0') {
                    return ['success' => true, 'data' => $res];
                }
                
                return ['success' => false, 'message' => $res['CustomerMessage'] ?? 'Safaricom rejected the request.'];
            }

            $res = $response->json();
            Log::error('M-Pesa STK Push Failed: ', (array)$res);
            return ['success' => false, 'message' => $res['errorMessage'] ?? 'Safaricom servers are currently unreachable.'];

        } catch (\Exception $e) {
            Log::error('M-Pesa Exception (STK Push): ' . $e->getMessage());
            return ['success' => false, 'message' => 'Connection to M-Pesa timed out. Please check your network.'];
        }
    }

    public function queryStkStatus($checkoutRequestId)
    {
        $token = $this->getAccessToken();
        if (!$token) return ['success' => false, 'message' => 'System could not connect to Safaricom.'];

        $timestamp = date('YmdHis');
        $password = base64_encode($this->shortCode . $this->passkey . $timestamp);

        $payload = [
            "BusinessShortCode" => $this->shortCode,
            "Password" => $password,
            "Timestamp" => $timestamp,
            "CheckoutRequestID" => $checkoutRequestId
        ];

        try {
            // 🌟 SAFETY NET: 10-second timeout for status checks
            $response = Http::withToken($token)
                ->timeout(10)
                ->post("{$this->baseUrl}/mpesa/stkpushquery/v1/query", $payload);

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }
            
            $errorData = $response->json();
            
            // Log this quietly, as it often just means the user hasn't typed their PIN yet
            Log::info('M-Pesa STK Query Status: ', (array)$errorData);
            
            return ['success' => false, 'message' => $errorData['errorMessage'] ?? 'Transaction is still processing.'];

        } catch (\Exception $e) {
            Log::error('M-Pesa Exception (Query): ' . $e->getMessage());
            return ['success' => false, 'message' => 'Timeout while checking status. Please wait a moment.'];
        }
    }
}