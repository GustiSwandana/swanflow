<?php

namespace App\Http\Controllers;

use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\Folder;
use App\Models\Order;
use App\Models\OrderBankAccount;
use App\Models\OrderSetting;
use App\Models\StoredFile;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display a listing of client project orders and status.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // 1. Get or create Studio Settings
        $settings = OrderSetting::firstOrCreate(
            ['user_id' => $user->id],
            [
                'studio_name' => 'Studio & Creative',
                'bank_instructions' => "1. Transfer sesuai nominal total tagihan.\n2. Simpan struk / bukti pembayaran.\n3. Unggah bukti pembayaran melalui link ini untuk membuka akses download file.",
            ]
        );

        // 2. Auto-seed bank accounts from existing user wallets if user has none
        $bankAccounts = OrderBankAccount::where('user_id', $user->id)->get();
        if ($bankAccounts->isEmpty()) {
            $userWallets = Wallet::where('user_id', $user->id)->get();
            foreach ($userWallets as $w) {
                OrderBankAccount::create([
                    'user_id' => $user->id,
                    'wallet_id' => $w->id,
                    'bank_name' => $w->name,
                    'account_number' => '-',
                    'account_name' => $user->name,
                    'is_active' => true,
                ]);
            }
            $bankAccounts = OrderBankAccount::where('user_id', $user->id)->get();
        }

        // 3. Filters & Search
        $statusFilter = (string) $request->query('status', 'all');
        $search = trim((string) $request->query('q', ''));

        $query = Order::where('user_id', $user->id)
            ->with(['wallet', 'deliverables', 'storedFile', 'folder'])
            ->latest();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%")
                    ->orWhere('project_title', 'like', "%{$search}%")
                    ->orWhere('token', 'like', "%{$search}%")
                    ->orWhere('client_phone', 'like', "%{$search}%");
            });
        }

        if ($statusFilter === 'free') {
            $query->where('is_free', true);
        } elseif ($statusFilter !== 'all') {
            $query->where('status', $statusFilter)->where('is_free', false);
        }

        $orders = $query->paginate(20)->withQueryString();

        // 4. Statistics Calculation
        $allUserOrders = Order::where('user_id', $user->id)->get();
        $totalProjects = $allUserOrders->count();
        $verifiedOrders = $allUserOrders->where('status', 'verified')->where('is_free', false);
        $totalRevenue = (float) $verifiedOrders->sum(fn ($o) => $o->final_amount);
        $waitingCount = $allUserOrders->where('status', 'waiting_verification')->count();
        $unpaidCount = $allUserOrders->where('status', 'unpaid')->where('is_free', false)->count();

        // 5. External selection assets from SwanFlow
        $wallets = Wallet::where('user_id', $user->id)->orderBy('name')->get();
        $storedFiles = StoredFile::where('user_id', $user->id)->latest()->take(50)->get();
        $folders = Folder::where('user_id', $user->id)->withCount('files')->orderBy('name')->get();

        $preselectedFolderId = $request->query('folder_id');
        $preselectedFileId = $request->query('file_id');
        $autoCreate = $request->boolean('create');

        return view('orders.index', [
            'orders' => $orders,
            'settings' => $settings,
            'bankAccounts' => $bankAccounts,
            'wallets' => $wallets,
            'storedFiles' => $storedFiles,
            'folders' => $folders,
            'statusFilter' => $statusFilter,
            'search' => $search,
            'totalProjects' => $totalProjects,
            'totalRevenue' => $totalRevenue,
            'waitingCount' => $waitingCount,
            'unpaidCount' => $unpaidCount,
            'preselectedFolderId' => $preselectedFolderId,
            'preselectedFileId' => $preselectedFileId,
            'autoCreate' => $autoCreate,
        ]);
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_phone' => 'nullable|string|max:50',
            'project_title' => 'required|string|max:255',
            'amount' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'discount_label' => 'nullable|string|max:100',
            'is_free' => 'nullable|boolean',
            'gdrive_url' => 'nullable|url|max:1000',
            'stored_file_id' => 'nullable|exists:stored_files,id',
            'folder_id' => 'nullable|exists:folders,id',
            'wallet_id' => 'nullable|exists:wallets,id',
            'notes' => 'nullable|string',
            'custom_token' => 'nullable|string|alpha_dash|max:64|unique:orders,token',
        ]);

        $token = ! empty($validated['custom_token'])
            ? Str::slug($validated['custom_token'])
            : Str::lower(Str::random(10));

        // Ensure token uniqueness
        while (Order::where('token', $token)->exists()) {
            $token = Str::lower(Str::random(10));
        }

        $order = Order::create([
            'user_id' => $user->id,
            'token' => $token,
            'client_name' => $validated['client_name'],
            'client_phone' => $validated['client_phone'] ?? null,
            'project_title' => $validated['project_title'],
            'amount' => (float) ($validated['amount'] ?? 0),
            'discount' => (float) ($validated['discount'] ?? 0),
            'discount_label' => $validated['discount_label'] ?? null,
            'is_free' => $request->boolean('is_free'),
            'gdrive_url' => $validated['gdrive_url'] ?? null,
            'stored_file_id' => $validated['stored_file_id'] ?? null,
            'folder_id' => $validated['folder_id'] ?? null,
            'wallet_id' => $validated['wallet_id'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => $request->boolean('is_free') ? 'verified' : 'unpaid',
        ]);

        // Auto-activate SwanDrive folder or file sharing if linked
        if (! empty($validated['folder_id'])) {
            $folder = Folder::where('id', $validated['folder_id'])->where('user_id', $user->id)->first();
            if ($folder) {
                if (! $folder->share_token) {
                    $folder->share_token = Str::random(40);
                }
                $folder->is_public = true;
                $folder->save();
            }
        }
        if (! empty($validated['stored_file_id'])) {
            $file = StoredFile::where('id', $validated['stored_file_id'])->where('user_id', $user->id)->first();
            if ($file) {
                if (! $file->share_token) {
                    $file->share_token = Str::random(40);
                }
                $file->is_public = true;
                $file->save();
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Proyek klien berhasil dibuat!',
                'order' => $order,
                'client_url' => $order->client_url,
            ]);
        }

        return redirect()->route('orders.index')->with('success', "Proyek '{$order->project_title}' berhasil dibuat! Link klien siap dibagikan.");
    }

    /**
     * Update the specified order.
     */
    public function update(Request $request, Order $order): RedirectResponse|JsonResponse
    {
        $this->authorizeOrder($order);

        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_phone' => 'nullable|string|max:50',
            'project_title' => 'required|string|max:255',
            'amount' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'discount_label' => 'nullable|string|max:100',
            'is_free' => 'nullable|boolean',
            'status' => 'required|in:unpaid,waiting_verification,verified,rejected',
            'gdrive_url' => 'nullable|url|max:1000',
            'stored_file_id' => 'nullable|exists:stored_files,id',
            'folder_id' => 'nullable|exists:folders,id',
            'wallet_id' => 'nullable|exists:wallets,id',
            'notes' => 'nullable|string',
            'token' => 'required|string|alpha_dash|max:64|unique:orders,token,'.$order->id,
        ]);

        $order->update([
            'client_name' => $validated['client_name'],
            'client_phone' => $validated['client_phone'] ?? null,
            'project_title' => $validated['project_title'],
            'amount' => (float) ($validated['amount'] ?? 0),
            'discount' => (float) ($validated['discount'] ?? 0),
            'discount_label' => $validated['discount_label'] ?? null,
            'is_free' => $request->boolean('is_free'),
            'status' => $validated['status'],
            'gdrive_url' => $validated['gdrive_url'] ?? null,
            'stored_file_id' => $validated['stored_file_id'] ?? null,
            'folder_id' => $validated['folder_id'] ?? null,
            'wallet_id' => $validated['wallet_id'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'token' => Str::slug($validated['token']),
        ]);

        // Auto-activate SwanDrive folder or file sharing if linked
        if (! empty($validated['folder_id'])) {
            $folder = Folder::where('id', $validated['folder_id'])->where('user_id', $order->user_id)->first();
            if ($folder) {
                if (! $folder->share_token) {
                    $folder->share_token = Str::random(40);
                }
                $folder->is_public = true;
                $folder->save();
            }
        }
        if (! empty($validated['stored_file_id'])) {
            $file = StoredFile::where('id', $validated['stored_file_id'])->where('user_id', $order->user_id)->first();
            if ($file) {
                if (! $file->share_token) {
                    $file->share_token = Str::random(40);
                }
                $file->is_public = true;
                $file->save();
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data proyek berhasil diperbarui!',
                'order' => $order,
            ]);
        }

        return redirect()->route('orders.index')->with('success', 'Data proyek berhasil diperbarui!');
    }

    /**
     * Remove the specified order from storage.
     */
    public function destroy(Order $order): RedirectResponse|JsonResponse
    {
        $this->authorizeOrder($order);

        if ($order->payment_proof_path && Storage::disk('public')->exists($order->payment_proof_path)) {
            Storage::disk('public')->delete($order->payment_proof_path);
        }

        $order->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Proyek berhasil dihapus.',
            ]);
        }

        return redirect()->route('orders.index')->with('success', 'Proyek berhasil dihapus.');
    }

    /**
     * Toggle the free status of the order.
     */
    public function toggleFree(Order $order): RedirectResponse|JsonResponse
    {
        $this->authorizeOrder($order);

        $newFree = ! $order->is_free;
        $order->is_free = $newFree;
        if ($newFree && $order->status === 'unpaid') {
            $order->status = 'verified';
        }
        $order->save();

        $msg = $newFree
            ? 'Akses proyek ini sekarang GRATIS untuk klien (link file langsung terbuka).'
            : 'Akses proyek dikembalikan menjadi BERBAYAR (klien wajib menyelesaikan pembayaran).';

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg, 'is_free' => $order->is_free]);
        }

        return redirect()->route('orders.index')->with('success', $msg);
    }

    /**
     * Verify payment proof and optionally record income transaction in SwanFlow.
     */
    public function verify(Request $request, Order $order): RedirectResponse|JsonResponse
    {
        $this->authorizeOrder($order);
        $user = $request->user();

        $walletId = $request->input('wallet_id', $order->wallet_id);
        $recordTransaction = $request->boolean('record_transaction', true);

        DB::transaction(function () use ($order, $user, $walletId, $recordTransaction) {
            $order->status = 'verified';
            $order->verified_at = now();
            $order->rejection_reason = null;

            if ($walletId) {
                $order->wallet_id = $walletId;
            }

            // Auto-create income transaction if requested and order has amount
            if ($recordTransaction && $order->final_amount > 0 && $order->wallet_id && ! $order->transaction_id) {
                $wallet = Wallet::where('user_id', $user->id)->find($order->wallet_id);
                if ($wallet) {
                    // Find or create 'Pendapatan Project' category
                    $category = Category::firstOrCreate(
                        [
                            'user_id' => $user->id,
                            'name' => 'Pendapatan Project',
                            'type' => TransactionType::Income->value,
                        ],
                        [
                            'icon' => 'camera',
                            'color' => '#10b981',
                        ]
                    );

                    $tx = Transaction::create([
                        'user_id' => $user->id,
                        'wallet_id' => $wallet->id,
                        'category_id' => $category->id,
                        'type' => TransactionType::Income->value,
                        'amount' => $order->final_amount,
                        'admin_fee' => 0,
                        'fee_payer' => 'source',
                        'date' => now()->toDateString(),
                        'description' => "Pembayaran Project: {$order->client_name} ({$order->project_title})",
                    ]);

                    $wallet->increment('balance', $order->final_amount);
                    $order->transaction_id = $tx->id;
                }
            }

            $order->save();
        });

        $msg = "Pembayaran untuk {$order->client_name} berhasil diverifikasi! Link file sekarang terbuka untuk klien.";

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('orders.index')->with('success', $msg);
    }

    /**
     * Reject payment proof with reason.
     */
    public function reject(Request $request, Order $order): RedirectResponse|JsonResponse
    {
        $this->authorizeOrder($order);

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $order->status = 'rejected';
        $order->rejection_reason = $request->input('rejection_reason');
        $order->save();

        $msg = 'Bukti transfer ditolak. Alasan penolakan telah dicatat dan ditampilkan kepada klien.';

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('orders.index')->with('success', $msg);
    }

    /**
     * Update Studio & Payment Settings.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'studio_name' => 'required|string|max:255',
            'bank_instructions' => 'nullable|string',
            'admin_pin' => 'nullable|string|max:20',
            'qris_image' => 'nullable|image|max:3072',
            'midtrans_enabled' => 'nullable|boolean',
            'midtrans_server_key' => 'nullable|string',
            'midtrans_client_key' => 'nullable|string',
            'midtrans_is_production' => 'nullable|boolean',
        ]);

        $settings = OrderSetting::firstOrCreate(['user_id' => $user->id]);
        $settings->studio_name = $validated['studio_name'];
        $settings->bank_instructions = $validated['bank_instructions'] ?? null;
        $settings->midtrans_enabled = $request->boolean('midtrans_enabled');

        if ($request->has('midtrans_server_key')) {
            $settings->midtrans_server_key = $request->input('midtrans_server_key');
        }
        if ($request->has('midtrans_client_key')) {
            $settings->midtrans_client_key = $request->input('midtrans_client_key');
        }
        $settings->midtrans_is_production = $request->boolean('midtrans_is_production');

        if (! empty($validated['admin_pin'])) {
            $settings->admin_pin = $validated['admin_pin'];
        }

        if ($request->hasFile('qris_image')) {
            $path = $request->file('qris_image')->store('qris', 'public');
            $settings->qris_image_path = $path;
        }

        $settings->save();

        return redirect()->route('orders.index')->with('success', 'Pengaturan Studio & Pembayaran berhasil disimpan!');
    }

    /**
     * Add a bank account / e-wallet.
     */
    public function storeBankAccount(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:100',
            'account_name' => 'required|string|max:255',
            'wallet_id' => 'nullable|exists:wallets,id',
        ]);

        OrderBankAccount::create([
            'user_id' => $user->id,
            'wallet_id' => $validated['wallet_id'] ?? null,
            'bank_name' => $validated['bank_name'],
            'account_number' => $validated['account_number'],
            'account_name' => $validated['account_name'],
            'is_active' => true,
        ]);

        return redirect()->route('orders.index')->with('success', 'Rekening / E-Wallet tujuan transfer berhasil ditambahkan!');
    }

    /**
     * Delete a bank account.
     */
    public function destroyBankAccount(OrderBankAccount $account): RedirectResponse
    {
        if ($account->user_id !== auth()->id()) {
            abort(403);
        }

        $account->delete();

        return redirect()->route('orders.index')->with('success', 'Rekening berhasil dihapus.');
    }

    /**
     * Helper to verify order belongs to authenticated user.
     */
    protected function authorizeOrder(Order $order): void
    {
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Akses tidak diizinkan.');
        }
    }
}
