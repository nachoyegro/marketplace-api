<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;
use App\Models\Variation;
use App\Enums\UserRole;
use App\Models\Benefit;

class VariationAPITest extends TestCase
{
    use RefreshDatabase;

    // INDEX
    
    // Test the company list endpoint without authentication
    public function test_list_requires_authentication() 
    {
        $response = $this->getJson('/api/variations');
        $response->assertStatus(401);
    }


    // Test the variations list endpoint as an admin
    public function test_list_as_admin() 
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/variations');
        $response->assertStatus(200);
    }

    // Test the variations list endpoint as an admin company
    public function test_list_as_admin_company() 
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN_COMPANY]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/variations');
        $response->assertStatus(200);
    }

    // Test the variations list endpoint as a user
    public function test_list_as_user_company() 
    {
        $user = User::factory()->create(['role' => UserRole::USER_COMPANY]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/variations');
        $response->assertStatus(200);
    }

    // Test create endpoint as admin
    public function test_create_variation_as_admin()
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        Sanctum::actingAs($user);

        $benefit = Benefit::factory()->create(['id' => 1]);
        $variationData = [
            'benefit_id' => $benefit->id,
            'title' => 'TEST',
            'cost' => 100.00,
            'price' => 150.00,
            'price_credits' => 10
        ];

        $response = $this->postJson('/api/variations', $variationData);
        $response->assertStatus(201);
        $this->assertDatabaseHas('variations', $variationData);
    }

    // Test create endpoint as admin company
    public function test_create_variation_as_admin_company()
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN_COMPANY]);
        Sanctum::actingAs($user);

        $benefit = Benefit::factory()->create(['id' => 1]);
        $variationData = [
            'benefit_id' => $benefit->id,
            'title' => 'TEST',
            'cost' => 100.00,
            'price' => 150.00,
            'price_credits' => 10
        ];

        $response = $this->postJson('/api/variations', $variationData);
        $response->assertStatus(403);
    }

    // Test create endpoint as user company
    public function test_create_variation_as_user_company()
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN_COMPANY]);
        Sanctum::actingAs($user);

        $benefit = Benefit::factory()->create(['id' => 1]);
        $variationData = [
            'benefit_id' => $benefit->id,
            'title' => 'TEST',
            'cost' => 100.00,
            'price' => 150.00,
            'price_credits' => 10
        ];

        $response = $this->postJson('/api/variations', $variationData);
        $response->assertStatus(403);
    }
}