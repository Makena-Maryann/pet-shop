<?php

namespace Database\Seeders;

use App\Models\v1\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'first_name' => 'Pet',
            'last_name' => 'Admin',
            'is_admin' => true,
            'email' => 'admin@buckhill.co.uk',
            'password' => Hash::make('admin'),
            'email_verified_at' => now(),
            'address' => 'Remetinečka cesta 13 10 000 Zagreb, Croatia',
            'phone_number' => '01284 810 810',
            'is_marketing' => true,
        ]);

        User::factory()->count(20)->create();
    }
}
