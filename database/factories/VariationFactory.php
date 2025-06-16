<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Benefit;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Variation>
 */
class VariationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'cost' => $this->faker->randomFloat(2, 1, 100), // Random cost between 1 and 100
            'price' => $this->faker->randomFloat(2, 1, 100), // Random price between 1 and 100
            'price_credits' => $this->faker->numberBetween(1, 1000), // Random credits between 1 and 1000
            'benefit_id' => Benefit::factory(), // Create a benefit for this variation
        ];
    }
}

