<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Siguroha nga naka-log in ang user ug gi-gamit ang server-side session/auth
        if (!auth()->check()) {
            return redirect('/login');
        }

        // 2. Kuhaa ang role sa authenticated user gikan sa database
        $userRole = auth()->user()->role; // Assumed nga naa kay 'role' column sa users table

        // 3. I-check kung ang user role kay kesagaran pareho sa gisugo o allowed
        if ($userRole !== $role) {
            // Kung dili pwede, i-abort o i-redirect pabalik (Unauthorized / 403)
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}