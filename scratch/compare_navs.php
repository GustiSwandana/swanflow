<?php

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ViewErrorBag;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$user = User::first();
auth()->login($user);
view()->share('errors', new ViewErrorBag);

// Request dashboard
$req1 = Request::create('/', 'GET');
app()->instance('request', $req1);
Route::dispatch($req1);
$dashHtml = view('dashboard', [
    'user' => $user,
    'totalBalance' => 1000000,
    'thisMonthIncome' => 500000,
    'thisMonthExpense' => 200000,
    'wallets' => collect(),
    'categoryBudgets' => collect(),
    'recentTransactions' => collect(),
    'pendingDebts' => collect(),
    'todayTodos' => collect(),
    'activeSubscriptions' => collect(),
    'totalBudget' => 0,
    'totalBudgetSpent' => 0,
    'netCashflow' => 300000,
])->render();

// Request todos
$req2 = Request::create('/todos', 'GET');
app()->instance('request', $req2);
Route::dispatch($req2);
$todosHtml = view('todos.index', [
    'todos' => collect(),
    'todayTodos' => collect(),
    'upcomingTodos' => collect(),
    'completedTodos' => collect(),
    'tab' => 'today',
])->render();

function getNavBlock($html)
{
    $start = strpos($html, '<nav');
    $end = strpos($html, '</nav>', $start);

    return substr($html, $start, $end - $start + 6);
}

$dashNav = getNavBlock($dashHtml);
$todosNav = getNavBlock($todosHtml);

file_put_contents(__DIR__.'/dash_full.html', $dashHtml);
file_put_contents(__DIR__.'/todos_full.html', $todosHtml);

function inspectTagStackBeforeNav($html)
{
    $navPos = strpos($html, '<nav');
    $preceding = substr($html, 0, $navPos);
    preg_match_all('/<(\/?[a-z0-9]+)[^>]*>/i', $preceding, $matches);
    $stack = [];
    $voidTags = ['meta', 'link', 'img', 'br', 'hr', 'input', 'source', 'param', 'path', 'circle', 'rect', 'stop', 'line', 'polyline', 'polygon', 'ellipse', 'use', 'fedropshadow'];
    foreach ($matches[1] as $tag) {
        $tagLower = strtolower($tag);
        if (in_array($tagLower, $voidTags)) {
            continue;
        }
        if (substr($tagLower, 0, 1) === '/') {
            $closing = substr($tagLower, 1);
            if (! empty($stack) && end($stack) === $closing) {
                array_pop($stack);
            } else {
                echo "Mismatched close: $closing, expected: ".(end($stack) ?: 'none')."\n";
            }
        } else {
            $stack[] = $tagLower;
        }
    }

    return $stack;
}

echo 'Stack before <nav> on Dashboard: '.implode(' > ', inspectTagStackBeforeNav($dashHtml))."\n";
echo 'Stack before <nav> on Todos: '.implode(' > ', inspectTagStackBeforeNav($todosHtml))."\n";
