<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use App\Models\User;
use Closure;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = User::find(auth()->user()->id);
            if ($user->role === 'admin' && $user->unit()->exists()) {
                return $next($request);
            }

            auth()->logout();
        }

        return redirect()->route('login');
    }
}
