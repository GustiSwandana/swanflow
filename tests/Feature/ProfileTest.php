<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_can_be_rendered(): void
    {
        $user = User::factory()->create([
            'name' => 'Gusti Swandana',
            'email' => 'gustiswandana@swanflow.com',
        ]);

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
        $response->assertSee('Gusti Swandana');
        $response->assertSee('gustiswandana@swanflow.com');
        $response->assertSee('Informasi Pribadi');
        $response->assertSee('Keamanan Akun');
        $response->assertSee('Keluar dari Akun');
    }

    public function test_user_can_update_name_and_email(): void
    {
        $user = User::factory()->create([
            'name' => 'Gusti Swandana',
            'email' => 'gustiswandana@swanflow.com',
        ]);

        $response = $this->actingAs($user)->put('/profile', [
            'name' => 'Gusti Swandana Updated',
            'email' => 'gusti.updated@swanflow.com',
        ]);

        $response->assertRedirect('/profile');
        $response->assertSessionHas('success', 'Profil Anda berhasil diperbarui!');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Gusti Swandana Updated',
            'email' => 'gusti.updated@swanflow.com',
        ]);
    }

    public function test_user_can_update_password_with_valid_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword123'),
        ]);

        $response = $this->actingAs($user)->put('/profile/password', [
            'current_password' => 'oldpassword123',
            'password' => 'newpassword456',
            'password_confirmation' => 'newpassword456',
        ]);

        $response->assertRedirect('/profile');
        $response->assertSessionHas('success', 'Password Anda berhasil diperbarui!');

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword456', $user->password));
    }

    public function test_password_update_fails_with_invalid_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword123'),
        ]);

        $response = $this->actingAs($user)->put('/profile/password', [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword456',
            'password_confirmation' => 'newpassword456',
        ]);

        $response->assertSessionHasErrors('current_password');

        $user->refresh();
        $this->assertTrue(Hash::check('oldpassword123', $user->password));
    }
}
