<?php

namespace Database\Factories\V1;

use App\Models\v1\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\v1\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_uuid' => function () {
                return Category::factory()->create()->uuid;
            },
            'title' => $this->faker->sentence(),
            'slug' => $this->faker->slug(),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 10, 100),
            'metadata' => [
                'brand' => $this->faker->uuid,
                'image' => $this->faker->uuid,
            ],
        ];
    }
}
