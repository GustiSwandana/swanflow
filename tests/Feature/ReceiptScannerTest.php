<?php

namespace Tests\Feature;

use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ReceiptScannerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_scan_receipt(): void
    {
        $response = $this->postJson(route('transactions.scan-receipt'), []);
        $response->assertStatus(401);
    }

    public function test_scan_validation_requires_valid_image(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('transactions.scan-receipt'), []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['image']);
    }

    public function test_user_can_upload_receipt_image_and_receives_json_response(): void
    {
        $user = User::factory()->create();
        Category::create([
            'name' => 'Belanja Harian',
            'type' => TransactionType::Expense,
            'user_id' => $user->id,
        ]);

        $image = UploadedFile::fake()->image('struk_indomaret.jpg', 600, 800);

        $response = $this->actingAs($user)->postJson(route('transactions.scan-receipt'), [
            'image' => $image,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'source']);
    }

    public function test_user_can_parse_receipt_text_via_heuristic_parser(): void
    {
        $user = User::factory()->create();
        $cat = Category::create([
            'name' => 'Makanan & Minuman',
            'type' => TransactionType::Expense,
            'user_id' => $user->id,
        ]);

        $sampleText = "KOPI KENANGAN TEBET\nJl. Tebet Raya No. 12\n06/09/2026 14:30\n\n1 Kopi Kenangan Mantan 22.000\n1 Roti Coklat 15.000\n\nTOTAL BAYAR: Rp 37.000\nCASH: Rp 50.000\nKEMBALI: Rp 13.000";

        $response = $this->actingAs($user)->postJson(route('transactions.scan-receipt.parse-text'), [
            'text' => $sampleText,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'source' => 'heuristic_text_parser',
            'amount' => 37000,
            'date' => '2026-09-06',
            'type' => 'expense',
            'category_id' => $cat->id,
            'category_name' => 'Makanan & Minuman',
        ]);
        $this->assertStringContainsString('KOPI KENANGAN', $response->json('merchant'));
    }

    public function test_heuristic_parser_detects_transportation_category(): void
    {
        $user = User::factory()->create();
        $cat = Category::create([
            'name' => 'Transportasi',
            'type' => TransactionType::Expense,
            'user_id' => $user->id,
        ]);

        $sampleText = "SPBU PERTAMINA 34-12345\nJAKARTA SELATAN\nTanggal: 05/09/2026\nPERTALITE 10.00 LTR\nTOTAL: Rp 100.000";

        $response = $this->actingAs($user)->postJson(route('transactions.scan-receipt.parse-text'), [
            'text' => $sampleText,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'amount' => 100000,
            'type' => 'expense',
            'category_id' => $cat->id,
        ]);
    }
}
