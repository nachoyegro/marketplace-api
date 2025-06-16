<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Variation;
use App\Models\Employee;
use App\Models\GiftCard;
use App\Models\Company;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
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
            'variation_id' => Variation::factory(),
            'employee_id' => Employee::factory(),
            'company_id' => Company::factory(),
            'gift_card_id' => GiftCard::factory(),
            'cost' => $this->faker->randomFloat(2, 1, 100), // Random cost between 1 and 100
            'sale_price' => $this->faker->randomFloat(2, 1, 100), // Random sale price between 1 and 100
            'sale_price_credits' => $this->faker->numberBetween(1, 1000), // Random credits between 1 and 1000
        ];
    }
}