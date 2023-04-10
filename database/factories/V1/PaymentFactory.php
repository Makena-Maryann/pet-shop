<?php

namespace Database\Factories\V1;

use App\Models\v1\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\v1\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(['credit_card', 'cash_on_delivery', 'bank_transfer']),
            'details' => function (array $attributes) {
                switch ($attributes['type']) {
                    case 'credit_card':
                        return [
                            'holder_name' => $this->faker->name,
                            'number' => $this->faker->creditCardNumber,
                            'ccv' => $this->faker->numberBetween(100, 999),
                            'expire_date' => $this->faker->date('Y-m-d', 'now'),
                        ];
                    case 'cash_on_delivery':
                        return [
                            'first_name' => $this->faker->firstName,
                            'last_name' => $this->faker->lastName,
                            'address' => $attributes['user']->address,
                        ];
                    case 'bank_transfer':
                        return [
                            'swift' => $this->faker->swiftBicNumber,
                            'iban' => $this->faker->iban('DE'),
                            'name' => $this->faker->name,
                        ];
                }
            },
        ];
    }
}
