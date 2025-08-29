<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        // Cek apakah user sudah login
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Pisahkan peran yang diizinkan berdasarkan koma atau pipe
        $allowedRoles = preg_split('/[|,]/', $roles);

        // Periksa apakah peran pengguna ada di dalam daftar peran yang diizinkan
        if (!in_array($request->user()->role, $allowedRoles)) {
            abort(403, 'Unauthorized access. Your role does not have permission to access this page.');
        }

        return $next($request);
    }
}
