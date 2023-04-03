<?php

namespace Database\Seeders;

use App\Models\v1\User;
use Illuminate\Database\Seeder;
use Database\Factories\V1\OrderFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::whereNot('is_admin', true)->get()->each(function ($user) {
            $user->orders()->saveMany(OrderFactory::times(5)->make());
        });
    }
}
