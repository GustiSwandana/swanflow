<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureMaintenanceAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $secret = (string) env('SWANFLOW_DEPLOY_KEY');
        $providedKey = (string) ($request->header('X-SwanFlow-Key') ?? $request->query('key', ''));

        if ($secret !== '' && hash_equals($secret, $providedKey)) {
            return $next($request);
        }

        if (Auth::check()) {
            return $next($request);
        }

        abort(403, 'Akses ditolak: Endpoint maintenance SwanFlow dilindungi.');
    }
}
