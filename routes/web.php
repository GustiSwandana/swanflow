<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CalculatorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientPortalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\DriveController;
use App\Http\Controllers\DropLinkController;
use App\Http\Controllers\FaceIdController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PayAdminApiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReceiptScannerController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

// Public Authentication, PIN & Face ID Login Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/auth/pin/verify', [AuthController::class, 'verifyPin'])->name('auth.pin.verify');
Route::post('/face-id/login/challenge', [FaceIdController::class, 'loginChallenge'])->name('faceid.login.challenge');
Route::post('/face-id/login/verify', [FaceIdController::class, 'loginVerify'])->name('faceid.login.verify');

Route::get('/manifest.webmanifest', function () {
    return response(file_get_contents(public_path('manifest.webmanifest')), 200, [
        'Content-Type' => 'application/manifest+json; charset=UTF-8',
    ]);
});
Route::get('/manifest.json', function () {
    return response(file_get_contents(public_path('manifest.json')), 200, [
        'Content-Type' => 'application/manifest+json; charset=UTF-8',
    ]);
});

// Maintenance endpoint to clear view cache and wipe stale compiled templates
Route::get('/swanflow-clear-view-cache', function () {
    try {
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        Artisan::call('cache:clear');
    } catch (Throwable $e) {
    }

    $files = glob(storage_path('framework/views/*.php'));
    $deleted = 0;
    if ($files) {
        foreach ($files as $file) {
            if (is_file($file)) {
                @unlink($file);
                $deleted++;
            }
        }
    }

    return response()->json([
        'status' => 'success',
        'deleted_views' => $deleted,
        'message' => 'View cache and compiled templates purged successfully.',
    ]);
});

// Maintenance endpoint to execute pending migrations on Hostinger
Route::get('/swanflow-migrate', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);
        $output = Artisan::output();

        return response()->json([
            'status' => 'success',
            'output' => trim($output),
        ]);
    } catch (Throwable $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
});

Route::get('/swanflow-status', function () {
    $buildPath = public_path('build/assets');
    $buildFiles = is_dir($buildPath) ? array_values(array_diff(scandir($buildPath), ['.', '..'])) : [];

    $gitLog = 'unavailable';
    try {
        if (function_exists('shell_exec')) {
            $gitLog = trim(@shell_exec('git log -1 --oneline 2>&1') ?? 'unavailable');
        }
    } catch (Throwable $e) {
        $gitLog = $e->getMessage();
    }

    return response()->json([
        'git' => $gitLog,
        'manifest' => file_exists(public_path('build/manifest.json')) ? json_decode(file_get_contents(public_path('build/manifest.json')), true) : null,
        'build_assets' => $buildFiles,
        'public_path' => public_path(),
    ]);
});

Route::get('/swanflow-git-pull', function () {
    $output = 'unavailable';
    try {
        if (function_exists('shell_exec')) {
            $output = trim(@shell_exec('git pull origin main 2>&1') ?? 'disabled');
        }
    } catch (Throwable $e) {
        $output = $e->getMessage();
    }

    return response()->json([
        'status' => 'success',
        'output' => $output,
    ]);
});

// Public Share & File Transfer Routes (SwanDrive)
Route::get('/share/{token}', [DriveController::class, 'sharedView'])->name('drive.shared.view');
Route::get('/share/{token}/preview', [DriveController::class, 'sharedPreview'])->name('drive.shared.preview');
Route::get('/share/{token}/download', [DriveController::class, 'sharedDownload'])->name('drive.shared.download');

// Public Shared Folder Routes (SwanDrive)
Route::get('/share/folder/{token}', [DriveController::class, 'sharedFolderView'])->name('drive.shared.folder.view');
Route::get('/share/folder/{token}/preview/{file}', [DriveController::class, 'sharedFolderPreview'])->name('drive.shared.folder.preview');
Route::get('/share/folder/{token}/download/{file}', [DriveController::class, 'sharedFolderDownload'])->name('drive.shared.folder.download');
Route::get('/share/folder/{token}/zip', [DriveController::class, 'sharedFolderZip'])->name('drive.shared.folder.zip');

