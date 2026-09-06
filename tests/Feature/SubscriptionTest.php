<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_subscriptions_page(): void
    {
        $user = User::factory()->create();
        Subscription::factory()->create([
            'user_id' => $user->id,
            'name' => 'Spotify Premium',
            'amount' => 54990,
            'cycle' => 'monthly',
        ]);

        $response = $this->actingAs($user)->get('/subscriptions');

        $response->assertOk();
        $response->assertSee('Spotify Premium');
        $response->assertSee('Tagihan Rutin');
    }

    public function test_user_can_create_a_subscription(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create(['user_id' => $user->id]);
        $category = Category::factory()->expense()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post('/subscriptions', [
            'name' => 'Netflix 4K',
            'amount' => 186000,
            'cycle' => 'monthly',
            'billing_date' => 15,
            'next_due_date' => now()->addDays(10)->toDateString(),
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
            'status' => 'active',
            'notes' => 'Langganan keluarga',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'name' => 'Netflix 4K',
            'amount' => 186000,
            'cycle' => 'monthly',
            'billing_date' => 15,
        ]);
    }

    public function test_subscription_creation_validation_fails(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/subscriptions', [
            'name' => '',
            'amount' => -50,
            'cycle' => 'invalid_cycle',
        ]);

        $response->assertSessionHasErrors(['name', 'amount', 'cycle', 'billing_date', 'next_due_date']);
    }

    public function test_user_can_update_their_own_subscription(): void
    {
        $user = User::factory()->create();
        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'name' => 'iCloud 50GB',
            'amount' => 15000,
        ]);

        $response = $this->actingAs($user)->put("/subscriptions/{$subscription->id}", [
            'name' => 'iCloud 200GB',
            'amount' => 45000,
            'cycle' => 'monthly',
            'billing_date' => 1,
            'next_due_date' => now()->addDays(5)->toDateString(),
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $subscription->refresh();
        $this->assertEquals('iCloud 200GB', $subscription->name);
        $this->assertEquals(45000, (float) $subscription->amount);
    }

    public function test_user_cannot_update_another_users_subscription(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $subscription = Subscription::factory()->create([
            'user_id' => $user2->id,
            'name' => 'User 2 Sub',
        ]);

        $response = $this->actingAs($user1)->put("/subscriptions/{$subscription->id}", [
            'name' => 'Hacked Sub',
            'amount' => 50000,
            'cycle' => 'monthly',
            'billing_date' => 1,
            'next_due_date' => now()->toDateString(),
            'status' => 'active',
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_pay_subscription_and_transaction_is_recorded(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 1000000,
        ]);
        $category = Category::factory()->expense()->create(['user_id' => $user->id]);

        $initialDue = Carbon::parse('2026-09-10');
        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'name' => 'Wi-Fi IndiHome',
            'amount' => 350000,
            'cycle' => 'monthly',
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
            'next_due_date' => $initialDue->toDateString(),
        ]);

        $response = $this->actingAs($user)->post("/subscriptions/{$subscription->id}/pay", [
            'wallet_id' => $wallet->id,
            'date' => now()->toDateString(),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check wallet balance decreased
        $wallet->refresh();
        $this->assertEquals(650000, (float) $wallet->balance);

        // Check expense transaction created
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'type' => 'expense',
            'amount' => 350000,
            'description' => 'Pembayaran Rutin: Wi-Fi IndiHome',
        ]);

        // Check subscription due date moved forward
        $subscription->refresh();
        $this->assertEquals('2026-10-10', $subscription->next_due_date->format('Y-m-d'));
    }

    public function test_user_cannot_pay_another_users_subscription(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $wallet = Wallet::factory()->create(['user_id' => $user1->id]);
        $subscription = Subscription::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($user1)->post("/subscriptions/{$subscription->id}/pay", [
            'wallet_id' => $wallet->id,
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_delete_their_own_subscription(): void
    {
        $user = User::factory()->create();
        $subscription = Subscription::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete("/subscriptions/{$subscription->id}");

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('subscriptions', ['id' => $subscription->id]);
    }

    public function test_user_cannot_delete_another_users_subscription(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $subscription = Subscription::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($user1)->delete("/subscriptions/{$subscription->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('subscriptions', ['id' => $subscription->id]);
    }
}
