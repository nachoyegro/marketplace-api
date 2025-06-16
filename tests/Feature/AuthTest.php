<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test to ensure that a user can log in with valid credentials.
     */
    public function test_it_allows_user_to_login_with_valid_credentials()
    {
        
        $user = User::factory()->create([
            'email' => 'juan@maslow.hr',
            'password' => bcrypt('secret123'),
        ]);

        
        $response = $this->postJson('/api/login', [
            'email' => 'juan@maslow.hr',
            'password' => 'secret123',
        ]);

        
        $response->assertOk()->assertJsonStructure([
                'message',
                'token',
            ]);
    }

    /**
     * Test to ensure that a user cannot log in with an invalid password. 
     */ 
    public function test_it_rejects_login_with_invalid_password()
    {
        $user = User::factory()->create([
            'email' => 'juan@maslow.hr',
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'juan@maslow.hr',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('email');
    }

    /** 
     * Test to ensure that a user cannot log in with a non-existent email.
     */
    public function test_it_rejects_login_with_nonexistent_email()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'nobody@maslow.hr',
            'password' => 'messi123',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('email');
    }
}