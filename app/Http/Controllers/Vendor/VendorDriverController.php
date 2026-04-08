<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Booking;
use App\Services\VendorSubscriptionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VendorDriverController extends Controller
{
    public function index()
    {
        $drivers = Driver::query()
            ->where('vendor_id', Auth::id())
            ->with(['latestBooking.user:id,name', 'latestBooking.vehicle:id,title,brand,model'])
            ->latest()
            ->paginate(10);

        $subscriptionSummary = app(VendorSubscriptionService::class)->getSummary(Auth::id());

        return view('vendor.pages.drivers.index', compact('drivers', 'subscriptionSummary'));
    }

    public function create()
    {
        $subscriptionService = app(VendorSubscriptionService::class);
        $subscriptionSummary = $subscriptionService->getSummary(Auth::id());
        $canAddDriver = $subscriptionService->canAddDriver(Auth::id());

        return view('vendor.pages.drivers.create', compact('subscriptionSummary', 'canAddDriver'));
    }

    public function store(Request $request)
    {
        $vendorId = Auth::id();
        $subscriptionService = app(VendorSubscriptionService::class);

        if (! $subscriptionService->canAddDriver($vendorId)) {
            return redirect()
                ->route('vendor.drivers.index')
                ->with('error', 'Your current plan allows only ' . $subscriptionService->getDriverLimit($vendorId) . ' active drivers. Upgrade your subscription to add more drivers.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => [
                'nullable',
                'string',
                'max:15',
                Rule::unique('drivers', 'phone')->where(fn ($query) => $query->where('vendor_id', $vendorId)),
            ],
            'license_number' => [
                'required',
                'string',
                Rule::unique('drivers', 'license_number')->where(fn ($query) => $query->where('vendor_id', $vendorId)),
            ],
            'availability_status' => 'required|in:available,unavailable',
            'rating' => 'nullable|numeric|min:0|max:5',
            'image' => 'nullable|image|max:1024'
        ]);

        $imagePath = $request->image ? $request->image->store('drivers', 'public') : null;

        Driver::create([
            'vendor_id' => $vendorId,
            'name' => $request->name,
            'phone' => $request->phone,
            'license_number' => $request->license_number,
            'availability_status' => $request->availability_status,
            'rating' => $request->rating,
            'image' => $imagePath
        ]);

        return redirect()->route('vendor.drivers.index')->with('success', 'Driver added successfully!');
    }

    public function show($driverId)
    {
        $driver = Driver::where('vendor_id', Auth::id())->findOrFail($driverId);
        return view('vendor.pages.drivers.show', compact('driver'));
    }

    public function edit($driverId)
    {
        $driver = Driver::where('vendor_id', Auth::id())->findOrFail($driverId);
        return view('vendor.pages.drivers.edit', compact('driver'));
    }

    public function update(Request $request, $driverId)
    {
        $vendorId = Auth::id();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => [
                'nullable',
                'string',
                'max:15',
                Rule::unique('drivers', 'phone')
                    ->where(fn ($query) => $query->where('vendor_id', $vendorId))
                    ->ignore($driverId),
            ],
            'license_number' => [
                'required',
                'string',
                Rule::unique('drivers', 'license_number')
                    ->where(fn ($query) => $query->where('vendor_id', $vendorId))
                    ->ignore($driverId),
            ],
            'availability_status' => 'required|in:available,unavailable',
            'rating' => 'nullable|numeric|min:0|max:5',
            'image' => 'nullable|image|max:1024'
        ]);

        $driver = Driver::where('vendor_id', Auth::id())->findOrFail($driverId);

        if ($request->hasFile('image')) {
            $imagePath = $request->image->store('drivers', 'public');
            $driver->image = $imagePath;
        }

        $driver->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'license_number' => $request->license_number,
            'availability_status' => $request->availability_status,
            'rating' => $request->rating,
        ]);

        return redirect()->route('vendor.drivers.index')->with('success', 'Driver updated successfully!');
    }

    public function assignDriver(Request $request, $bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        if ($booking->driver_id) {
            return back()->withErrors(['driver' => 'This booking already has a driver assigned.']);
        }

        $driver = Driver::where('vendor_id', Auth::id())->findOrFail($request->driver_id);

        if ($driver->availability_status !== 'available') {
            return back()->withErrors(['driver' => 'Driver is not available.']);
        }

        $driverBusy = Booking::where('driver_id', $driver->id)
            ->whereIn('status', ['pending', 'confirmed', 'active'])
            ->where('pickup_datetime', '<=', $booking->drop_datetime)
            ->where('drop_datetime', '>=', $booking->pickup_datetime)
            ->exists();

        if ($driverBusy) {
            return back()->withErrors(['driver' => 'Driver is already booked for this time period.']);
        }

        $booking->update([
            'driver_id' => $driver->id,
            'status' => 'confirmed',
        ]);

        return redirect()->route('vendor.bookings.index')->with('success', 'Driver assigned to booking!');
    }
}
