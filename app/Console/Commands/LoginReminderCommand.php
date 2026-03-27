<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Notifications\LoginReminderNotification;
use Illuminate\Console\Command;

class LoginReminderCommand extends Command
{
    protected $signature = 'reminder:login';

    protected $description = 'Send login reminder emails to inactive clients';

    public function handle(): int
    {
        $cutoff = now()->subDays(30);

        $clients = Client::query()
            ->whereNull('last_login_at')
            ->orWhere('last_login_at', '<', $cutoff)
            ->get(['id', 'email', 'name', 'last_login_at']);

        if ($clients->isEmpty()) {
            $this->info('No inactive clients found.');

            return self::SUCCESS;
        }

        foreach ($clients as $client) {
            $client->notify(new LoginReminderNotification($client->name));
        }

        $this->info("Queued login reminders for {$clients->count()} client(s).");

        return self::SUCCESS;
    }
}
