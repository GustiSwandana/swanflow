<?php

namespace Tests\Feature;

use App\Models\OrderSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_pin_backdoor_is_removed(): void
    {
        $user = User::factory()->create(['pin' => '998877']);
        OrderSetting::create([
            'user_id' => $user->id,
            'studio_name' => 'Secure Studio',
            'admin_pin' => '998877',
        ]);

        // Attempting with old hardcoded default 123456 MUST fail with 401
        $response = $this->postJson('/api/admin/login', ['pin' => '123456']);
        $response->assertStatus(401);

        // Attempting with the actual configured PIN succeeds
        $successResponse = $this->postJson('/api/admin/login', ['pin' => '998877']);
        $successResponse->assertStatus(200);
    }

    public function test_maintenance_endpoints_forbidden_without_auth_or_key(): void
    {
        $response = $this->get('/swanflow-clear-view-cache');
        $response->assertStatus(403);

        $logResponse = $this->get('/swanflow-latest-log');
        $logResponse->assertStatus(403);
    }

    public function test_maintenance_endpoints_allowed_with_deploy_key(): void
    {
        putenv('SWANFLOW_DEPLOY_KEY=test-secret-deploy-key-12345');
        $_ENV['SWANFLOW_DEPLOY_KEY'] = 'test-secret-deploy-key-12345';

        $response = $this->withHeaders([
            'X-SwanFlow-Key' => 'test-secret-deploy-key-12345',
        ])->get('/swanflow-clear-view-cache');

        $response->assertStatus(200);

        // Wrong key should still be 403
        $forbidden = $this->withHeaders([
            'X-SwanFlow-Key' => 'wrong-key',
        ])->get('/swanflow-clear-view-cache');

        $forbidden->assertStatus(403);

        putenv('SWANFLOW_DEPLOY_KEY');
        unset($_ENV['SWANFLOW_DEPLOY_KEY']);
    }

    public function test_maintenance_endpoints_allowed_for_authenticated_users(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/swanflow-clear-view-cache');
        $response->assertStatus(200);
    }
}
