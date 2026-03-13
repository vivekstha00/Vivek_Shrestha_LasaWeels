<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class VendorReviewController extends Controller
{
    public function index()
    {
        $vendorId = Auth::id();

        $reviews = Review::with(['booking', 'user', 'vehicle', 'driver'])
            ->whereHas('vehicle', function ($query) use ($vendorId) {
                $query->where('vendor_id', $vendorId);
            })
            ->latest()
            ->paginate(10);

        $totalReviews = Review::whereHas('vehicle', function ($query) use ($vendorId) {
            $query->where('vendor_id', $vendorId);
        })->count();

        $avgVehicleRating = Review::whereHas('vehicle', function ($query) use ($vendorId) {
            $query->where('vendor_id', $vendorId);
        })->avg('vehicle_rating');

        $avgOverallRating = Review::whereHas('vehicle', function ($query) use ($vendorId) {
            $query->where('vendor_id', $vendorId);
        })->avg('overall_rating');

        return view('vendor.pages.reviews.index', compact(
            'reviews',
            'totalReviews',
            'avgVehicleRating',
            'avgOverallRating'
        ));
    }

    public function show(Review $review)
    {
        $vendorId = Auth::id();

        abort_unless($review->vehicle && $review->vehicle->vendor_id === $vendorId, 403);

        $review->load(['booking', 'user', 'vehicle', 'driver']);

        return view('vendor.pages.reviews.show', compact('review'));
    }
}
