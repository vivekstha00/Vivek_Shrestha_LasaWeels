<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;
use App\Notifications\WelcomeUserNotification;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    private const OTP_EXPIRY_MINUTES = 10;

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

        /** @var \App\Models\User|null $user */
        $user = User::where('email', $credentials['email'])->first();

        if ($user?->isGoogleOnlyAccount()) {
            return back()
                ->withErrors([
                    'email' => 'This account was created with Google. Please sign in with Google or use Forgot Password to create a password.',
                ])
                ->onlyInput('email');
        }

            if (!Auth::attempt($credentials, $request->boolean('remember'))) {
                return back()
                    ->with('error', 'Invalid email or password')
                    ->onlyInput('email');
            }

        $request->session()->regenerate();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Admin
        if ($user->role === 'admin') {
            if ($user->status !== 'approved') {
                Auth::logout();
                return back()->with('error', 'Your account is pending approval.');
            }
            return redirect()->route('admin.dashboard');
        }

        // Vendor
        if ($user->role === 'vendor') {
            // Check if user is active (not suspended)
            if (!in_array($user->status, ['active', 'approved'])) {
                Auth::logout();
                return back()->with('error', 'Your account is pending approval or suspended.');
            }

            // if vendor not verified, send to verification page
            if ($user->vendor_status !== 'approved') {
                return redirect()
                    ->route('vendor.verification')
                    ->with('error', 'Please complete the document verification process first.');
            }

            return redirect()->route('vendor.dashboard');
        }

        // Normal User
        if ($user->status !== 'approved') {
            Auth::logout();
            return back()->with('error', 'Your account is pending approval.');
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

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'],
            'password' => Hash::make($data['password']),
            'role'     => 'user',
            'status'   => 'approved',
        ]);

        try {
            $user->notify(new WelcomeUserNotification());
        } catch (Throwable $exception) {
            Log::warning('Welcome email notification failed after registration.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $exception->getMessage(),
            ]);
        }

        return redirect()->route('login')->with('success', 'Registration successful. Please login.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function forgotPasswordForm()
    {
        if (Auth::check()) {
            return redirect()
                ->route('user.profile.edit')
                ->with('error', 'You are already logged in. Use the profile security section to send OTP.');
        }

        return view('user.pages.forgot-password');
    }

    public function sendPasswordResetOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $email = $validated['email'];
        /** @var \App\Models\User|null $user */
        $user = User::where('email', $email)->first();

        $this->issuePasswordResetOtp($email);

        return redirect()
            ->route('password.otp.form', ['email' => $email])
            ->with('success', $user?->isGoogleOnlyAccount()
                ? 'We sent a 6-digit OTP to your email so you can create a password.'
                : 'We sent a 6-digit OTP to your email.');
    }

    public function sendAuthenticatedPasswordResetOtp(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $email = $user->email;
        $this->issuePasswordResetOtp($email);

        return redirect()
            ->route('password.otp.form', ['email' => $email])
            ->with('success', $user->isGoogleOnlyAccount()
                ? 'OTP sent to your registered email so you can create a password.'
                : 'OTP sent to your registered email.');
    }

    public function otpVerificationForm(Request $request)
    {
        $email = $request->query('email');

        if (Auth::check() && $email && Auth::user()->email !== $email) {
            return redirect()
                ->route('user.profile.edit')
                ->with('error', 'You can only verify password reset for your own account.');
        }

        if (!$email) {
            if (Auth::check()) {
                $email = Auth::user()->email;
            }
        }

        if (!$email) {
            return redirect()->route('password.forgot')->with('error', 'Please enter your email first.');
        }

        /** @var \App\Models\User|null $user */
        $user = User::where('email', $email)->first();
        $isPasswordCreation = $user?->isGoogleOnlyAccount() ?? false;

        return view('user.pages.verify-otp', compact('email', 'isPasswordCreation'));
    }

    public function verifyPasswordResetOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'otp' => ['required', 'digits:6'],
        ]);

        /** @var \App\Models\User|null $user */
        $user = User::where('email', $validated['email'])->first();

        if (Auth::check() && Auth::user()->email !== $validated['email']) {
            return back()->with('error', 'You can only reset password for your own account.');
        }

        $resetRow = DB::table('password_reset_tokens')->where('email', $validated['email'])->first();

        if (!$resetRow) {
            return back()->with('error', 'No OTP request found. Please request a new OTP.');
        }

        $isExpired = Carbon::parse($resetRow->created_at)->addMinutes(self::OTP_EXPIRY_MINUTES)->isPast();
        if ($isExpired) {
            DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();
            return back()->with('error', 'OTP expired. Please request a new one.');
        }

        if (!Hash::check($validated['otp'], $resetRow->token)) {
            return back()->with('error', 'Invalid OTP. Please try again.');
        }

        session([
            'password_reset_verified_email' => $validated['email'],
            'password_reset_verified_at' => now()->timestamp,
        ]);

        return redirect()
            ->route('password.reset.form')
            ->with('success', $user?->isGoogleOnlyAccount()
                ? 'OTP verified. Create your password.'
                : 'OTP verified. Set your new password.');
    }

    public function resetPasswordForm()
    {
        $verifiedEmail = session('password_reset_verified_email');

        if (!$verifiedEmail) {
            return redirect()->route('password.forgot')->with('error', 'Please verify OTP first.');
        }

        /** @var \App\Models\User|null $user */
        $user = User::where('email', $verifiedEmail)->first();
        $isPasswordCreation = $user?->isGoogleOnlyAccount() ?? false;

        return view('user.pages.reset-password', [
            'email' => $verifiedEmail,
            'isPasswordCreation' => $isPasswordCreation,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $verifiedEmail = session('password_reset_verified_email');
        $verifiedAt = session('password_reset_verified_at');

        if (!$verifiedEmail || !$verifiedAt || Carbon::createFromTimestamp($verifiedAt)->addMinutes(self::OTP_EXPIRY_MINUTES)->isPast()) {
            session()->forget(['password_reset_verified_email', 'password_reset_verified_at']);
            return redirect()->route('password.forgot')->with('error', 'Reset session expired. Please verify OTP again.');
        }

        $validated = $request->validate([
            'email' => ['required', 'email', 'in:' . $verifiedEmail],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if (Auth::check() && Auth::user()->email !== $validated['email']) {
            return back()->with('error', 'You can only reset password for your own account.');
        }

        /** @var \App\Models\User $user */
        $user = User::where('email', $validated['email'])->firstOrFail();
        $wasGoogleOnlyAccount = $user->isGoogleOnlyAccount();

        $user->update([
            'password' => $validated['password'],
        ]);

        DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();
        session()->forget(['password_reset_verified_email', 'password_reset_verified_at']);

        $successMessage = $wasGoogleOnlyAccount
            ? 'Password created successfully. You can now sign in with Google or email/password.'
            : 'Password reset successful. Please login.';

        if (Auth::check() && Auth::user()->email === $validated['email']) {
            return redirect()->route('user.profile')->with('success', $successMessage);
        }

        return redirect()->route('login')->with('success', $successMessage);
    }

    private function issuePasswordResetOtp(string $email): void
    {
        $otp = (string) random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($otp),
                'created_at' => now(),
            ]
        );

        Mail::raw(
            "Your LasaWheels password reset OTP is {$otp}. This OTP is valid for " . self::OTP_EXPIRY_MINUTES . " minutes.",
            function ($message) use ($email) {
                $message->to($email)->subject('LasaWheels Password Reset OTP');
            }
        );
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')->with('error', 'Google login failed. Please try again.');
        }

        /** @var \App\Models\User|null $user */
        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            if (!$user->google_id) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'auth_provider' => 'google',
                    'name' => $user->name ?: $googleUser->getName(),
                ]);
            }
        } else {
            $user = User::create([
                'name' => $googleUser->getName() ?: 'Google User',
                'email' => $googleUser->getEmail(),
                'phone' => null,
                'password' => null,
                'google_id' => $googleUser->getId(),
                'auth_provider' => 'google',
                'role' => 'user',
                'status' => 'approved',
            ]);
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        // Admin
        if ($user->role === 'admin') {
            if ($user->status !== 'approved') {
                Auth::logout();
                return redirect()->route('login')->with('error', 'Your account is pending approval.');
            }

            return redirect()->route('admin.dashboard');
        }

        // Vendor
        if ($user->role === 'vendor') {
            if (!in_array($user->status, ['active', 'approved'])) {
                Auth::logout();
                return redirect()->route('login')->with('error', 'Your account is pending approval or suspended.');
            }

            if ($user->vendor_status !== 'approved') {
                return redirect()
                    ->route('vendor.verification')
                    ->with('error', 'Please complete the document verification process first.');
            }

            return redirect()->route('vendor.dashboard');
        }

        if ($user->status !== 'approved') {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your account is pending approval.');
        }

        if (!$user->phone) {
            return redirect()->route('user.profile.edit')
                ->with('warning', 'Please complete your phone number and profile details.');
        }

        return redirect()->intended(route('home'))
            ->with('success', 'Logged in with Google successfully.');
    }
}
