<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use App\Models\Company;
use App\Enums\UserRole;
use App\Models\Employee;
use App\Models\User;

class CompanyAPITest extends TestCase
{
    use RefreshDatabase;
    
    // Test the company list endpoint
    public function test_company_index(): void
    {
        Company::factory()->create(['name' => 'Quilmes']);
        Company::factory()->create(['name' => 'Acme']);
    
        $response = $this->getJson('/api/companies');
    
        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Quilmes'])
                 ->assertJsonFragment(['name' => 'Acme']);
    }

    
    // Test the company filter by name endpoint
    public function test_company_filter_by_name(): void
    {
        Company::factory()->create(['name' => 'Quilmes']);
        Company::factory()->create(['name' => 'Acme']);
    
        $response = $this->getJson('/api/companies?name=Quilmes');
    
        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJsonFragment(['name' => 'Quilmes']);
    }

    // Test billing by company endpoint unauthorized by not being logged in
    public function test_billing_by_company_unauthorized(): void
    {
        $response = $this->getJson('/api/billing-by-company');
        $response->assertStatus(401);
    }

    // Test billing by company endpoint authorized with admin role
    public function test_billing_by_company_authorized_as_admin(): void
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        Sanctum::actingAs($user);
        $response = $this->getJson('/api/billing-by-company');
        
        $response->assertStatus(200);
    }


    // Test billing by company endpoint unauthorized with role user_company
    public function test_billing_by_company_unauthorized_as_user_company(): void
    {
        $user = User::factory()->create(['role' => UserRole::USER_COMPANY]);
        Sanctum::actingAs($user);
        $response = $this->getJson('/api/billing-by-company');
        $response->assertStatus(403);
    }

    // Test billing by company endpoint unauthorized with role admin_company
    public function test_billing_by_company_unauthorized_as_admin_company(): void
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN_COMPANY]);
        Sanctum::actingAs($user);
        $response = $this->getJson('/api/billing-by-company');
        $response->assertStatus(403);
    }

    // Test billing by company endpoint calculates correctly
    public function test_billing_by_company_calculates_correctly()
    {
        config(['platform.cost_per_user' => 5]);
        
        $company = Company::factory()->create();
        Employee::factory()->count(3)->create([
            'company_id' => $company->id,
        ]);

        
        $admin = User::factory()->create([
            'role' => UserRole::ADMIN->value,
        ]);

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/billing-by-company');

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'company_id' => $company->id,
            'user_count' => 3,
            'total_usd' => 15.0, // 3 * $5
        ]);
    }
}
