<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureApiKey
{
    public function handle(Request $request, Closure $next)
    {
        $key = $request->header('X-API-Key') ?: $request->query('api_key');
        $expected = env('API_KEY', '');

        // Saat API_KEY belum diisi (mode development), endpoint tetap bisa diuji tanpa key
        if (empty($expected)) {
            return $next($request);
        }

        if (empty($key) || !is_string($key) || !hash_equals($expected, $key)) {
            return response()->json([
                'success' => false,
                'message' => 'API Key tidak valid. Gunakan header X-API-Key.',
            ], 401);
        }

        return $next($request);
    }
}