<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorUserController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');
        $vendorId = Auth::id();

        $users = User::query()
            ->where('role', 'user')
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%");
                });
            })
            ->withCount([
                'bookings as vendor_bookings_count' => function ($query) use ($vendorId) {
                    $query->whereHas('vehicle', function ($vehicleQuery) use ($vendorId) {
                        $vehicleQuery->where('vendor_id', $vendorId);
                    });
                },
            ])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $userIds = $users->getCollection()->pluck('id');

        if ($userIds->isNotEmpty()) {
            $latestBookingIdsSubquery = Booking::query()
                ->selectRaw('MAX(bookings.id)')
                ->whereIn('user_id', $userIds)
                ->whereHas('vehicle', function ($vehicleQuery) use ($vendorId) {
                    $vehicleQuery->where('vendor_id', $vendorId);
                })
                ->groupBy('user_id');

            $latestBookings = Booking::query()
                ->with([
                    'vehicle:id,title,brand,model',
                    'payment:id,booking_id,status',
                ])
                ->whereIn('id', $latestBookingIdsSubquery)
                ->get()
                ->keyBy('user_id');

            $users->getCollection()->transform(function (User $user) use ($latestBookings) {
                $user->setRelation('latestVendorBooking', $latestBookings->get($user->id));

                return $user;
            });
        }

        return view('vendor.pages.users.index', compact('users', 'q'));
    }

    public function show(User $user)
    {
        abort_unless($user->role === 'user', 404);

        $vendorId = Auth::id();

        $bookingQuery = Booking::query()
            ->where('user_id', $user->id)
            ->whereHas('vehicle', function ($query) use ($vendorId) {
                $query->where('vendor_id', $vendorId);
            });

        $totalBookings = (clone $bookingQuery)->count();
        $completedBookings = (clone $bookingQuery)
            ->where('status', 'completed')
            ->count();

        $ongoingBookings = (clone $bookingQuery)
            ->whereIn('status', ['pending', 'confirmed', 'active', 'ongoing'])
            ->count();

        $latestBooking = (clone $bookingQuery)
            ->with(['vehicle', 'payment'])
            ->latest('pickup_datetime')
            ->first();

        $lastThreeBookings = (clone $bookingQuery)
            ->with(['vehicle', 'payment'])
            ->latest('pickup_datetime')
            ->limit(3)
            ->get();

        $requiredDocumentTypes = $user->requiredSelfDriveDocuments();
        $verificationDocuments = $user->documents()
            ->where('purpose', 'user_verification')
            ->whereIn('type', $requiredDocumentTypes)
            ->get()
            ->keyBy('type');

        $docStatus = $user->selfDriveVerificationStatus() === 'approved'
            ? 'Verified'
            : 'Not Verified';

        return view('vendor.pages.users.show', compact(
            'user',
            'totalBookings',
            'completedBookings',
            'ongoingBookings',
            'docStatus',
            'latestBooking',
            'lastThreeBookings',
            'requiredDocumentTypes',
            'verificationDocuments'
        ));
    }
}
