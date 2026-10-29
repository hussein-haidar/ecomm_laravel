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
            // Rule: pelanggan = 10 min idle; pemilik/admin/superadmin = 1 day total
            if ($level === 'pelanggan') {
                $idle = session('last_activity_at');
                $idleLimit = (int) config('session.logout_idle_minutes');
                if (empty($idle)) {
                    session()->put('last_activity_at', now());
                } elseif (now()->greaterThan(Carbon::parse($idle)->addMinutes($idleLimit))) {
                    return $this->flushAndRedirect($request, $level);
                }
            } else {
                $loggedInAt = session('logged_in_at');
                if (empty($loggedInAt)) {
                    session()->put('logged_in_at', now());
                } else {
                    $expiresAt = Carbon::parse($loggedInAt)->addHours((int) config('session.max_active_hours'));
                    if (now()->greaterThanOrEqualTo($expiresAt)) {
                        return $this->flushAndRedirect($request, $level);
                    }
                }
            }
        }

        // Pelanggan aktif -> update last_activity; non-pelanggan tidak perlu
        if ($level === 'pelanggan') {
            session()->put('last_activity_at', now());
        }

        return $next($request);
    }

    private function flushAndRedirect(Request $request, $level): Response
    {
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
