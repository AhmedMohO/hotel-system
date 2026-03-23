<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
class AdminSeeder extends Seeder
{
public function run(): void
{
    $admin = User::create([
        'name' => 'Admin',
        'email' => 'admin@admin.com',
        'password' => bcrypt('123456'),
        'national_id' => '0000000000',
        'avatar_image' => null,
    ]);

    $admin->assignRole('admin');
}
}
