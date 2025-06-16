<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\CompanySeeder;
use Database\Seeders\EmployeeSeeder;
use Database\Seeders\BrandSeeder;
use Database\Seeders\BenefitSeeder;
use Database\Seeders\VariationSeeder;
use Database\Seeders\OrderSeeder;
use Database\Seeders\GiftCardSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CompanySeeder::class,
            EmployeeSeeder::class,
            BrandSeeder::class,
            BenefitSeeder::class,
            VariationSeeder::class,
            OrderSeeder::class,
            GiftCardSeeder::class,
        ]);
    }
}
