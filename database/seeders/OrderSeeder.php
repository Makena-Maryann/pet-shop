<?php

namespace Database\Seeders;

use App\Models\v1\User;
use App\Models\v1\Order;
use App\Models\v1\Payment;
use Illuminate\Database\Seeder;
use Database\Factories\V1\OrderFactory;
use Database\Factories\V1\PaymentFactory;
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

        $orders = Order::where('order_status_id', 3)->orWhere('order_status_id', 4)->get();

        foreach ($orders as $order) {
            $payment = PaymentFactory::new()->state([
                'details' => [
                    'holder_name' => $order->user->name,
                    'first_name' => $order->user->first_name,
                    'last_name' => $order->user->last_name,
                    'address' => $order->user->address,
                    'name' => $order->user->name,
                ],
            ])->make();
            $payment->save();
            $order->update(['payment_id' => $payment->id]);
        }
    }
}
