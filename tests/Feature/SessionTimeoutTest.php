<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionTimeoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_with_active_session_can_access_protected_route(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withSession(['last_activity_time' => now()->timestamp - 60]) // 1 minute ago
            ->get('/');

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($user);
    }

    public function test_authenticated_user_is_logged_out_when_session_inactivity_exceeds_timeout(): void
    {
        $user = User::factory()->create();
        config(['session.inactivity_timeout' => 15]);

        // Inactive for 16 minutes (960 seconds)
        $response = $this->actingAs($user)
            ->withSession(['last_activity_time' => now()->timestamp - 960])
            ->get('/');

        $response->assertRedirect('/login?expired=1');
        $response->assertSessionHas('warning');
        $this->assertGuest();
    }

    public function test_ajax_request_receives_unauthorized_json_when_session_expired(): void
    {
        $user = User::factory()->create();
        config(['session.inactivity_timeout' => 15]);

        $response = $this->actingAs($user)
            ->withSession(['last_activity_time' => now()->timestamp - 960])
            ->getJson('/transactions');

        $response->assertStatus(401);
        $response->assertJson([
            'session_expired' => true,
        ]);
        $this->assertGuest();
    }

    public function test_login_page_renders_warning_banner_when_session_expired_query_or_flash_is_present(): void
    {
        $response = $this->get('/login?expired=1');

        $response->assertStatus(200);
        $response->assertSee('Sesi Anda telah berakhir');
    }

    public function test_authenticated_user_visiting_login_with_expired_flag_is_logged_out(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/login?expired=1');

        $response->assertStatus(200);
        $this->assertGuest();
        $response->assertSee('Sesi Anda telah berakhir');
    }

    public function test_logout_via_get_with_expired_flag_redirects_to_login_with_warning(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/logout?expired=1');

        $response->assertRedirect('/login?expired=1');
        $response->assertSessionHas('warning');
        $this->assertGuest();
    }

    public function test_keepalive_endpoint_refreshes_last_activity_time(): void
    {
        $user = User::factory()->create();
        $oldTimestamp = now()->timestamp - 120; // 2 minutes ago

        $response = $this->actingAs($user)
            ->withSession(['last_activity_time' => $oldTimestamp])
            ->postJson(route('session.keepalive'), ['active' => true]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'active',
        ]);

        $this->assertGreaterThan($oldTimestamp, session('last_activity_time'));
    }

    public function test_session_expires_after_5_minutes_of_inactivity(): void
    {
        $user = User::factory()->create();
        config(['session.inactivity_timeout' => 5]);

        // 301 seconds ago (> 5 minutes)
        $response = $this->actingAs($user)
            ->withSession(['last_activity_time' => now()->timestamp - 301])
            ->get('/');

        $response->assertRedirect('/login?expired=1');
        $this->assertGuest();
    }
}