// Public Drop Portal (Upload file request link)
Route::get('/drop/{token}', [DropLinkController::class, 'show'])->name('drive.drop.view');
Route::post('/drop/{token}', [DropLinkController::class, 'upload'])->name('drive.drop.upload');

// Public Client Portal & File Deliverables (/p/{token})
Route::get('/p/{token}', [ClientPortalController::class, 'show'])->name('orders.portal');
Route::get('/api/p/{token}', [ClientPortalController::class, 'getProjectData'])->name('api.orders.portal.data');
Route::post('/api/p/{token}/snap-token', [ClientPortalController::class, 'getSnapToken'])->name('api.orders.portal.snap-token');
Route::post('/api/p/{token}/upload', [ClientPortalController::class, 'uploadProof'])->name('api.orders.portal.upload');
Route::post('/p/{token}/upload', [ClientPortalController::class, 'uploadProof'])->name('orders.portal.upload');

// Midtrans Payment Gateway Webhook
Route::post('/api/midtrans/webhook', [MidtransWebhookController::class, 'handleNotification'])->name('midtrans.webhook');

// Client File Delivery & Paywall Gateway Admin (/admin & /api/admin/*)
Route::get('/admin', [PayAdminApiController::class, 'index'])->name('pay.admin');
Route::get('/api/admin/me', [PayAdminApiController::class, 'me']);
Route::post('/api/admin/login', [PayAdminApiController::class, 'login']);
Route::post('/api/admin/logout', [PayAdminApiController::class, 'logout']);
Route::get('/api/admin/settings', [PayAdminApiController::class, 'getSettings']);
Route::post('/api/admin/settings', [PayAdminApiController::class, 'updateSettings']);
Route::get('/api/admin/projects', [PayAdminApiController::class, 'getProjects']);
Route::post('/api/admin/projects', [PayAdminApiController::class, 'createProject']);
Route::put('/api/admin/projects/{id}', [PayAdminApiController::class, 'updateProject']);
Route::delete('/api/admin/projects/{id}', [PayAdminApiController::class, 'deleteProject']);
Route::post('/api/admin/projects/{id}/verify', [PayAdminApiController::class, 'verifyProject']);
Route::post('/api/admin/projects/{id}/make-free', [PayAdminApiController::class, 'makeFree']);
Route::get('/api/admin/bank-accounts', [PayAdminApiController::class, 'getBankAccounts']);
Route::post('/api/admin/bank-accounts', [PayAdminApiController::class, 'createBankAccount']);
Route::delete('/api/admin/bank-accounts/{id}', [PayAdminApiController::class, 'deleteBankAccount']);

// Static Uploads Fallback for Payment Proofs
Route::get('/uploads/{filename}', function ($filename) {
    $publicPath = public_path('uploads/'.$filename);
    if (file_exists($publicPath)) {
        return response()->file($publicPath);
    }
    $storagePath = storage_path('app/public/payment_proofs/'.$filename);
    if (file_exists($storagePath)) {
        return response()->file($storagePath);
    }
    abort(404);
})->where('filename', '[a-zA-Z0-9_\-\.]+');

