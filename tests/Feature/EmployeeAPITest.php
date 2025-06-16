<?php

namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;
use App\Models\Company;
use App\Models\Employee;
use App\Enums\UserRole;

class EmployeeApiTest extends TestCase
{
    use RefreshDatabase;

    // VIEW

    // Test the employee view endpoint while being unauthenticated
    public function test_view_requires_authentication()
    {
        $employee = Employee::factory()->create();
        $response = $this->getJson("/api/employees/{$employee->id}");
        $response->assertStatus(401);
    }

    // Test the employee view endpoint as admin
    public function test_employee_view_as_admin()
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        Sanctum::actingAs($user);

        $employee = Employee::factory()->create();
        $response = $this->getJson("/api/employees/{$employee->id}");
        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $employee->id]);
    }

    // Test the employee view endpoint as admin_company of own company
    public function test_own_employee_view_as_admin_company()
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN_COMPANY]);
        Sanctum::actingAs($user);

        $company = Company::factory()->create();
        $employee_admin = Employee::factory()->create(['company_id' => $company->id, 'user_id' => $user->id]);

        $employee = Employee::factory()->create(['company_id' => $company->id]);

        $response = $this->getJson("/api/employees/{$employee->id}");
        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $employee->id]);
    }

    // Test the employee view endpoint as admin_company of other company
    public function test_admin_employee_cannot_view_other_company_employee()
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN_COMPANY]);
        Sanctum::actingAs($user);

        // Company 1 with actual user
        $company1 = Company::factory()->create();
        $employee_admin = Employee::factory()->create(['company_id' => $company1->id, 'user_id' => $user->id]);

        // Company 2 with different employee
        $company2 = Company::factory()->create();
        $employee = Employee::factory()->create(['company_id' => $company2->id]);

        $response = $this->getJson("/api/employees/{$employee->id}");
        $response->assertStatus(403);
    }

    // Test the employee view endpoint as user_company
    public function test_employee_view_as_user_company()
    {
        $user = User::factory()->create(['role' => UserRole::USER_COMPANY]);
        Sanctum::actingAs($user);

        $employee = Employee::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson("/api/employees/{$employee->id}");
        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $employee->id]);
    }

    // INDEX

    // Test the employees index endpoint while being unauthenticated
    public function test_index_requires_authentication()
    {
        $response = $this->getJson('/api/employees');
        $response->assertStatus(401);
    }

    // Test the employees index endpoint as admin
    public function test_employees_index_as_admin()
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/employees');
        $response->assertStatus(200);
        
    }

    // Test the employees index endpoint as admin_company
    public function test_employees_index_as_admin_company()
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN_COMPANY]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/employees');
        $response->assertStatus(200);

    }

    // Test the employees index endpoint as user_company
    public function test_employees_index_as_user_company()
    {
        $user = User::factory()->create(['role' => UserRole::USER_COMPANY]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/employees');
        $response->assertStatus(403);
    }

    // Test the employees index endpoint as user_company with a specific company
    public function test_admin_company_sees_only_own_company_employees()
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        // User with ADMIN_COMPANY role for company1
        $adminCompanyUser = User::factory()->create([
            'role' => UserRole::ADMIN_COMPANY,
        ]);

        // Employee for company1
        $employeeCompany1 = Employee::factory()->create([
            'company_id' => $company1->id,
            'user_id' => $adminCompanyUser->id, 
        ]);

        // Employee for company2
        $employeeCompany2 = Employee::factory()->create([
            'company_id' => $company2->id,
        ]);

        Sanctum::actingAs($adminCompanyUser);

        $response = $this->getJson('/api/employees');

        $response->assertStatus(200);
        // Check that the response contains only employees from company1
        $response->assertJsonMissing(['id' => $employeeCompany2->id]);
        $response->assertJsonFragment(['id' => $employeeCompany1->id]);
    }

    // STORE

    // Test for unauthenticated user trying to store an employee
    public function test_store_requires_authentication()
    {
        $response = $this->postJson('/api/employees', []);
        $response->assertStatus(401);
    }

    // Test for user_company trying to store an employee
    public function test_user_company_cannot_store_employee()
    {
        $user = User::factory()->create(['role' => UserRole::USER_COMPANY]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/employees', []);
        $response->assertStatus(403);
    }

    // Test for admin_company trying to store an employee
    public function test_admin_company_cannot_store_employee()
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN_COMPANY]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/employees', []);
        $response->assertStatus(403);
    }

    // Test for admin trying to store an employee
    public function test_admin_can_store_employee()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        Sanctum::actingAs($admin);

        $company = Company::factory()->create();

        $employee = [
            'user_id' => User::factory()->create()->id,
            'company_id' => 1,
            'first_name' => 'Juan Ignacio',
            'last_name' => 'Yegro',
            'birth_date' => '1991-02-07',
            'credits' => 1000,
        ];
        $response = $this->postJson('/api/employees', $employee);
        $response->assertStatus(201);

        $this->assertDatabaseHas('employees', [
            'first_name' => 'Juan Ignacio',
            'last_name' => 'Yegro',
            'company_id' => $company->id,
        ]);
    }

    // UPDATE

    // Test for unauthenticated user trying to update an employee
    public function test_update_requires_authentication()
    {
        $employee = Employee::factory()->create();
        $response = $this->putJson("/api/employees/{$employee->id}", []);
        $response->assertStatus(401);
    }

    // Test for user_company trying to update an employee
    public function test_user_company_cannot_update_employee()
    {
        $user = User::factory()->create(['role' => UserRole::USER_COMPANY]);
        Sanctum::actingAs($user);


        $employee = Employee::factory()->create();
        $response = $this->putJson("/api/employees/{$employee->id}", []);
        $response->assertStatus(403);
    }

    // Test for admin_company trying to update an employee
    public function test_admin_company_cannot_update_employee()
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN_COMPANY]);
        Sanctum::actingAs($user);

        $employee = Employee::factory()->create();
        $response = $this->putJson("/api/employees/{$employee->id}", []);
        $response->assertStatus(403);
    }

    // Test for admin trying to update an employee
    public function test_admin_can_update_employee()
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        Sanctum::actingAs($user);

        $employee = Employee::factory()->create([
            'first_name' => 'Jorge',
        ]);

        $response = $this->putJson("/api/employees/{$employee->id}", [
            'first_name' => 'Juan',
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'first_name' => 'Juan',
        ]);

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'first_name' => 'Juan',
        ]);
    }

    // DESTROY

    // Test for unauthenticated user trying to destroy an employee
    public function test_destroy_requires_authentication()
    {
        $employee = Employee::factory()->create();
        $response = $this->deleteJson("/api/employees/{$employee->id}", []);
        $response->assertStatus(401);
    }

    // Test for user_company trying to destroy an employee
    public function test_user_company_cannot_destroy_employee()
    {
        $user = User::factory()->create(['role' => UserRole::USER_COMPANY]);
        Sanctum::actingAs($user);


        $employee = Employee::factory()->create();
        $response = $this->deleteJson("/api/employees/{$employee->id}", []);
        $response->assertStatus(403);
    }

    // Test for admin_company trying to destroy an employee
    public function test_admin_company_cannot_destroy_employee()
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN_COMPANY]);
        Sanctum::actingAs($user);

        $employee = Employee::factory()->create();
        $response = $this->deleteJson("/api/employees/{$employee->id}", []);
        $response->assertStatus(403);
    }

    // Test for admin trying to destroy an employee
    public function test_admin_can_destroy_employee()
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        Sanctum::actingAs($user);

        $employee = Employee::factory()->create();

        $response = $this->deleteJson("/api/employees/{$employee->id}");
        $response->assertStatus(204);

        $this->assertDatabaseMissing('employees', [
            'id' => $employee->id,
        ]);
    }

}