<?php

namespace Database\Factories\V1;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\v1\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'is_admin' => false,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('userpassword'),
            'email_verified_at' => now(),
            'avatar' => $this->faker->uuid(),
            'address' => $this->faker->address,
            'phone_number' => $this->faker->phoneNumber,
            'is_marketing' => false,
        ];
    }
}
