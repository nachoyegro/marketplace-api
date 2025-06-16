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

class OrderAPITest extends TestCase
{
    use RefreshDatabase;
    
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