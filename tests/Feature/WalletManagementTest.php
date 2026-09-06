<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_wallets_page(): void
    {
        $user = User::factory()->create();
        Wallet::factory()->create([
            'user_id' => $user->id,
            'name' => 'BCA Utama',
            'type' => 'bank',
            'balance' => 5000000,
        ]);

        $response = $this->actingAs($user)->get('/wallets');

        $response->assertOk();
        $response->assertSee('BCA Utama');
        $response->assertSee('Total Akumulasi Saldo');
    }

    public function test_user_can_create_a_wallet(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/wallets', [
            'name' => 'GoPay',
            'type' => 'ewallet',
            'balance' => 250000,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('wallets', [
            'user_id' => $user->id,
            'name' => 'GoPay',
            'type' => 'ewallet',
            'balance' => 250000,
        ]);
    }

    public function test_create_wallet_validation_fails_with_invalid_data(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/wallets', [
            'name' => '',
            'type' => 'invalid_type',
            'balance' => -100,
        ]);

        $response->assertSessionHasErrors(['name', 'type', 'balance']);
    }

    public function test_user_can_update_their_own_wallet(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'name' => 'Mandiri Lama',
            'type' => 'bank',
            'balance' => 1000000,
        ]);

        $response = $this->actingAs($user)->put("/wallets/{$wallet->id}", [
            'name' => 'Mandiri Tabungan',
            'type' => 'bank',
            'balance' => 1500000,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $wallet->refresh();
        $this->assertEquals('Mandiri Tabungan', $wallet->name);
        $this->assertEquals(1500000, (float) $wallet->balance);
    }

    public function test_user_cannot_update_another_users_wallet(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $wallet = Wallet::factory()->create([
            'user_id' => $user2->id,
            'name' => 'Dompet User 2',
        ]);

        $response = $this->actingAs($user1)->put("/wallets/{$wallet->id}", [
            'name' => 'Hacked Name',
            'type' => 'bank',
            'balance' => 999999,
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_delete_wallet_when_they_have_multiple_wallets(): void
    {
        $user = User::factory()->create();
        $wallet1 = Wallet::factory()->create(['user_id' => $user->id]);
        $wallet2 = Wallet::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete("/wallets/{$wallet1->id}");

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('wallets', ['id' => $wallet1->id]);
    }

    public function test_user_cannot_delete_their_last_remaining_wallet(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete("/wallets/{$wallet->id}");

        $response->assertRedirect();
        $response->assertSessionHasErrors(['wallet']);
        $this->assertDatabaseHas('wallets', ['id' => $wallet->id]);
    }

    public function test_user_cannot_delete_another_users_wallet(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $wallet = Wallet::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($user1)->delete("/wallets/{$wallet->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('wallets', ['id' => $wallet->id]);
    }
}
