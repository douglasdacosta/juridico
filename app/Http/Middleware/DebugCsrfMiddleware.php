<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DebugCsrfMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('post') || $request->isMethod('put') || $request->isMethod('patch')) {
            try {
                $sessionToken = $request->session()->token();
                $requestToken = $request->input('_token') ?? $request->header('X-CSRF-TOKEN');
                $tokensMatch = hash_equals($sessionToken, $requestToken ?: '');

                Log::debug('CSRF DEBUG', [
                    'path' => $request->path(),
                    'method' => $request->method(),
                    'session_id' => $request->session()->getId(),
                    'session_token' => substr($sessionToken, 0, 10) . '...',
                    'request_token' => $requestToken ? substr($requestToken, 0, 10) . '...' : 'MISSING',
                    'tokens_match' => $tokensMatch,
                    'request_has_token' => !empty($requestToken),
                    'request_field_token' => !empty($request->input('_token')),
                    'request_header_token' => !empty($request->header('X-CSRF-TOKEN')),
                ]);
            } catch (\Throwable $e) {
                Log::error('CSRF DEBUG ERROR: ' . $e->getMessage());
            }
        }

        return $next($request);
    }
}
