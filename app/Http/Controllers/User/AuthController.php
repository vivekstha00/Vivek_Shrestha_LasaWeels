<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('user.pages.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return back()
                ->withErrors(['email' => 'Invalid email or password'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Admin
        if ($user->role === 'admin') {
            if ($user->status !== 'approved') {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account is pending approval.']);
            }
            return redirect()->route('admin.dashboard');
        }

        // Vendor
        if ($user->role === 'vendor') {
            // status must be approved to login (your rule)
            if ($user->status !== 'approved') {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account is pending approval.']);
            }

            // if vendor not verified, send to verification page
            if ($user->vendor_status !== 'approved') {
                return redirect()->route('vendor.verification');
            }

            return redirect()->route('vendor.dashboard');
        }

        // Normal User
        if ($user->status !== 'approved') {
            Auth::logout();
            return back()->withErrors(['email' => 'Your account is pending approval.']);
        }

       return redirect()->intended(route('home'))->with('success', 'Login successful');
    }


    public function registerForm()
    {
        return view('user.pages.register');
    }

    public function registerStore(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'phone'    => ['required', 'string', 'max:15', 'unique:users,phone'],
            'password' => ['required', 'min:6', 'confirmed'],
        ]);

        User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'],
            'password' => Hash::make($data['password']),
            'role'     => 'user',
            'status'   => 'approved',
        ]);

        return redirect()->route('login')->with('success', 'Registration successful. Please login.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
