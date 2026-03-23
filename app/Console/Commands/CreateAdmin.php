<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateAdmin extends Command
{
    protected $signature = 'create:admin
                            {--name= : The email of the admin}
                            {--password= : The password of the admin}';

    protected $description = 'Create a new admin user';

    public function handle(): void
    {
        $email    = $this->option('name');
        $password = $this->option('password');

        if (!$email || !$password) {
            $this->error('Both --name and --password are required.');
            return;
        }

        if (strlen($password) < 6) {
            $this->error('Password must be at least 6 characters.');
            return;
        }

        if (User::where('email', $email)->exists()) {
            $this->error('A user with this email already exists.');
            return;
        }

        $admin = User::create([
            'name'         => 'Admin',
            'email'        => $email,
            'password'     => bcrypt($password),
            'national_id'  => uniqid('ADM'),
            'avatar_image' => null,
        ]);

        $admin->assignRole('admin');

        $this->info("Admin created successfully: {$email}");
    }
}
