<?php

namespace Database\Factories\V1;

use App\Models\v1\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\v1\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_status_id' => $this->faker->numberBetween(1, 5),
            'payment_id' => function (array $attributes) {
                if ($attributes['order_status_id'] == 3 || $attributes['order_status_id'] == 4) {
                    return $this->faker->numberBetween(1, 3);
                }
            },
            'products' => function () {
                $products = Product::inRandomOrder()->take($this->faker->numberBetween(1, 5))->get();
                return $products->map(function ($product) {
                    return [
                        'product' => $product->uuid,
                        'quantity' => $this->faker->numberBetween(1, 5),
                    ];
                });
            },
            'address' => [
                'billing' => $this->faker->address,
                'shipping' => $this->faker->address,
            ],
            'amount' => function (array $attributes) {
                $totalAmount = 0;
                foreach ($attributes['products'] as $product) {
                    $totalAmount += Product::where('uuid', $product['product'])->value('price') * $product['quantity'];
                }

                return $totalAmount;
            },
            'delivery_fee' => function (array $attributes) {
                return $attributes['amount'] > 500 ? 0 : 15;
            },
            'shipped_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
