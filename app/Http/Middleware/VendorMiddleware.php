<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VendorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Ensure role is vendor
        if ($user->role !== 'vendor') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Access denied. Vendor only.']);
        }

        // 🔒 If vendor not verified, allow only verification routes
        if ($user->vendor_status !== 'approved') {

            if (!$request->routeIs('vendor.verification') &&
                !$request->routeIs('vendor.verification.resubmit')) {

                return redirect()->route('vendor.verification');
            }
        }

        return $next($request);
    }
}
