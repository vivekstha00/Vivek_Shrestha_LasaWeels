<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\LoyaltyService;

class UserReviewController extends Controller
{
    public function store(Request $request, Booking $booking)
    {
        abort_unless($booking->user_id === Auth::id(), 403);

        $booking->loadMissing(['vehicle', 'driver']);

        if ($booking->status !== 'completed') {
            return back()->with('error', 'Only completed bookings can be reviewed.');
        }

        if ($booking->review) {
            return back()->with('error', 'You have already reviewed this booking.');
        }

        $hasDriver = !is_null($booking->driver_id);

        $validated = $request->validate([
            'overall_rating' => ['required', 'numeric', 'between:1,5'],
            'overall_review' => ['nullable', 'string', 'max:1000'],

            'vehicle_rating' => ['required', 'numeric', 'between:1,5'],
            'vehicle_review' => ['nullable', 'string', 'max:1000'],

            'driver_rating' => [$hasDriver ? 'required' : 'nullable', 'numeric', 'between:1,5'],
            'driver_review' => ['nullable', 'string', 'max:1000'],
        ]);

        Review::create([
            'booking_id'      => $booking->id,
            'user_id'         => Auth::id(),
            'vehicle_id'      => $booking->vehicle_id,
            'vendor_id'       => $booking->vehicle->vendor_id,
            'driver_id'       => $hasDriver ? $booking->driver_id : null,

            'overall_rating'  => $validated['overall_rating'],
            'overall_review'  => $validated['overall_review'] ?? null,

            'vehicle_rating'  => $validated['vehicle_rating'],
            'vehicle_review'  => $validated['vehicle_review'] ?? null,

            'driver_rating'   => $hasDriver ? ($validated['driver_rating'] ?? null) : null,
            'driver_review'   => $hasDriver ? ($validated['driver_review'] ?? null) : null,
        ]);

        if ($hasDriver && !empty($booking->driver_id)) {
            $avgDriverRating = Review::query()
                ->where('driver_id', $booking->driver_id)
                ->whereNotNull('driver_rating')
                ->avg('driver_rating');

            Driver::whereKey($booking->driver_id)->update([
                'rating' => $avgDriverRating !== null ? round((float) $avgDriverRating, 1) : null,
            ]);
        }

        app(LoyaltyService::class)->awardReviewBonus($booking->fresh());

        return back()->with('success', 'Review submitted successfully.');
    }
}
