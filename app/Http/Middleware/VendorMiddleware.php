<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VendorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->role !== 'vendor') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
           return redirect()->route('login')
                ->withErrors(['email' => 'Access denied. Vendor only.']);
        }

        return $next($request);
    }
}
