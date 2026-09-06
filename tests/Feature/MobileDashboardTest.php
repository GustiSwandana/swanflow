<?php

namespace Tests\Feature;

use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\StoredFile;
use App\Models\Transaction;
use App\Models\UploadLink;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileDashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test mobile dashboard page renders with expected mobile layout elements.
     */
    public function test_dashboard_page_renders_with_mobile_layout_and_components(): void
    {
        $user = User::factory()->create(['name' => 'Gusti Swandana']);
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'name' => 'Bank BCA',
            'balance' => 14850000.00,
        ]);
        $cat = Category::factory()->expense()->create([
            'user_id' => $user->id,
            'name' => 'Makanan',
        ]);
        Transaction::factory()->expense()->create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'category_id' => $cat->id,
            'description' => 'Makan Siang Nasi Padang',
            'amount' => 45000,
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);

        // Header and mobile meta tests
        $response->assertSee('viewport-fit=cover', false);
        $response->assertSee('color-scheme', false);
        $response->assertSee('swanflow_theme', false);
        $response->assertSee('id="theme-toggle-btn"', false);
        $response->assertSee('toggleSwanFlowTheme()', false);
        $response->assertSee('Gusti Swandana', false);
        $response->assertSee('GS', false);
        $response->assertSee('Notifikasi', false);

        // Core Financial Components
        $response->assertSee('Total Saldo Aktif', false);
        $response->assertSee('Rp 14.850.000', false);
        $response->assertSee('Pemasukan', false);
        $response->assertSee('Pengeluaran', false);

        // Quick Actions & Wallets
        $response->assertSee('Dompet & Rekening', false);
        $response->assertSee('Bank BCA', false);
        $response->assertSee('SwanDrive Vault', false);
        $response->assertSee(route('drive.index'), false);

        // Transaction list
        $response->assertSee('Transaksi Terakhir', false);
        $response->assertSee('Makan Siang Nasi Padang', false);

        // Fixed Bottom Navigation Bar Items
        $response->assertSee('Home', false);
        $response->assertSee('Laporan', false);
        $response->assertSee('Transaksi', false);
        $response->assertSee('Riwayat', false);
    }

    /**
     * Test Eloquent models, relationships, and transaction scopes.
     */
    public function test_models_relationships_and_scopes_work_seamlessly(): void
    {
        $user = User::factory()->create([
            'name' => 'Gusti Swandana',
            'email' => 'gustiswandana@swanflow.com',
        ]);

        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'name' => 'BCA Syariah',
            'type' => 'bank',
            'balance' => 5000000.00,
        ]);

        $incomeCategory = Category::factory()->income()->create([
            'user_id' => $user->id,
            'name' => 'Gaji',
        ]);

        $expenseCategory = Category::factory()->expense()->create([
            'user_id' => $user->id,
            'name' => 'Kopi & Makan',
        ]);

        $incomeTransaction = Transaction::factory()->income()->create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'category_id' => $incomeCategory->id,
            'amount' => 5000000.00,
            'date' => now()->toDateString(),
            'description' => 'Gaji pertama',
        ]);

        $expenseTransaction = Transaction::factory()->expense()->create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'category_id' => $expenseCategory->id,
            'amount' => 50000.00,
            'date' => now()->toDateString(),
            'description' => 'Beli kopi',
        ]);

        // Assert relationships
        $this->assertCount(1, $user->wallets);
        $this->assertCount(2, $user->categories);
        $this->assertCount(2, $user->transactions);

        $this->assertTrue($wallet->user->is($user));
        $this->assertCount(2, $wallet->transactions);

        $this->assertTrue($incomeTransaction->user->is($user));
        $this->assertTrue($incomeTransaction->wallet->is($wallet));
        $this->assertTrue($incomeTransaction->category->is($incomeCategory));

        // Assert Enum casting
        $this->assertInstanceOf(TransactionType::class, $incomeTransaction->type);
        $this->assertSame(TransactionType::Income, $incomeTransaction->type);
        $this->assertSame(TransactionType::Expense, $expenseTransaction->type);
        $this->assertTrue($incomeTransaction->type->isIncome());
        $this->assertTrue($expenseTransaction->type->isExpense());

        // Assert Scopes
        $this->assertSame(1, Transaction::income()->count());
        $this->assertSame(1, Transaction::expense()->count());
        $this->assertSame(2, Transaction::recent(5)->count());
    }

    /**
     * Test guest request is redirected to login page when unauthenticated.
     */
    public function test_guest_request_redirects_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    /**
     * Test PWA manifest and mobile web app meta tags are properly rendered.
     */
    public function test_pwa_manifest_and_meta_tags_rendered_on_mobile_layout(): void
    {
        $user = User::factory()->create(['name' => 'Gusti Swandana']);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSee('rel="manifest"', false);
        $response->assertSee('manifest.webmanifest', false);
        $response->assertSee('name="apple-mobile-web-app-capable" content="yes"', false);
        $response->assertSee('name="mobile-web-app-capable" content="yes"', false);
        $response->assertSee('apple-touch-icon', false);
        $response->assertSee('/sw.js', false);
    }

    /**
     * Test manifest endpoint returns valid PWA configuration JSON.
     */
    public function test_manifest_endpoint_returns_valid_pwa_configuration(): void
    {
        $response = $this->get('/manifest.webmanifest');

        $response->assertStatus(200);
        $response->assertJson([
            'name' => 'SwanFlow - Personal Finance',
            'short_name' => 'SwanFlow',
            'display' => 'standalone',
            'theme_color' => '#0f172a',
        ]);

        $this->assertFileExists(public_path('manifest.webmanifest'));
        $this->assertFileExists(public_path('icons/icon-192.png'));
        $this->assertFileExists(public_path('icons/icon-512.png'));
        $this->assertFileExists(public_path('icons/apple-touch-icon.png'));
        $this->assertFileExists(public_path('sw.js'));
    }

    /**
     * Test Drive shortcut displays badge ONLY when active links exist, not for stored files.
     */
    public function test_drive_shortcut_shows_badge_only_for_active_links(): void
    {
        $user = User::factory()->create();

        // 1. User has 1 stored file, but 0 active links
        StoredFile::create([
            'user_id' => $user->id,
            'title' => 'Test Doc',
            'original_name' => 'doc.pdf',
            'file_path' => 'test/doc.pdf',
            'mime_type' => 'application/pdf',
            'extension' => 'pdf',
            'size_bytes' => 1024,
            'category' => 'document',
            'is_public' => false,
        ]);

        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(200);
        // Should NOT display active link badge
        $response->assertDontSee('Link Aktif', false);

        // 2. User creates an active drop link
        UploadLink::create([
            'user_id' => $user->id,
            'title' => 'Kirim File Project',
            'token' => 'active-token-123',
            'max_files' => 5,
            'max_file_size_mb' => 25,
            'is_active' => true,
        ]);

        $response2 = $this->actingAs($user)->get('/');
        $response2->assertStatus(200);
        $response2->assertSee('1 Link Aktif', false);
    }
}
