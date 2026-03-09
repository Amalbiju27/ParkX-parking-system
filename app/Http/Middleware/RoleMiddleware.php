<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Check if the user is logged in
        if (!Auth::check()) {
            return redirect('/login');
        }

        // 2. Check if the user's role matches the required role for the route
        if (Auth::user()->role !== $role) {
            // Redirect based on their actual role if they try to go somewhere they shouldn't
            return match (Auth::user()->role) {
                'admin' => redirect('/admin'),
                'owner' => redirect('/owner'),
                'user'  => redirect('/user'),
                default => redirect('/login'),
            };
        }

        return $next($request);
    }
}