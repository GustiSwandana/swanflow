<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CalculatorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\DriveController;
use App\Http\Controllers\DropLinkController;
use App\Http\Controllers\FaceIdController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

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

// Public Share & File Transfer Routes (SwanDrive)
Route::get('/share/{token}', [DriveController::class, 'sharedView'])->name('drive.shared.view');
Route::get('/share/{token}/download', [DriveController::class, 'sharedDownload'])->name('drive.shared.download');

// Public Drop Portal (Upload file request link)
Route::get('/drop/{token}', [DropLinkController::class, 'show'])->name('drive.drop.view');
Route::post('/drop/{token}', [DropLinkController::class, 'upload'])->name('drive.drop.upload');

// Protected Application Routes (Requires Authentication)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Transactions (CRUD)
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
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

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Financial Calculator & Split Bill (Patungan)
    Route::get('/calculator', [CalculatorController::class, 'index'])->name('calculator.index');

    // SwanDrive (File Storage & Transfer)
    Route::get('/drive', [DriveController::class, 'index'])->name('drive.index');
    Route::post('/drive/upload', [DriveController::class, 'store'])->name('drive.store');
    Route::patch('/drive/quota', [DriveController::class, 'updateQuota'])->name('drive.quota.update');
    Route::get('/drive/{file}/download', [DriveController::class, 'download'])->name('drive.download');
    Route::patch('/drive/{file}/share', [DriveController::class, 'toggleShare'])->name('drive.share.toggle');
    Route::delete('/drive/{file}', [DriveController::class, 'destroy'])->name('drive.destroy');
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
});
