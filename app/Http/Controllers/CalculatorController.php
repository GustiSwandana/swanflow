<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CalculatorController extends Controller
{
    /**
     * Display the financial calculator and split bill simulator.
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();

        $wallets = Wallet::where('user_id', $user->id)->orderBy('name')->get();
        $totalBalance = (float) $wallets->sum('balance');

        return view('calculator.index', [
            'wallets' => $wallets,
            'totalBalance' => $totalBalance,
        ]);
    }
}
