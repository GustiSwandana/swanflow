<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $timeoutMinutes = (int) config('session.inactivity_timeout', config('session.lifetime', 5));
            $timeoutSeconds = $timeoutMinutes * 60;
            $lastActivity = $request->session()->get('last_activity_time');

            if ($lastActivity !== null && (now()->timestamp - (int) $lastActivity) > $timeoutSeconds) {
                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Sesi Anda telah berakhir karena tidak ada aktivitas. Silakan verifikasi biometrik atau PIN.',
                        'session_expired' => true,
                    ], 401);
                }

                return redirect()->route('login')->with([
                    'session_expired' => true,
                    'warning' => "Sesi Anda telah terkunci karena tidak aktif selama {$timeoutMinutes} menit demi keamanan akun keuangan Anda. Silakan gunakan Biometrik (Face ID / Sidik Jari) atau masukkan PIN Anda.",
                ]);
            }

            $request->session()->put('last_activity_time', now()->timestamp);
        }

        return $next($request);
    }
}
