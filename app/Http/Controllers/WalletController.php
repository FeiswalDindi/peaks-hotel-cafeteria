<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    // --- 1. STAFF-TO-STAFF P2P TRANSFER ---
    public function transfer(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
        ]);

        $sender = Auth::user();
        $receiver = User::findOrFail($request->receiver_id);
        $amount = $request->amount;

        // Loophole Closed: Prevent sending to yourself
        if ($sender->id == $receiver->id) {
            return redirect()->back()->with('error', 'You cannot transfer funds to yourself.');
        }

        // Loophole Closed: Prevent overdrafts
        if ($sender->wallet_balance < $amount) {
            return redirect()->back()->with('error', 'Insufficient wallet balance for this transfer.');
        }

        try {
            // The Bulletproof Engine
            DB::transaction(function () use ($sender, $receiver, $amount) {
                $sender->decrement('wallet_balance', $amount);
                $receiver->increment('wallet_balance', $amount);

                WalletTransaction::create([
                    'sender_id' => $sender->id,
                    'receiver_id' => $receiver->id,
                    'amount' => $amount,
                    'type' => 'p2p_transfer',
                    'description' => 'Staff to Staff Transfer'
                ]);
            });

            return redirect()->back()->with('success', 'KES ' . number_format($amount) . ' successfully transferred to ' . $receiver->name);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Transfer failed. Please try again.');
        }
    }

    // --- 2. ADMIN FORCE OVERRIDE ---
    public function adminOverride(Request $request)
    {
        $request->validate([
            'sender_id' => 'required|exists:users,id',
            'receiver_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
        ]);

        $sender = User::findOrFail($request->sender_id);
        $receiver = User::findOrFail($request->receiver_id);
        $amount = $request->amount;

        if ($sender->id == $receiver->id) {
            return redirect()->back()->with('error', 'Sender and Receiver cannot be the same person.');
        }

        if ($sender->wallet_balance < $amount) {
            return redirect()->back()->with('error', 'The selected staff member does not have enough funds to override.');
        }

        try {
            DB::transaction(function () use ($sender, $receiver, $amount) {
                $sender->decrement('wallet_balance', $amount);
                $receiver->increment('wallet_balance', $amount);

                WalletTransaction::create([
                    'sender_id' => $sender->id,
                    'receiver_id' => $receiver->id,
                    'amount' => $amount,
                    'type' => 'admin_override',
                    'description' => 'Admin Override Transfer'
                ]);
            });

            return redirect()->back()->with('success', 'Admin Override: KES ' . number_format($amount) . ' securely moved from ' . $sender->name . ' to ' . $receiver->name);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Admin Override failed.');
        }
    }
}