<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class ReceptionistSeeder extends Seeder
{
    public function run(): void
    {
        // Grab all seeded managers to distribute receptionists among them.
        $managers = User::role('manager')->get();

        if ($managers->isEmpty()) {
            $this->command->warn('No managers found – run ManagerSeeder first.');
            return;
        }

        $managers->each(function (User $manager) {
            User::factory(3)
                ->receptionist($manager->id)
                ->create()
                ->each(fn (User $user) => $user->assignRole('receptionist'));
        });
    }
}
