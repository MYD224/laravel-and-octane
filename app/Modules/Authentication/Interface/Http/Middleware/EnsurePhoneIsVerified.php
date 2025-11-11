<?php

namespace App\Modules\Authentication\Interface\Http\Middleware;


use Closure;

class EnsurePhoneIsVerified
{
    public function handle($request, Closure $next)
    {
        if (!$request->user() || !$request->user()->phone_verified_at) {
            return response()->json(['error' => 'Phone not verified'], 403);
        }

        return $next($request);
    }
}
