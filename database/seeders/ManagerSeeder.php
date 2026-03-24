<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class ManagerSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(5)
            ->manager()
            ->create()
            ->each(fn (User $user) => $user->assignRole('manager'));
    }
}
