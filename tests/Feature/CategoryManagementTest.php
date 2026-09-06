<?php

namespace Tests\Feature;

use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_categories_page(): void
    {
        $user = User::factory()->create();
        Category::factory()->create([
            'user_id' => $user->id,
            'name' => 'Langganan Streaming',
            'type' => TransactionType::Expense,
        ]);

        $response = $this->actingAs($user)->get('/categories');

        $response->assertOk();
        $response->assertSee('Langganan Streaming');
        $response->assertSee('Kategori Transaksi');
    }

    public function test_user_can_create_a_category(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/categories', [
            'name' => 'Kopi & Nongkrong',
            'type' => 'expense',
            'color' => '#F59E0B',
            'icon' => 'coffee',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'user_id' => $user->id,
            'name' => 'Kopi & Nongkrong',
            'type' => 'expense',
            'color' => '#F59E0B',
            'icon' => 'coffee',
        ]);
    }

    public function test_create_category_validation_fails_with_invalid_data(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/categories', [
            'name' => '',
            'type' => 'invalid_type',
        ]);

        $response->assertSessionHasErrors(['name', 'type']);
    }

    public function test_user_can_update_their_own_category(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create([
            'user_id' => $user->id,
            'name' => 'Kategori Lama',
            'type' => TransactionType::Expense,
        ]);

        $response = $this->actingAs($user)->put("/categories/{$category->id}", [
            'name' => 'Kategori Baru',
            'type' => 'expense',
            'color' => '#10B981',
            'icon' => 'tag',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $category->refresh();
        $this->assertEquals('Kategori Baru', $category->name);
        $this->assertEquals('#10B981', $category->color);
    }

    public function test_user_cannot_update_system_category(): void
    {
        $user = User::factory()->create();
        $systemCategory = Category::factory()->create([
            'user_id' => null,
            'name' => 'System Category',
            'type' => TransactionType::Expense,
        ]);

        $response = $this->actingAs($user)->put("/categories/{$systemCategory->id}", [
            'name' => 'Modified Name',
            'type' => 'expense',
        ]);

        $response->assertStatus(403);
    }

    public function test_user_cannot_update_another_users_category(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $category = Category::factory()->create([
            'user_id' => $user2->id,
            'name' => 'User 2 Category',
            'type' => TransactionType::Expense,
        ]);

        $response = $this->actingAs($user1)->put("/categories/{$category->id}", [
            'name' => 'Hacked Name',
            'type' => 'expense',
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_delete_their_own_category(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create([
            'user_id' => $user->id,
            'name' => 'Mau Dihapus',
            'type' => TransactionType::Expense,
        ]);

        $response = $this->actingAs($user)->delete("/categories/{$category->id}");

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_user_cannot_delete_system_category(): void
    {
        $user = User::factory()->create();
        $systemCategory = Category::factory()->create([
            'user_id' => null,
            'name' => 'Default Sistem',
            'type' => TransactionType::Expense,
        ]);

        $response = $this->actingAs($user)->delete("/categories/{$systemCategory->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('categories', ['id' => $systemCategory->id]);
    }

    public function test_user_cannot_delete_another_users_category(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $category = Category::factory()->create([
            'user_id' => $user2->id,
            'name' => 'User 2 Category',
        ]);

        $response = $this->actingAs($user1)->delete("/categories/{$category->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }
}
