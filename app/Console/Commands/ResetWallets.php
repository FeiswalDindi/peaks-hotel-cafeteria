<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ResetWallets extends Command
{
    // The command you type in the terminal
    protected $signature = 'wallet:reset';

    // What the command does
    protected $description = 'Resets daily allocations: 500 for Admin, 200 for Staff';

    public function handle()
    {
        $users = User::all();
        $count = 0;

        foreach ($users as $user) {
            // Give Admins 500
            if ($user->hasRole('admin')) {
                $user->update(['daily_allocation' => 500]);
                $count++;
            } 
            // Give normal Staff 200
            elseif ($user->hasRole('staff')) {
                $user->update(['daily_allocation' => 200]);
                $count++;
            }
        }

        $this->info("Successfully reset wallets for {$count} users!");
    }
}