<?php

/**
 * @file app/Http/Middleware/VerifyExternalApiToken.php
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyExternalApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $expected = (string) config('external_api.token');
        $provided = (string) $request->bearerToken();

        if ($expected === '' || $provided === '' || !hash_equals($expected, $provided)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
                'data' => null,
            ], 401);
        }

        return $next($request);
    }
}
