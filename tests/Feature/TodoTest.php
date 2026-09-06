<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_todos(): void
    {
        $response = $this->get('/todos');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_todos_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/todos');

        $response->assertOk();
        $response->assertViewIs('todos.index');
    }

    public function test_todos_page_shows_today_tab_by_default(): void
    {
        $user = User::factory()->create();
        Todo::factory()->dueToday()->create(['user_id' => $user->id, 'title' => 'Tugas Hari Ini']);

        $response = $this->actingAs($user)->get('/todos');

        $response->assertOk();
        $response->assertViewHas('tab', 'today');
        $response->assertSee('Tugas Hari Ini');
    }

    public function test_user_can_create_todo(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/todos', [
            'title' => 'Beli susu',
            'priority' => 'medium',
            'category' => 'belanja',
            'due_date' => today()->toDateString(),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Aktivitas berhasil ditambahkan!');
        $this->assertDatabaseHas('todos', ['title' => 'Beli susu', 'user_id' => $user->id]);
    }

    public function test_user_can_toggle_todo_completion(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->pending()->create(['user_id' => $user->id]);

        $this->actingAs($user)->patch("/todos/{$todo->id}/toggle");

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'is_completed' => true,
        ]);
        $this->assertNotNull($todo->fresh()->completed_at);
    }

    public function test_toggle_completed_todo_marks_it_pending(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->completed()->create(['user_id' => $user->id]);

        $this->actingAs($user)->patch("/todos/{$todo->id}/toggle");

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'is_completed' => false,
        ]);
        $this->assertNull($todo->fresh()->completed_at);
    }

    public function test_user_can_update_todo(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put("/todos/{$todo->id}", [
            'title' => 'Judul Diperbarui',
            'priority' => 'high',
            'category' => 'kerja',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Aktivitas berhasil diperbarui!');
        $this->assertDatabaseHas('todos', ['id' => $todo->id, 'title' => 'Judul Diperbarui', 'priority' => 'high']);
    }

    public function test_user_can_delete_todo(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete("/todos/{$todo->id}");

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Aktivitas berhasil dihapus!');
        $this->assertDatabaseMissing('todos', ['id' => $todo->id]);
    }

    public function test_user_cannot_modify_another_users_todo(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)->put("/todos/{$todo->id}", [
            'title' => 'Coba Hack',
            'priority' => 'low',
        ])->assertForbidden();

        $this->actingAs($other)->delete("/todos/{$todo->id}")->assertForbidden();
        $this->actingAs($other)->patch("/todos/{$todo->id}/toggle")->assertForbidden();
    }

    public function test_dashboard_shows_today_todos_widget(): void
    {
        $user = User::factory()->create();
        Todo::factory()->dueToday()->high()->create(['user_id' => $user->id, 'title' => 'Rapat Penting']);

        $response = $this->actingAs($user)->get('/');

        $response->assertOk();
        $response->assertSee('Aktivitas Hari Ini');
        $response->assertSee('Rapat Penting');
    }
}