// Logout Route (Accessible whether authenticated or expired, ensuring proper cleanup and redirect)
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Application Routes (Requires Authentication & Session Timeout Protection)
Route::middleware(['auth', 'session.timeout'])->group(function () {
    Route::post('/session/keepalive', [AuthController::class, 'keepAlive'])->name('session.keepalive');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Transactions (CRUD)
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::post('/transactions/sync', [TransactionController::class, 'sync'])->name('transactions.sync');
    Route::post('/transactions/scan-receipt', [ReceiptScannerController::class, 'scan'])->name('transactions.scan-receipt');
    Route::post('/transactions/scan-receipt/async', [ReceiptScannerController::class, 'scanAsync'])->name('transactions.scan-receipt.async');
    Route::get('/transactions/scan-receipt/status/{scanId}', [ReceiptScannerController::class, 'checkStatus'])->name('transactions.scan-receipt.status');
    Route::post('/transactions/scan-receipt/parse-text', [ReceiptScannerController::class, 'parseText'])->name('transactions.scan-receipt.parse-text');
    Route::put('/transactions/{transaction}', [TransactionController::class, 'update'])->name('transactions.update');
    Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');

    // Wallets / Accounts (CRUD)
    Route::get('/wallets', [WalletController::class, 'index'])->name('wallets.index');
    Route::post('/wallets', [WalletController::class, 'store'])->name('wallets.store');
    Route::put('/wallets/{wallet}', [WalletController::class, 'update'])->name('wallets.update');
    Route::delete('/wallets/{wallet}', [WalletController::class, 'destroy'])->name('wallets.destroy');

    // Categories (CRUD)
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Monthly Budgets (CRUD)
    Route::get('/budgets', [BudgetController::class, 'index'])->name('budgets.index');
    Route::post('/budgets', [BudgetController::class, 'store'])->name('budgets.store');
    Route::put('/budgets/{budget}', [BudgetController::class, 'update'])->name('budgets.update');
    Route::delete('/budgets/{budget}', [BudgetController::class, 'destroy'])->name('budgets.destroy');

    // Subscriptions & Recurring Bills (CRUD + Pay)
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::post('/subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');
    Route::put('/subscriptions/{subscription}', [SubscriptionController::class, 'update'])->name('subscriptions.update');
    Route::post('/subscriptions/{subscription}/pay', [SubscriptionController::class, 'pay'])->name('subscriptions.pay');
    Route::delete('/subscriptions/{subscription}', [SubscriptionController::class, 'destroy'])->name('subscriptions.destroy');

    // Debts & Receivables (CRUD + Repay)
    Route::get('/debts', [DebtController::class, 'index'])->name('debts.index');
    Route::post('/debts', [DebtController::class, 'store'])->name('debts.store');
    Route::put('/debts/{debt}', [DebtController::class, 'update'])->name('debts.update');
    Route::post('/debts/{debt}/repay', [DebtController::class, 'repay'])->name('debts.repay');
    Route::delete('/debts/{debt}', [DebtController::class, 'destroy'])->name('debts.destroy');

    // Investments & Portfolio Tracking
    Route::get('/investments', [InvestmentController::class, 'index'])->name('investments.index');
    Route::post('/investments', [InvestmentController::class, 'store'])->name('investments.store');
    Route::put('/investments/{investment}', [InvestmentController::class, 'update'])->name('investments.update');
    Route::post('/investments/{investment}/topup', [InvestmentController::class, 'topup'])->name('investments.topup');
    Route::post('/investments/{investment}/withdraw', [InvestmentController::class, 'withdraw'])->name('investments.withdraw');
    Route::patch('/investments/{investment}/value', [InvestmentController::class, 'updateValue'])->name('investments.update-value');
    Route::delete('/investments/{investment}', [InvestmentController::class, 'destroy'])->name('investments.destroy');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Financial Calculator & Split Bill (Patungan)
    Route::get('/calculator', [CalculatorController::class, 'index'])->name('calculator.index');

    // SwanDrive (File Storage & Transfer)
    Route::get('/drive', [DriveController::class, 'index'])->name('drive.index');
    Route::post('/drive/upload', [DriveController::class, 'store'])->name('drive.store');
    Route::post('/drive/upload-chunk', [DriveController::class, 'uploadChunk'])->name('drive.upload-chunk');
    Route::get('/drive/connect-google', [DriveController::class, 'connectGoogle'])->name('drive.connect');
    Route::get('/drive/callback', [DriveController::class, 'googleCallback'])->name('drive.callback');
    Route::patch('/drive/quota', [DriveController::class, 'updateQuota'])->name('drive.quota.update');
    Route::post('/drive/sync-google-quota', [DriveController::class, 'syncGoogleQuota'])->name('drive.sync-google-quota');
    Route::get('/drive/{file}/preview', [DriveController::class, 'preview'])->name('drive.preview');
    Route::get('/drive/{file}/download', [DriveController::class, 'download'])->name('drive.download');
    Route::patch('/drive/{file}/share', [DriveController::class, 'toggleShare'])->name('drive.share.toggle');
    Route::patch('/drive/{file}/move', [DriveController::class, 'moveFile'])->name('drive.file.move');
    Route::delete('/drive/{file}', [DriveController::class, 'destroy'])->name('drive.destroy');
    Route::post('/drive/batch-destroy', [DriveController::class, 'batchDestroy'])->name('drive.batch.destroy');

    // Folder Management
    Route::post('/drive/folders', [DriveController::class, 'createFolder'])->name('drive.folders.store');
    Route::patch('/drive/folders/{folder}', [DriveController::class, 'updateFolder'])->name('drive.folders.update');
    Route::delete('/drive/folders/{folder}', [DriveController::class, 'destroyFolder'])->name('drive.folders.destroy');
    Route::patch('/drive/folders/{folder}/share', [DriveController::class, 'toggleFolderShare'])->name('drive.folders.share.toggle');

    Route::post('/drive/drop-links', [DropLinkController::class, 'store'])->name('drive.drop-links.store');
    Route::patch('/drive/drop-links/{link}/toggle', [DropLinkController::class, 'toggle'])->name('drive.drop-links.toggle');
    Route::delete('/drive/drop-links/{link}', [DropLinkController::class, 'destroy'])->name('drive.drop-links.destroy');

    // To-Do List / Activities
    Route::get('/todos', [TodoController::class, 'index'])->name('todos.index');
    Route::post('/todos', [TodoController::class, 'store'])->name('todos.store');
    Route::put('/todos/{todo}', [TodoController::class, 'update'])->name('todos.update');
    Route::patch('/todos/{todo}/toggle', [TodoController::class, 'toggle'])->name('todos.toggle');
    Route::delete('/todos/{todo}', [TodoController::class, 'destroy'])->name('todos.destroy');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::put('/profile/pin', [ProfileController::class, 'updatePin'])->name('profile.pin.update');

    // Face ID Registration & Management
    Route::post('/face-id/register/challenge', [FaceIdController::class, 'registerChallenge'])->name('faceid.register.challenge');
    Route::post('/face-id/register/verify', [FaceIdController::class, 'registerVerify'])->name('faceid.register.verify');
    Route::delete('/face-id/{credential}', [FaceIdController::class, 'destroy'])->name('faceid.destroy');

    // Client Projects, Invoices & Deliverables (/orders)
    Route::get('/orders', function (Request $request) {
        if (class_exists(OrderController::class) && Schema::hasTable('orders')) {
            return app(OrderController::class)->index($request);
        }

        return redirect()->route('dashboard');
    })->name('orders.index');

    if (class_exists(OrderController::class)) {
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
        Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
        Route::post('/orders/{order}/make-free', [OrderController::class, 'toggleFree'])->name('orders.toggle-free');
        Route::post('/orders/{order}/verify', [OrderController::class, 'verify'])->name('orders.verify');
        Route::post('/orders/{order}/reject', [OrderController::class, 'reject'])->name('orders.reject');
        Route::post('/orders/settings', [OrderController::class, 'updateSettings'])->name('orders.settings.update');
        Route::post('/orders/bank-accounts', [OrderController::class, 'storeBankAccount'])->name('orders.bank-accounts.store');
        Route::delete('/orders/bank-accounts/{account}', [OrderController::class, 'destroyBankAccount'])->name('orders.bank-accounts.destroy');
    }
});
