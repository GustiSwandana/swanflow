<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PinAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_renders_banking_lock_screen_with_pin_dots_and_keypad(): void
    {
        $user = User::factory()->create([
            'name' => 'Gusti Swandana',
            'email' => 'gustiswandana@swanflow.com',
            'pin' => Hash::make('123456'),
        ]);

        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Gusti Swandana');
        $response->assertSee('pin-dots-container');
        $response->assertSee('Face ID');
        $response->assertSee('Masuk dengan Email');
    }

    public function test_user_can_verify_and_login_with_valid_pin_via_ajax(): void
    {
        $user = User::factory()->create([
            'email' => 'gustiswandana@swanflow.com',
            'pin' => Hash::make('654321'),
        ]);

        $response = $this->postJson('/auth/pin/verify', [
            'pin' => '654321',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $this->assertNotEmpty($response->json('redirect'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_pin_verification_fails_with_invalid_pin(): void
    {
        $user = User::factory()->create([
            'email' => 'gustiswandana@swanflow.com',
            'pin' => Hash::make('654321'),
        ]);

        $response = $this->postJson('/auth/pin/verify', [
            'pin' => '000000',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'PIN yang Anda masukkan salah.',
        ]);
        $this->assertGuest();
    }

    public function test_pin_verification_rejects_non_six_digit_or_non_numeric_pin(): void
    {
        User::factory()->create([
            'email' => 'gustiswandana@swanflow.com',
            'pin' => Hash::make('654321'),
        ]);

        $responseTooShort = $this->postJson('/auth/pin/verify', ['pin' => '123']);
        $responseTooShort->assertStatus(422);

        $responseTooLong = $this->postJson('/auth/pin/verify', ['pin' => '1234567']);
        $responseTooLong->assertStatus(422);

        $responseNonNumeric = $this->postJson('/auth/pin/verify', ['pin' => 'abcdef']);
        $responseNonNumeric->assertStatus(422);

        $this->assertGuest();
    }

    public function test_authenticated_user_can_update_pin_in_profile(): void
    {
        $user = User::factory()->create([
            'pin' => Hash::make('123456'),
        ]);

        $response = $this->actingAs($user)->put('/profile/pin', [
            'current_pin' => '123456',
            'pin' => '987654',
            'pin_confirmation' => '987654',
        ]);

        $response->assertRedirect('/profile');
        $response->assertSessionHas('success', 'PIN Keamanan 6-Digit Anda berhasil diperbarui!');

        $user->refresh();
        $this->assertTrue($user->verifyPin('987654'));
    }

    public function test_user_cannot_update_pin_with_wrong_current_pin(): void
    {
        $user = User::factory()->create([
            'pin' => Hash::make('123456'),
        ]);

        $response = $this->actingAs($user)->put('/profile/pin', [
            'current_pin' => '000000',
            'pin' => '987654',
            'pin_confirmation' => '987654',
        ]);

        $response->assertSessionHasErrors('current_pin');

        $user->refresh();
        $this->assertTrue($user->verifyPin('123456'));
    }

    public function test_user_cannot_update_pin_with_mismatched_confirmation(): void
    {
        $user = User::factory()->create([
            'pin' => Hash::make('123456'),
        ]);

        $response = $this->actingAs($user)->put('/profile/pin', [
            'current_pin' => '123456',
            'pin' => '987654',
            'pin_confirmation' => '111111',
        ]);

        $response->assertSessionHasErrors('pin');

        $user->refresh();
        $this->assertTrue($user->verifyPin('123456'));
    }
}
