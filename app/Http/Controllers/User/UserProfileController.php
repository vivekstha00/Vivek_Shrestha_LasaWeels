<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Storage;

class UserProfileController extends Controller
{

    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $documents = $user->documents()
            ->latest()
            ->get()
            ->keyBy('type');

        $bookings = Booking::query()
            ->where('user_id', $user->id)
            ->with(['vehicle', 'driver']) 
            ->latest()
            ->paginate(10);

        return view('user.pages.profile.index', compact('user', 'documents', 'bookings'));
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'phone'         => ['nullable', 'string', 'max:20'],
            'address'       => ['nullable', 'string', 'max:255'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $validated['profile_image'] = $request->file('profile_image')
                ->store('profile-images', 'public');
        } else {
            unset($validated['profile_image']);
        }

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }
}
