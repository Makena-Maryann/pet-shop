<?php

namespace Database\Seeders;

use App\Models\v1\Payment;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentTypes = [
            [
                'title' => 'Credit Card',
                'details' => [
                    'holder_name' => 'string',
                    'number' => 'string',
                    'ccv' => 'int',
                    'expire_date' => 'string',
                ],
            ],
            [
                'title' => 'Cash on Delivery',
                'details' => [
                    'first_name' => 'string',
                    'last_name' => 'string',
                    'address' => 'string',
                ],
            ],
            [
                'title' => 'Bank Transfer',
                'details' => [
                    'swift' => 'string',
                    'iban' => 'string',
                    'name' => 'string',
                ],
            ],
        ];

        foreach ($paymentTypes as $paymentType) {
            Payment::create([
                'type' => $paymentType['title'],
                'details' => $paymentType['details'],
            ]);
        }
    }
}
