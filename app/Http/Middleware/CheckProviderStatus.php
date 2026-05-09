<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckProviderStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $provider = $request->user();

        if (!$provider || $provider->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Your account is not approved yet. Please wait for admin approval.',
            ], 403);
        }

        return $next($request);
    }
}
