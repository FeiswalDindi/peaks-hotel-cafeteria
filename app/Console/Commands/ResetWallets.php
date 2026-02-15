<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ResetWallets extends Command
{
    // Keeping your original signature!
    protected $signature = 'wallet:reset';

    // Updated description
    protected $description = 'Refills the wallet_balance to match each user\'s unique daily_allocation limit';

    public function handle()
    {
        $this->info('Starting Daily Wallet Reset...');

        // 🌟 THE FIX: This single query finds everyone who has an allocation, 
        // and instantly copies their specific allocation limit straight into their spendable wallet!
        $updatedCount = User::where('daily_allocation', '>', 0)
                            ->update(['wallet_balance' => DB::raw('daily_allocation')]);

        $this->info("Successfully refilled wallets for {$updatedCount} users!");
    }
}