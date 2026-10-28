<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu untuk mengakses halaman tersebut.');
        }

        $user = Auth::user();

        if (!in_array($user->role, $roles)) {
            if ($user->isAdmin()) {
                return redirect('/admin/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            }

            if ($user->isUser()) {
                return redirect('/my-haki')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            }

            return redirect('/')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        return $next($request);
    }
}
