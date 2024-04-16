<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register_successfully()
    {
        $response = $this->json('POST', 'api/register', [
            'username' => 'TestUser',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agreed_to_terms' => true,
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'User successfully registered.'
            ]);

        $this->assertDatabaseHas('users', [
            'username' => 'TestUser',
            'email' => 'test@example.com',
        ]);
    }
    public function test_registration_should_give_errors_if_input_is_not_provided()
    {
        $response = $this->json('POST', 'api/register', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['username', 'email', 'password', 'agreed_to_terms']);
    }

    public function test_registration_should_give_email_errors_if_email_is_not_provided()
    {
        $response = $this->json('POST', 'api/register', [
            'username' => 'TestUser',
            'email' => 'not-an-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agreed_to_terms' => true,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_registration_should_give_password_errors_if_password_is_not_provided()
    {
        $response = $this->json('POST', 'api/register', [
            'username' => 'TestUser',
            'email' => 'not-an-email',
            'agreed_to_terms' => true,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }

    public function test_registration_should_give_email_errors_when_email_is_not_correct()
    {
        $response = $this->json('POST', 'api/register', [
            'username' => 'TestUser',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agreed_to_terms' => true,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }


}
