<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\LoyaltyService;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class UserProfileController extends Controller
{

    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $loyaltyService = app(LoyaltyService::class);

        $bookingsToComplete = Booking::where('user_id', $user->id)
            ->whereIn('status', ['confirmed', 'active'])
            ->where('drop_datetime', '<', Carbon::now())
            ->get();

        foreach ($bookingsToComplete as $bookingToComplete) {
            $bookingToComplete->update([
                'status' => 'completed',
            ]);

            $loyaltyService->awardCompletedBookingPoints($bookingToComplete->fresh());
        }

        $documents = $user->documents()
            ->latest()
            ->get()
            ->keyBy('type');

        $baseQuery = Booking::query()->where('user_id', $user->id);

        $totalBookings = (clone $baseQuery)->count();
        $confirmedBookings = (clone $baseQuery)->where('status', 'confirmed')->count();
        $completedBookings = (clone $baseQuery)->where('status', 'completed')->count();
        $cancelledBookings = (clone $baseQuery)->where('status', 'cancelled')->count();

        $bookings = Booking::query()
            ->where('user_id', $user->id)
            ->with(['vehicle', 'driver', 'review'])
            ->latest()
            ->paginate(10);

        $selfDriveVerified = $user->hasApprovedSelfDriveDocuments();
        $selfDriveVerificationStatus = $user->selfDriveVerificationStatus();

        $loyaltyAccount = $user->loyaltyAccount;
        $loyaltyTransactions = $user->loyaltyTransactions()
            ->latest()
            ->take(5)
            ->get();

        return view('user.pages.profile.index', compact(
            'user',
            'documents',
            'bookings',
            'totalBookings',
            'confirmedBookings',
            'completedBookings',
            'cancelledBookings',
            'selfDriveVerified',
            'selfDriveVerificationStatus',
            'loyaltyAccount',
            'loyaltyTransactions'
        ));
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
