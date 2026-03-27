<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        $gender = fake()->randomElement(['Male', 'Female']);
        return [
            'name' => fake()->name($gender === 'Male' ? 'male' : 'female'),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'avatar_image' => null,
            'mobile' => fake()->phoneNumber(),
            'country' => fake()->country(),
            'gender' => $gender,
            'approved_by' => User::inRandomOrder()->value('id') ?? null,
            'approved_at' => now(),
            'last_login_at' => fake()->optional()->dateTimeThisYear(),
            'remember_token' => Str::random(10),
        ];
    }
}
