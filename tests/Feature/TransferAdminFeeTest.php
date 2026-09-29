<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransferAdminFeeTest extends TestCase
{
    use RefreshDatabase;

    public function test_transfer_with_admin_fee_borne_by_source_wallet(): void
    {
        $user = User::factory()->create();
        $sourceWallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'name' => 'Bank Mandiri',
            'balance' => 100000,
        ]);
        $targetWallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'name' => 'GoPay',
            'balance' => 0,
        ]);

        // Transfer 35,000 with 1,200 admin fee borne by source (Bank)
        $response = $this->actingAs($user)->post('/transactions', [
            'type' => 'transfer',
            'wallet_id' => $sourceWallet->id,
            'target_wallet_id' => $targetWallet->id,
            'amount' => 35000,
            'admin_fee' => 1200,
            'fee_payer' => 'source',
            'date' => now()->toDateString(),
            'description' => 'Top Up Gopay',
        ]);

        $response->assertSessionHas('success');

        // Source decreases by 35,000 + 1,200 = 36,200 -> Remaining 100,000 - 36,200 = 63,800
        $this->assertEquals(63800, (float) $sourceWallet->fresh()->balance);
        // Target increases by 35,000 -> 35,000
        $this->assertEquals(35000, (float) $targetWallet->fresh()->balance);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'amount' => 35000,
            'admin_fee' => 1200,
            'fee_payer' => 'source',
            'type' => 'transfer',
        ]);
    }

    public function test_transfer_with_admin_fee_borne_by_destination_wallet(): void
    {
        $user = User::factory()->create();
        $sourceWallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'name' => 'Bank BCA',
            'balance' => 100000,
        ]);
        $targetWallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'name' => 'OVO',
            'balance' => 0,
        ]);

        // Transfer 35,000 with 1,200 admin fee borne by destination (OVO)
        $response = $this->actingAs($user)->post('/transactions', [
            'type' => 'transfer',
            'wallet_id' => $sourceWallet->id,
            'target_wallet_id' => $targetWallet->id,
            'amount' => 35000,
            'admin_fee' => 1200,
            'fee_payer' => 'destination',
            'date' => now()->toDateString(),
            'description' => 'Transfer to OVO',
        ]);

        $response->assertSessionHas('success');

        // Source decreases by 35,000 -> Remaining 100,000 - 35,000 = 65,000
        $this->assertEquals(65000, (float) $sourceWallet->fresh()->balance);
        // Target increases by 35,000 - 1,200 = 33,800
        $this->assertEquals(33800, (float) $targetWallet->fresh()->balance);
    }

    public function test_deleting_transfer_reverts_admin_fee_correctly(): void
    {
        $user = User::factory()->create();
        $sourceWallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 100000,
        ]);
        $targetWallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 50000,
        ]);

        $this->actingAs($user)->post('/transactions', [
            'type' => 'transfer',
            'wallet_id' => $sourceWallet->id,
            'target_wallet_id' => $targetWallet->id,
            'amount' => 20000,
            'admin_fee' => 2500,
            'fee_payer' => 'source',
            'date' => now()->toDateString(),
        ]);

        $tx = Transaction::where('user_id', $user->id)->first();
        $this->assertNotNull($tx);

        // Delete the transaction
        $delResponse = $this->actingAs($user)->delete("/transactions/{$tx->id}");
        $delResponse->assertSessionHas('success');

        // Balances should be back to original
        $this->assertEquals(100000, (float) $sourceWallet->fresh()->balance);
        $this->assertEquals(50000, (float) $targetWallet->fresh()->balance);
    }
}
