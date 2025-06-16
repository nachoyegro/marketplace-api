<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;
use App\Models\Benefit;
use App\Enums\UserRole;
use App\Models\Brand;

class BenefitAPITest extends TestCase
{
    use RefreshDatabase;

    // INDEX
    
    // Test the benefit list endpoint without authentication
    public function test_list_requires_authentication() 
    {
        $response = $this->getJson('/api/benefits');
        $response->assertStatus(401);
    }


    // Test the benefits list endpoint as an admin
    public function test_list_as_admin() 
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/benefits');
        $response->assertStatus(200);
    }

    // Test the benefits list endpoint as an admin company
    public function test_list_as_admin_company() 
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN_COMPANY]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/benefits');
        $response->assertStatus(200);
    }

    // Test the benefits list endpoint as a user
    public function test_list_as_user_company() 
    {
        $user = User::factory()->create(['role' => UserRole::USER_COMPANY]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/benefits');
        $response->assertStatus(200);
    }

    // Test create endpoint as admin
    public function test_create_benefit_as_admin()
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        Sanctum::actingAs($user);

        $brand = Brand::factory()->create(['id' => 1]);
        $benefitData = [
            'brand_id' => $brand->id,
            'name' => 'World Cup Final',
            'description' => 'Experience the thrill of the World Cup Final with exclusive benefits.',
            'country_code' => 'US'
        ];

        $response = $this->postJson('/api/benefits', $benefitData);
        $response->assertStatus(201);
        $this->assertDatabaseHas('benefits', $benefitData);
    }

    // Test create endpoint as admin company
    public function test_create_benefit_as_admin_company()
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN_COMPANY]);
        Sanctum::actingAs($user);

        $brand = Brand::factory()->create(['id' => 1]);
        $benefitData = [
            'brand_id' => $brand->id,
            'name' => 'World Cup Final',
            'description' => 'Experience the thrill of the World Cup Final with exclusive benefits.',
            'country_code' => 'US'
        ];

        $response = $this->postJson('/api/benefits', $benefitData);
        $response->assertStatus(403);
    }

    // Test create endpoint as user company
    public function test_create_benefit_as_user_company()
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN_COMPANY]);
        Sanctum::actingAs($user);

        $brand = Brand::factory()->create(['id' => 1]);
        $benefitData = [
            'brand_id' => $brand->id,
            'name' => 'World Cup Final',
            'description' => 'Experience the thrill of the World Cup Final with exclusive benefits.',
            'country_code' => 'US'
        ];

        $response = $this->postJson('/api/benefits', $benefitData);
        $response->assertStatus(403);
    }
}