<?php

namespace Database\Seeders;

use App\Models\v1\OrderStatus;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            'Pending',
            'Processing',
            'Paid',
            'Shipped',
            'Cancelled',
        ];

        foreach ($statuses as $status) {
            OrderStatus::create([
                'title' => $status,
            ]);
        }
    }
}
