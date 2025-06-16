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

    // VIEW

    // Test the company list endpoint without authentication
    public function test_view_requires_authentication() 
    {
        $response = $this->getJson('/api/companies');
        $response->assertStatus(401);
    }

    // Test the company list endpoint as an admin
    public function test_view_as_admin() 
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/companies');
        $response->assertStatus(200);
    }

    // Test the company list endpoint as an admin of a company
    public function test_view_as_admin_company_from_own_company() 
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        // User with ADMIN_COMPANY role for company1
        $adminCompanyUser = User::factory()->create([
            'role' => UserRole::ADMIN_COMPANY,
        ]);
        Sanctum::actingAs($adminCompanyUser);

        // Employee for company1
        $employeeCompany1 = Employee::factory()->create([
            'company_id' => $company1->id,
            'user_id' => $adminCompanyUser->id, 
        ]);


        $response = $this->getJson("/api/companies/{$company1->id}");
        $response->assertStatus(200);

    }

    // Test the company list endpoint as an admin of a company from another company
    public function test_view_as_admin_company_from_other_company() 
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        // User with ADMIN_COMPANY role for company1
        $adminCompanyUser = User::factory()->create([
            'role' => UserRole::ADMIN_COMPANY,
        ]);
        Sanctum::actingAs($adminCompanyUser);

        // Employee for company1
        $employeeCompany1 = Employee::factory()->create([
            'company_id' => $company1->id,
            'user_id' => $adminCompanyUser->id, 
        ]);


        $response = $this->getJson("/api/companies/{$company2->id}");
        $response->assertStatus(403);
    }

    // Test the company view endpoint as user_company of own company
    public function test_view_as_user_company_from_own_company() 
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        // User with USER_COMPANY role for company1
        $adminCompanyUser = User::factory()->create([
            'role' => UserRole::USER_COMPANY,
        ]);
        Sanctum::actingAs($adminCompanyUser);

        // Employee for company1
        $employeeCompany1 = Employee::factory()->create([
            'company_id' => $company1->id,
            'user_id' => $adminCompanyUser->id, 
        ]);


        $response = $this->getJson("/api/companies/{$company1->id}");
        $response->assertStatus(200);

    }

    // Test the company view endpoint as user_company
    public function test_view_as_user_company_from_other_company() 
    {
        
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        // User with USER_COMPANY role for company1
        $adminCompanyUser = User::factory()->create([
            'role' => UserRole::USER_COMPANY,
        ]);
        Sanctum::actingAs($adminCompanyUser);

        // Employee for company1
        $employeeCompany1 = Employee::factory()->create([
            'company_id' => $company1->id,
            'user_id' => $adminCompanyUser->id, 
        ]);


        $response = $this->getJson("/api/companies/{$company2->id}");
        $response->assertStatus(403);
    }

    
    // INDEX

    // Test the company list endpoint
    public function test_company_index_as_admin()
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        Sanctum::actingAs($user);

        Company::factory()->create(['name' => 'Quilmes']);
        Company::factory()->create(['name' => 'Acme']);
    
        $response = $this->getJson('/api/companies');
    
        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Quilmes'])
                 ->assertJsonFragment(['name' => 'Acme']);
    }


    // Test the company list endpoint as a user with user_company role
    public function test_company_index_unauthenticated()
    {
        $response = $this->getJson('/api/companies');
        $response->assertStatus(401);
    }

    // Test the company list endpoint as a user with user_company role
    public function test_company_index_as_admin_company()
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN_COMPANY]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/companies');
        $response->assertStatus(403);
    }

    // Test the company list endpoint as a user with user_company role
    public function test_company_index_as_user_company()
    {
        $user = User::factory()->create(['role' => UserRole::USER_COMPANY]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/companies');
        $response->assertStatus(403);
    }

    
    // Test the company filter by name endpoint
    public function test_company_filter_by_name_as_admin()
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        Sanctum::actingAs($user);

        Company::factory()->create(['name' => 'Quilmes']);
        Company::factory()->create(['name' => 'Acme']);
    
        $response = $this->getJson('/api/companies?name=Quilmes');
    
        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJsonFragment(['name' => 'Quilmes']);
    }

    // STORE
    // Test the store company endpoint without authentication
    public function test_store_requires_authentication() 
    {
        $response = $this->postJson('/api/companies', [
            'name' => 'Maslow',
            'billing_address' => '123 Main St',
        ]);
        $response->assertStatus(401);
    }

    // Test the store company endpoint as an admin
    public function test_store_as_admin() 
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/companies', [
            'name' => 'Maslow',
            'billing_address' => '123 Main St',
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => 'Maslow']);
    }

    // Test the store company endpoint as an admin of a company
    public function test_store_as_admin_company() 
    {
        $company = Company::factory()->create();
        $user = User::factory()->create([
            'role' => UserRole::ADMIN_COMPANY
        ]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/companies', [
            'name' => 'Maslow',
            'billing_address' => '123 Main St',
        ]);

        $response->assertStatus(403);
    }

    // Test the store company endpoint as a user_company
    public function test_store_as_user_company() 
    {
        $user = User::factory()->create(['role' => UserRole::USER_COMPANY]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/companies', [
            'name' => 'Maslow',
            'billing_address' => '123 Main St',
        ]);

        $response->assertStatus(403);
    }

    // UPDATE
    // Test the update company endpoint without authentication
    public function test_update_requires_authentication() 
    {
        $company = Company::factory()->create();
        $response = $this->putJson("/api/companies/{$company->id}", [
            'name' => 'Maslow 2',
            'billing_address' => '456 Elm St',
        ]);
        $response->assertStatus(401);
    }

    // Test the update company endpoint as an admin
    public function test_update_as_admin() 
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        Sanctum::actingAs($user);

        $company = Company::factory()->create(['name' => 'Maslow']);
        
        $response = $this->putJson("/api/companies/{$company->id}", [
            'name' => 'Maslow 2',
            'billing_address' => '456 Elm St',
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Maslow 2']);
    }

    // Test the update company endpoint as an admin of a company
    public function test_update_as_admin_company() 
    {
        $company = Company::factory()->create();
        $user = User::factory()->create([
            'role' => UserRole::ADMIN_COMPANY,
        ]);
        Sanctum::actingAs($user);

        $response = $this->putJson("/api/companies/{$company->id}", [
            'name' => 'Maslow 2',
            'billing_address' => '456 Elm St',
        ]);

        $response->assertStatus(403);
    }

    // Test the update company endpoint as a user_company
    public function test_update_as_user_company() 
    {
        $user = User::factory()->create(['role' => UserRole::USER_COMPANY]);
        Sanctum::actingAs($user);

        $company = Company::factory()->create(['name' => 'Maslow']);
        
        $response = $this->putJson("/api/companies/{$company->id}", [
            'name' => 'Maslow 2',
            'billing_address' => '456 Elm St',
        ]);

        $response->assertStatus(403);
    }

    // DESTROY

    // Test the destroy company endpoint without authentication
    public function test_destroy_requires_authentication() 
    {
        $company = Company::factory()->create();
        $response = $this->deleteJson("/api/companies/{$company->id}");
        $response->assertStatus(401);
    }

    // Test the destroy company endpoint as an admin
    public function test_destroy_as_admin() 
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        Sanctum::actingAs($user);

        $company = Company::factory()->create(['name' => 'Maslow']);
        
        $response = $this->deleteJson("/api/companies/{$company->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('companies', ['name' => 'Maslow']);
    }

    // Test the destroy company endpoint as an admin of a company
    public function test_destroy_as_admin_company() 
    {
        $company = Company::factory()->create();
        $user = User::factory()->create([
            'role' => UserRole::ADMIN_COMPANY,
        ]);
        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/companies/{$company->id}");

        $response->assertStatus(403);
    }

    // Test the destroy company endpoint as a user_company
    public function test_destroy_as_user_company() 
    {
        $user = User::factory()->create(['role' => UserRole::USER_COMPANY]);
        Sanctum::actingAs($user);

        $company = Company::factory()->create(['name' => 'Maslow']);
        
        $response = $this->deleteJson("/api/companies/{$company->id}");

        $response->assertStatus(403);
    }

    // BILLING BY COMPANY
    // Test billing by company endpoint unauthorized by not being logged in
    public function test_billing_by_company_unauthorized()
    {
        $response = $this->getJson('/api/billing-by-company');
        $response->assertStatus(401);
    }

    // Test billing by company endpoint authorized with admin role
    public function test_billing_by_company_authorized_as_admin()
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        Sanctum::actingAs($user);
        $response = $this->getJson('/api/billing-by-company');
        
        $response->assertStatus(200);
    }


    // Test billing by company endpoint unauthorized with role user_company
    public function test_billing_by_company_unauthorized_as_user_company()
    {
        $user = User::factory()->create(['role' => UserRole::USER_COMPANY]);
        Sanctum::actingAs($user);
        $response = $this->getJson('/api/billing-by-company');
        $response->assertStatus(403);
    }

    // Test billing by company endpoint unauthorized with role admin_company
    public function test_billing_by_company_unauthorized_as_admin_company()
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
