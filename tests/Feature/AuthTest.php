<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('SwanFlow');
        $response->assertSee('Face ID');
        $response->assertDontSee('Masuk sebagai Gusti Swandana');
    }

    public function test_quick_login_endpoint_has_been_removed_and_returns_not_found(): void
    {
        $response = $this->post('/login/quick');

        $response->assertStatus(404);
        $this->assertGuest();
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'gusti@example.com',
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'gusti@example.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'email' => 'gusti@example.com',
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'gusti@example.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/profile');
        $response->assertRedirect('/login');

        $response2 = $this->get('/transactions');
        $response2->assertRedirect('/login');

        $response3 = $this->get('/reports');
        $response3->assertRedirect('/login');
    }

    public function test_assets_and_routes_render_correct_https_url_behind_proxy(): void
    {
        $response = $this->withHeaders([
            'Host' => 'my-tunnel.trycloudflare.com',
            'X-Forwarded-Proto' => 'https',
            'X-Forwarded-Host' => 'my-tunnel.trycloudflare.com',
        ])->get('/login');

        $response->assertStatus(200);
        $content = $response->getContent();
        $this->assertStringContainsString('https://my-tunnel.trycloudflare.com/build/assets/app', $content);
        $this->assertStringNotContainsString('http://localhost:8000/build/assets/app', $content);
    }
}
