<?php

namespace Tests\Feature;

use App\Models\BiometricCredential;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaceIdTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_challenge_generates_challenge_and_detects_registered_state(): void
    {
        // 1. Without registered credentials
        $response = $this->postJson('/face-id/login/challenge');

        $response->assertStatus(200);
        $response->assertJsonStructure(['challenge', 'registered', 'rpId', 'credentials']);
        $this->assertFalse($response->json('registered'));

        // 2. With registered credential
        $user = User::factory()->create();
        BiometricCredential::create([
            'user_id' => $user->id,
            'credential_id' => 'ios_faceid_cred_abc123',
            'device_name' => 'iPhone 15 Pro',
        ]);

        $response2 = $this->postJson('/face-id/login/challenge');
        $response2->assertStatus(200);
        $this->assertTrue($response2->json('registered'));
        $this->assertContains('ios_faceid_cred_abc123', $response2->json('credentials'));
    }

    public function test_login_verify_fails_if_session_challenge_is_missing(): void
    {
        $response = $this->postJson('/face-id/login/verify', [
            'credential_id' => 'sample_cred',
        ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    public function test_login_verify_authenticates_user_with_valid_challenge_and_credential(): void
    {
        $user = User::factory()->create([
            'name' => 'Gusti Swandana',
            'email' => 'gustiswandana@swanflow.com',
        ]);

        $cred = BiometricCredential::create([
            'user_id' => $user->id,
            'credential_id' => 'apple_faceid_token_999',
            'device_name' => 'iPhone 15',
        ]);

        // Get challenge first to set session
        $this->postJson('/face-id/login/challenge');

        $response = $this->postJson('/face-id/login/verify', [
            'credential_id' => 'apple_faceid_token_999',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'redirect' => route('dashboard'),
        ]);

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($cred->fresh()->last_used_at);
    }

    public function test_register_challenge_requires_authentication(): void
    {
        $response = $this->postJson('/face-id/register/challenge');

        $response->assertStatus(401);
    }

    public function test_register_challenge_returns_user_and_rp_data(): void
    {
        $user = User::factory()->create([
            'name' => 'Gusti Swandana',
            'email' => 'gustiswandana@swanflow.com',
        ]);

        $response = $this->actingAs($user)->postJson('/face-id/register/challenge');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'challenge',
            'rp' => ['name', 'id'],
            'user' => ['id', 'name', 'displayName'],
        ]);
        $this->assertSame('Gusti Swandana', $response->json('user.displayName'));
    }

    public function test_register_verify_stores_biometric_credential(): void
    {
        $user = User::factory()->create([
            'name' => 'Gusti Swandana',
        ]);

        // Request challenge to set session
        $this->actingAs($user)->postJson('/face-id/register/challenge');

        $response = $this->actingAs($user)->postJson('/face-id/register/verify', [
            'credential_id' => 'new_iphone_faceid_credential_key',
            'device_name' => 'iPhone 15 Pro Max',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('biometric_credentials', [
            'user_id' => $user->id,
            'credential_id' => 'new_iphone_faceid_credential_key',
            'device_name' => 'iPhone 15 Pro Max',
        ]);
    }

    public function test_user_can_delete_biometric_credential(): void
    {
        $user = User::factory()->create();
        $cred = BiometricCredential::create([
            'user_id' => $user->id,
            'credential_id' => 'cred_to_delete',
        ]);

        $response = $this->actingAs($user)->delete("/face-id/{$cred->id}");

        $response->assertRedirect('/profile');
        $this->assertDatabaseMissing('biometric_credentials', ['id' => $cred->id]);
    }

    public function test_user_cannot_delete_another_users_biometric_credential(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $credB = BiometricCredential::create([
            'user_id' => $userB->id,
            'credential_id' => 'user_b_cred',
        ]);

        $response = $this->actingAs($userA)->delete("/face-id/{$credB->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('biometric_credentials', ['id' => $credB->id]);
    }

    public function test_login_verify_succeeds_with_base64url_client_data_challenge(): void
    {
        $user = User::factory()->create();
        BiometricCredential::create([
            'user_id' => $user->id,
            'credential_id' => 'webauthn_test_cred',
        ]);

        $challengeRes = $this->postJson('/face-id/login/challenge');
        $rawChallengeHex = $challengeRes->json('challenge');
        $rawChallengeBin = hex2bin($rawChallengeHex);
        $base64urlChallenge = rtrim(strtr(base64_encode($rawChallengeBin), '+/', '-_'), '=');

        $clientDataJson = base64_encode(json_encode([
            'type' => 'webauthn.get',
            'challenge' => $base64urlChallenge,
            'origin' => 'http://localhost',
        ]));

        $response = $this->postJson('/face-id/login/verify', [
            'credential_id' => 'webauthn_test_cred',
            'client_data_json' => $clientDataJson,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertAuthenticatedAs($user);
    }
}
