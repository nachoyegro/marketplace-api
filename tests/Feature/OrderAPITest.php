<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use App\Enums\UserRole;
use App\Models\User;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Order;
use App\Models\Variation;
use App\Models\GiftCard;

class OrderAPITest extends TestCase
{
    use RefreshDatabase;

    // INDEX

    // Test the company list endpoint without authentication
    public function test_list_requires_authentication() 
    {
        $response = $this->getJson('/api/orders');
        $response->assertStatus(401);
    }

    // Test the company list endpoint with admin role
    public function test_list_as_admin() 
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/orders');
        $response->assertStatus(200);
    }

    // Test the company list endpoint with user_company role, getting orders from own company
    public function test_list_as_admin_company_from_own_company() 
    {
        $company = Company::factory()->create();

        // User with ADMIN_COMPANY role for company1
        $user = User::factory()->create([
            'role' => UserRole::ADMIN_COMPANY,
        ]);

        // Employee for company
        $employee = Employee::factory()->create([
            'company_id' => $company->id,
            'user_id' => $user->id, 
        ]);

        Sanctum::actingAs($user);

        $order = Order::factory()->create([
            'company_id' => $company->id, 
            'employee_id' => $employee->id,
            'cost' => 100,
            'sale_price' => 150,
            'sale_price_credits' => 50,
        ]);

        $response = $this->getJson('/api/orders');
        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data');
    }

    // Test the company list endpoint with user_company role, getting orders from other company
    // with multiple orders, one from own company and one from other company
    public function test_list_as_admin_company_from_other_company() 
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        // User with ADMIN_COMPANY role for company1
        $user = User::factory()->create([
            'role' => UserRole::ADMIN_COMPANY,
        ]);

        // Employee for company1
        $employee1 = Employee::factory()->create([
            'company_id' => $company1->id,
            'user_id' => $user->id, 
        ]);

        // Employee for company2
        $employee2 = Employee::factory()->create([
            'company_id' => $company2->id,
        ]);


        Sanctum::actingAs($user);

        $order1 = Order::factory()->create([
            'company_id' => $company1->id, 
            'employee_id' => $employee1->id,
            'cost' => 100,
            'sale_price' => 150,
            'sale_price_credits' => 50,
        ]);

        $order2 = Order::factory()->create([
            'company_id' => $company2->id, 
            'employee_id' => $employee2->id,
            'cost' => 100,
            'sale_price' => 150,
            'sale_price_credits' => 50,
        ]);

        $response = $this->getJson('/api/orders');
        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data');

    }

    // Test the order list endpoint with user_company role, 
    // with only retrieving own orders
    public function test_list_as_user_company() 
    {
        $company = Company::factory()->create();

        // User with USER_COMPANY role for company1
        $user = User::factory()->create([
            'role' => UserRole::USER_COMPANY,
        ]);

        // Employee for company1
        $employee1 = Employee::factory()->create([
            'company_id' => $company->id,
            'user_id' => $user->id, 
        ]);

        // Employee for company2
        $employee2 = Employee::factory()->create([
            'company_id' => $company->id,
        ]);


        Sanctum::actingAs($user);

        $order1 = Order::factory()->create([
            'company_id' => $company->id, 
            'employee_id' => $employee1->id,
            'cost' => 100,
            'sale_price' => 150,
            'sale_price_credits' => 50,
        ]);

        $order2 = Order::factory()->create([
            'company_id' => $company->id, 
            'employee_id' => $employee2->id,
            'cost' => 100,
            'sale_price' => 150,
            'sale_price_credits' => 50,
        ]);

        $response = $this->getJson('/api/orders');
        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data');
    }

    // VIEW
    // Test the view order endpoint without authentication
    public function test_view_requires_authentication() 
    {
        $order = Order::factory()->create();
        $response = $this->getJson("/api/orders/{$order->id}");
        $response->assertStatus(401);
    }

    // Test the view order endpoint with admin role
    public function test_view_as_admin() 
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        Sanctum::actingAs($user);

        $order = Order::factory()->create();
        $response = $this->getJson("/api/orders/{$order->id}");
        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $order->id]);
    }

    // Test the view order endpoint with admin_company role from own company
    public function test_view_as_admin_company_from_own_company()
    {
        $company = Company::factory()->create();

        // User with ADMIN_COMPANY role for company1
        $user = User::factory()->create([
            'role' => UserRole::ADMIN_COMPANY,
        ]);

        // Employee for company
        $employee = Employee::factory()->create([
            'company_id' => $company->id,
            'user_id' => $user->id, 
        ]);

        Sanctum::actingAs($user);

        $order = Order::factory()->create([
            'company_id' => $company->id, 
            'employee_id' => $employee->id,
            'cost' => 100,
            'sale_price' => 150,
            'sale_price_credits' => 50,
        ]);

        $response = $this->getJson("/api/orders/{$order->id}");
        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $order->id]);
    }

    // Test the view order endpoint with admin_company role from other company
    public function test_view_as_admin_company_from_other_company() 
    {
        $company = Company::factory()->create();
        $company2 = Company::factory()->create();

        // User with ADMIN_COMPANY role for company1
        $user = User::factory()->create([
            'role' => UserRole::ADMIN_COMPANY,
        ]);

        // Employee for company
        $employee = Employee::factory()->create([
            'company_id' => $company->id,
            'user_id' => $user->id, 
        ]);

        $employee2 = Employee::factory()->create([
            'company_id' => $company2->id,
        ]);

        Sanctum::actingAs($user);

        $order = Order::factory()->create([
            'company_id' => $company2->id, 
            'employee_id' => $employee2->id,
            'cost' => 100,
            'sale_price' => 150,
            'sale_price_credits' => 50,
        ]);

        $response = $this->getJson("/api/orders/{$order->id}");
        $response->assertStatus(403);
    }

    // Test the view order endpoint with user_company role, getting own order
    public function test_view_as_user_company() 
    {
        $company = Company::factory()->create();

        // User with USER_COMPANY role for company1
        $user = User::factory()->create([
            'role' => UserRole::USER_COMPANY,
        ]);

        // Employee for company
        $employee = Employee::factory()->create([
            'company_id' => $company->id,
            'user_id' => $user->id, 
        ]);

        Sanctum::actingAs($user);

        $order = Order::factory()->create([
            'company_id' => $company->id, 
            'employee_id' => $employee->id,
            'cost' => 100,
            'sale_price' => 150,
            'sale_price_credits' => 50,
        ]);

        $response = $this->getJson("/api/orders/{$order->id}");
        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $order->id]);

    }

    // Test the view order endpoint with user_company role, 
    // getting order from other employee
    public function test_view_as_user_company_other_employee() 
    {
        $company = Company::factory()->create();

        // User with USER_COMPANY role for company1
        $user = User::factory()->create([
            'role' => UserRole::USER_COMPANY,
        ]);

        // Employee for company
        $employee = Employee::factory()->create([
            'company_id' => $company->id,
            'user_id' => $user->id, 
        ]);
        $employee2 = Employee::factory()->create([
            'company_id' => $company->id,
        ]);

        Sanctum::actingAs($user);

        $order = Order::factory()->create([
            'company_id' => $company->id, 
            'employee_id' => $employee2->id,
            'cost' => 100,
            'sale_price' => 150,
            'sale_price_credits' => 50,
        ]);

        $response = $this->getJson("/api/orders/{$order->id}");
        $response->assertStatus(403);

    }

    // STORE

    // Test the store order endpoint without authentication
    public function test_store_requires_authentication() 
    {
        $response = $this->postJson('/api/orders', []);
        $response->assertStatus(401);
    }

    // Test the store order endpoint with user_company role
    // it should not let the user create an order
    public function test_store_as_admin_company() 
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN_COMPANY]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/orders', []);
        $response->assertStatus(403);
    }

    // Test the store order endpoint with user_company role
    // it should let the user create an order
    public function test_store_order_as_user_company() 
    {
        $admin = User::factory()->create(['role' => UserRole::USER_COMPANY]);
        Sanctum::actingAs($admin);

        $company = Company::factory()->create();
        $employee = Employee::factory()->create(['company_id' => $company->id, 'user_id' => $admin->id]);

        $variation = Variation::factory()->create();
        $giftCard = GiftCard::factory()->create();

        $data = [
            'variation_id' => $variation->id,
            'gift_card_id' => $giftCard->id, 
            'company_id' => $company->id,
            'employee_id' => $employee->id,
            'cost' => 100,
            'sale_price' => 150,
            'sale_price_credits' => 50,
        ];

        $response = $this->postJson('/api/orders', $data);
        $response->assertStatus(201);
    }
    
    

    // CONSUMPTION LAST WEEK

    /**
     * Test consumption last week endpoint unauthorized by not being logged in
     */
    public function test_consumption_last_week_unauthorized(): void
    {
        $company = Company::factory()->create();
        $response = $this->getJson("/api/consumption-last-week?company_id={$company->id}");
        $response->assertStatus(401);
    }

    /**
     * Test consumption last week endpoint authorized with admin role
     */
    public function test_consumption_last_week_authorized_as_admin(): void
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        Sanctum::actingAs($user);

        $company = Company::factory()->create();
        $response = $this->getJson("/api/consumption-last-week?company_id={$company->id}");
        
        $response->assertStatus(200);
    }

    /**
     * Test consumption last week endpoint unauthorized with role user_company
     */
    public function test_consumption_last_week_unauthorized_as_user_company(): void
    {
        $user = User::factory()->create(['role' => UserRole::USER_COMPANY]);
        Sanctum::actingAs($user);

        $company = Company::factory()->create();
        $response = $this->getJson("/api/consumption-last-week?company_id={$company->id}");

        $response->assertStatus(403);
    }

    /**
     * Test consumption last week endpoint unauthorized with role admin_company
     */
    public function test_consumption_last_week_unauthorized_as_admin_company(): void
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN_COMPANY]);
        Sanctum::actingAs($user);

        $company = Company::factory()->create();
        $response = $this->getJson("/api/consumption-last-week?company_id={$company->id}");

        $response->assertStatus(403);
    }

    /**
     * Test consumption last week endpoint fails without company_id param
     */
    public function test_consumption_last_week_fails_without_company_id(): void
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        Sanctum::actingAs($user);

        // No company_id provided
        $response = $this->getJson('/api/consumption-last-week');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['company_id']);
    }

    /**
     * Test consumption last week endpoint works correctly with valid company_id
     */
    public function test_consumption_last_week_authorized_with_valid_company_id(): void
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        Sanctum::actingAs($user);

        $company = Company::factory()->create();

        Order::factory()->count(5)->create([
            'company_id' => $company->id,
            'created_at' => now()->subDays(rand(1, 6)),
        ]);

        $response = $this->getJson("/api/consumption-last-week?company_id={$company->id}");

        $response->assertStatus(200);

        // Assert the response corresponds to the company
        $response->assertJsonFragment([
            'company_id' => $company->id,
            'company_name' => $company->name,
        ]);
        
        // Assert the response contains the correct number of consumptions
        $this->assertCount(1, $response['consumptions']['data']);
    }
}