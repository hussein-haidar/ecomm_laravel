<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class AutoLogout
{
    public function handle(Request $request, Closure $next): Response
    {
        $level = session('level');
        $isAuthed = session('user_logged_in') === true
            || in_array($level, ['pelanggan', 'pemilik', 'admin', 'superadmin']);

        if ($isAuthed) {
            $loggedInAt = session('logged_in_at');

            if (empty($loggedInAt)) {
                session(['logged_in_at' => now()]);
            } else {
                try {
                    $expiresAt = Carbon::parse($loggedInAt)->addHours((int) config('session.max_active_hours'));
                } catch (\Throwable $e) {
                    // Data session bentrok/tidak valid (misal sisa sesi lama) -> tidak boleh memicu 500
                    $expiresAt = now()->addHours((int) config('session.max_active_hours'));
                }

                if (now()->greaterThanOrEqualTo($expiresAt)) {
                    $isAjax = $request->expectsJson()
                        || str_starts_with($request->path(), 'pelanggan_data/chat')
                        || str_starts_with($request->path(), 'admin_data/chat');

                    session()->flush();

                    if ($isAjax) {
                        return response()->json(['success' => false, 'message' => 'Sesi berakhir. Silakan login kembali.'], 401);
                    }

                    $redirect = match ($level) {
                        'superadmin' => route('auth.login_superadmin'),
                        'pemilik', 'admin' => route('auth.login_user'),
                        default => route('auth.login_pelanggan'),
                    };

                    return redirect($redirect);
                }
            }
        }

        return $next($request);
    }
}
