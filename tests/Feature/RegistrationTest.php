<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_user_can_register_successfully()
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

    public function test_user_can_register_successfully_and_receive_confirmation_email()
    {
        Mail::fake();
        Notification::fake();


        $response = $this->json('POST', 'api/register', [
            'username' => 'TestUser',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agreed_to_terms' => true,
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'User successfully registered.']);

        $this->assertDatabaseHas('users', ['username' => 'TestUser']);
        $user = User::where('email', 'test@example.com')->first();

        Notification::assertSentTo([$user], CustomVerifyEmail::class);


    }

    public function test_user_can_confirm_email_successfully()
    {


        Mail::fake();
        Notification::fake();

        // Trigger registration
        $this->json('POST', 'api/register', [
            'username' => 'TestUser',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agreed_to_terms' => true,
        ]);

        $user = User::where('email', 'test@example.com')->firstOrFail();

        $notification = Notification::sent($user, CustomVerifyEmail::class)->first();
        $verificationUrl = $notification->toMail($user)->viewData['url'];

        parse_str(parse_url($verificationUrl, PHP_URL_QUERY), $queryArray);
        $backendVerificationUrl = $queryArray['verify_url'];

        $decodedBackendVerificationUrl = urldecode($backendVerificationUrl);

        $response = $this->get($decodedBackendVerificationUrl);

        $response->assertStatus(200);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }
    public function test_authenticated_user_cannot_access_registration_form()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('api/register');
        $response->assertStatus(403);
    }

    public function test_registration_fails_if_username_or_email_already_taken()
    {
        $existingUser = User::factory()->create([
            'username' => 'ExistingUser',
            'email' => 'existing@example.com',
        ]);

        $response = $this->json('POST', 'api/register', [
            'username' => 'ExistingUser',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agreed_to_terms' => true,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['username', 'email']);
    }

    public function test_registration_fails_if_username_is_less_than_3_characters()
    {
        $response = $this->json('POST', 'api/register', [
            'username' => 'ab',
            'email' => 'user@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agreed_to_terms' => true,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['username']);
    }



}
