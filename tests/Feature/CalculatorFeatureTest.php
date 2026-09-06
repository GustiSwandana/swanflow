<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalculatorFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_calculator(): void
    {
        $response = $this->get(route('calculator.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_calculator_page(): void
    {
        $user = User::factory()->create(['name' => 'Gusti Swandana']);
        $walletA = Wallet::factory()->create(['user_id' => $user->id, 'name' => 'BCA Utama', 'balance' => 7500000]);
        $walletB = Wallet::factory()->create(['user_id' => $user->id, 'name' => 'Dompet Cash', 'balance' => 500000]);

        $response = $this->actingAs($user)->get(route('calculator.index'));

        $response->assertStatus(200);
        $response->assertSee('Kalkulator & Simulasi');
        $response->assertSee('Simulasi Skenario');
        $response->assertSee('Hitung Patungan');
        $response->assertSee('BCA Utama');
        $response->assertSee('Dompet Cash');
        $response->assertSee('8.000.000'); // Sum of balances formatted
    }

    public function test_dashboard_displays_shortcut_to_calculator(): void
    {
        $user = User::factory()->create(['name' => 'Gusti Swandana']);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee(route('calculator.index'));
        $response->assertSee('Kalkulator');
    }
}
