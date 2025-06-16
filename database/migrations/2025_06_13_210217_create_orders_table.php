<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variation_id');
            $table->foreignId('employee_id');
            $table->foreignId('company_id');
            $table->foreignId('gift_card_id');
            $table->decimal('cost', 10, 2); // Cost in local currency of the variation
            $table->decimal('sale_price', 10, 2); // Sale price in local currency (how much it was sold for in local currency)
            $table->unsignedInteger('sale_price_credits'); // Sale price in credits (how much it was sold for in credits)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
