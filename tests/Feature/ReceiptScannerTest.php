<?php

namespace Tests\Feature;

use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
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
        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'amount' => 52500,
                                        'date' => '2026-09-26',
                                        'merchant' => 'Indomaret Tebet',
                                        'description' => 'Belanja di Indomaret Tebet',
                                        'type' => 'expense',
                                        'category_guess' => 'Belanja Harian',
                                        'items' => ['Roti Tawar', 'Susu Ultra'],
                                        'notes' => 'QRIS BCA',
                                    ]),
                                ],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $user = User::factory()->create();
        Category::create([
            'name' => 'Belanja Harian',
            'type' => TransactionType::Expense,
            'user_id' => $user->id,
        ]);
        Wallet::create([
            'name' => 'Dompet Utama',
            'type' => 'cash',
            'balance' => 500000,
            'user_id' => $user->id,
        ]);

        $image = UploadedFile::fake()->image('struk_indomaret.jpg', 600, 800);

        $response = $this->actingAs($user)->postJson(route('transactions.scan-receipt'), [
            'image' => $image,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'source', 'categories', 'wallets']);
        $this->assertEquals(52500, $response->json('amount'));
        $this->assertEquals('2026-09-26', $response->json('date'));
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
        $this->assertStringContainsStringIgnoringCase('KOPI KENANGAN', $response->json('merchant'));
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

    public function test_heuristic_parser_handles_trailing_sen_decimals(): void
    {
        $user = User::factory()->create();
        Category::create([
            'name' => 'Belanja Harian',
            'type' => TransactionType::Expense,
            'user_id' => $user->id,
        ]);

        $sampleText = "INDOMARET TEBET BARAT\n1 SUSU ULTRA 250ML 7.500\n1 ROTI TAWAR 15.000\n1 MINYAK GORENG 30.000\nTOTAL: Rp 52.500,00\nTUNAI: Rp 100.000,00\nKEMBALIAN: Rp 47.500,00";

        $response = $this->actingAs($user)->postJson(route('transactions.scan-receipt.parse-text'), [
            'text' => $sampleText,
        ]);

        $response->assertStatus(200);
        // Ensure it doesn't inflate to 5250000
        $this->assertEquals(52500, $response->json('amount'));
        $this->assertEquals('Indomaret', $response->json('merchant'));
    }

    public function test_heuristic_parser_prefers_total_over_cash_and_change(): void
    {
        $user = User::factory()->create();

        $sampleText = "RESTO BAKSO LAPANGAN TEMBAK\nBAKSO SPESIAL 45.000\nES TEH MANIS 10.000\nTOTAL HARGA: 55.000\nBAYAR CASH: 100.000\nKEMBALIAN: 45.000";

        $response = $this->actingAs($user)->postJson(route('transactions.scan-receipt.parse-text'), [
            'text' => $sampleText,
        ]);

        $response->assertStatus(200);
        $this->assertEquals(55000, $response->json('amount'));
    }

    public function test_heuristic_parser_detects_indonesian_textual_date(): void
    {
        $user = User::factory()->create();

        $sampleText = "ALFAMART PANGLIMA POLIM\n15 Agustus 2026 18:22\nTOTAL: Rp 32.000";

        $response = $this->actingAs($user)->postJson(route('transactions.scan-receipt.parse-text'), [
            'text' => $sampleText,
        ]);

        $response->assertStatus(200);
        $this->assertEquals('2026-08-15', $response->json('date'));
        $this->assertEquals(32000, $response->json('amount'));
    }

    public function test_heuristic_parser_detects_transfer_proof(): void
    {
        $user = User::factory()->create();

        $sampleText = "BCA MOBILE\nTRANSFER BERHASIL\nTanggal: 06-09-2026 10:15:30\nPenerima: Budi Santoso\nNominal: Rp 250.000\nTotal Transaksi: Rp 250.000";

        $response = $this->actingAs($user)->postJson(route('transactions.scan-receipt.parse-text'), [
            'text' => $sampleText,
        ]);

        $response->assertStatus(200);
        $this->assertEquals(250000, $response->json('amount'));
        $this->assertEquals('2026-09-06', $response->json('date'));
        $this->assertEquals('Transfer ke Budi Santoso', $response->json('merchant'));
    }

    public function test_async_receipt_scan_returns_instant_payload(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'amount' => '45.000',
                                        'date' => '26/09/2026',
                                        'merchant' => 'Alfamart',
                                        'description' => 'Belanja di Alfamart',
                                        'type' => 'expense',
                                        'category_guess' => 'Belanja Harian',
                                        'items' => ['Kopi', 'Snack'],
                                        'notes' => 'BCA',
                                    ]),
                                ],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $user = User::factory()->create();
        Category::create([
            'name' => 'Belanja Harian',
            'type' => TransactionType::Expense,
            'user_id' => $user->id,
        ]);
        Wallet::create([
            'name' => 'Dompet Utama',
            'type' => 'cash',
            'balance' => 500000,
            'user_id' => $user->id,
        ]);

        $image = UploadedFile::fake()->image('struk_alfa.jpg', 600, 800);

        $response = $this->actingAs($user)->postJson(route('transactions.scan-receipt.async'), [
            'image' => $image,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status' => 'completed',
        ]);
        $response->assertJsonStructure([
            'success',
            'scan_id',
            'status',
            'data' => [
                'status',
                'success',
                'source',
                'amount',
                'date',
                'merchant',
                'wallets',
                'categories',
            ],
        ]);

        // Assert amount and date normalization from string
        $this->assertEquals(45000, $response->json('amount'));
        $this->assertEquals('2026-09-26', $response->json('date'));

        $scanId = $response->json('scan_id');
        $statusResponse = $this->actingAs($user)->getJson(route('transactions.scan-receipt.status', ['scanId' => $scanId]));
        $statusResponse->assertStatus(200);
        $statusResponse->assertJson([
            'success' => true,
            'status' => 'completed',
        ]);
    }

    public function test_scan_accepts_heic_image(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'amount' => 75000,
                                        'date' => '2026-09-26',
                                        'merchant' => 'Cafe Swan',
                                        'type' => 'expense',
                                        'category_guess' => 'Makanan & Minuman',
                                    ]),
                                ],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $user = User::factory()->create();
        $heicFile = UploadedFile::fake()->create('receipt.heic', 1024, 'image/heic');

        $response = $this->actingAs($user)->postJson(route('transactions.scan-receipt.async'), [
            'image' => $heicFile,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'status' => 'completed']);
    }
}
