<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;

class AdminReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['booking', 'user', 'vehicle.vendor', 'driver'])
            ->latest()
            ->paginate(10);

        $totalReviews = Review::count();
        $avgOverallRating = Review::avg('overall_rating');
        $avgVehicleRating = Review::avg('vehicle_rating');
        $avgDriverRating = Review::whereNotNull('driver_rating')->avg('driver_rating');

        return view('admin.reviews.index', compact(
            'reviews',
            'totalReviews',
            'avgOverallRating',
            'avgVehicleRating',
            'avgDriverRating'
        ));
    }

    public function show(Review $review)
    {
        $review->load(['booking', 'user', 'vehicle.vendor', 'driver']);

        return view('admin.reviews.show', compact('review'));
    }
}
