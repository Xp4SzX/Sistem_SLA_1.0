<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OperatorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Daftar role yang diizinkan masuk sebagai operator bidang
        $allowedRoles = ['pembangunan', 'hosting', 'subdomain', 'informasi_publik'];

        if (auth()->check() && in_array(auth()->user()->role, $allowedRoles)) {
            return $next($request);
        }

        abort(403, 'Unauthorized action. Anda bukan operator bidang.');
    }
}
