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
        $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
            ->get("{$this->baseUrl}/oauth/v1/generate?grant_type=client_credentials");

        if ($response->successful()) {
            return $response->json()['access_token'];
        }

        Log::error('M-Pesa Token Error: ' . $response->body());
        return null;
    }

    public function stkPush($phoneNumber, $amount, $reference)
    {
        $token = $this->getAccessToken();
        if (!$token) return ['success' => false, 'message' => 'Token Generation Failed. Check your .env keys.'];

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
            // ✅ Use a valid-looking URL even on localhost to avoid silent rejection
            "CallBackURL" => "https://mydomain.com/api/callback", 
            "AccountReference" => "KCACafe",
            "TransactionDesc" => "Payment for Food"
        ];

        try {
            $response = Http::withToken($token)->post("{$this->baseUrl}/mpesa/stkpush/v1/processrequest", $payload);

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }

            $res = $response->json();
            return ['success' => false, 'message' => $res['errorMessage'] ?? 'STK Push Failed'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Connection Error: ' . $e->getMessage()];
        }
    }

/**
     * Step 3: Check Transaction Status (STK Query)
     * Use this if you are on Localhost or didn't get a Callback
     */
    public function queryStkStatus($checkoutRequestId)
    {
        $token = $this->getAccessToken();
        if (!$token) return ['success' => false, 'message' => 'Token Error'];

        $timestamp = date('YmdHis');
        $password = base64_encode($this->shortCode . $this->passkey . $timestamp);

        $payload = [
            "BusinessShortCode" => $this->shortCode,
            "Password" => $password,
            "Timestamp" => $timestamp,
            "CheckoutRequestID" => $checkoutRequestId
        ];

        try {
            $response = Http::withToken($token)
                ->post("{$this->baseUrl}/mpesa/stkpushquery/v1/query", $payload);

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }
            
            return ['success' => false, 'message' => $response->json()['errorMessage'] ?? 'Query Failed'];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

}