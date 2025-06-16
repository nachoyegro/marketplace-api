<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GiftCard>
 */
class GiftCardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'card_number' => $this->faker->unique()->creditCardNumber(), // Unique card number
            'expiration_date' => $this->faker->dateTimeBetween('now', '+1 years')->format('Y-m-d'), // Expiration date of the gift card
            'security_code' => $this->faker->numerify('####'), // Security code (CVV) for the gift card
        ];
    }
}