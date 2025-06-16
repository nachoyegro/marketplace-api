<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;
use App\Enums\UserRole;
use App\Models\Brand;

class UserAPITest extends TestCase
{
    use RefreshDatabase;

    // INDEX
    
    // Test the users list endpoint without authentication
    public function test_list_requires_authentication() 
    {
        $response = $this->getJson('/api/users');
        $response->assertStatus(401);
    }


    // Test the users list endpoint as an admin
    public function test_list_as_admin() 
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/users');
        $response->assertStatus(200);
    }

    // Test the users list endpoint as an admin company
    public function test_list_as_admin_company() 
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN_COMPANY]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/users');
        $response->assertStatus(403);
    }

    // Test the users list endpoint as a user
    public function test_list_as_user_company() 
    {
        $user = User::factory()->create(['role' => UserRole::USER_COMPANY]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/users');
        $response->assertStatus(403);
    }

    // Test create endpoint as admin
    public function test_create_user_as_admin()
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        Sanctum::actingAs($user);

        $userData = [
            'name' => 'nachoyegro',
            'email' => 'nachoyegro@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => UserRole::USER_COMPANY,
        ];

        $response = $this->postJson('/api/users', $userData);
        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'name' => 'nachoyegro',
            'email' => 'nachoyegro@gmail.com'
        ]);
    }

    // Test create endpoint as admin company
    public function test_create_user_as_admin_company()
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN_COMPANY]);
        Sanctum::actingAs($user);

        $userData = [
            'name' => 'nachoyegro',
            'email' => 'nachoyegro@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => UserRole::USER_COMPANY,
        ];

        $response = $this->postJson('/api/users', $userData);
        $response->assertStatus(403);
    }

    // Test create endpoint as user company
    public function test_create_user_as_user_company()
    {
        $user = User::factory()->create(['role' => UserRole::USER_COMPANY]);
        Sanctum::actingAs($user);

        $userData = [
            'name' => 'nachoyegro',
            'email' => 'nachoyegro@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => UserRole::USER_COMPANY,
        ];

        $response = $this->postJson('/api/users', $userData);
        $response->assertStatus(403);
    }
}