<?php
namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class VendorDriverController extends Controller
{
    // List all drivers for the vendor
    public function index()
    {
        $drivers = Driver::where('vendor_id', Auth::id())->latest()->paginate(10);
        return view('vendor.pages.drivers.index', compact('drivers'));
    }

    // Add a new driver
    public function create()
    {
        return view('vendor.pages.drivers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15|unique:drivers',
            'license_number' => 'required|string|unique:drivers',
            'availability_status' => 'required|in:available,unavailable',
            'rating' => 'nullable|numeric|min:0|max:5',
            'image' => 'nullable|image|max:1024'
        ]);

        // Handle image upload
        $imagePath = $request->image ? $request->image->store('drivers', 'public') : null;

        Driver::create([
            'vendor_id' => Auth::id(),
            'name' => $request->name,
            'phone' => $request->phone,
            'license_number' => $request->license_number,
            'availability_status' => $request->availability_status,
            'rating' => $request->rating,
            'image' => $imagePath
        ]);

        return redirect()->route('vendor.drivers.index')->with('success', 'Driver added successfully!');
    }

    // Show details for a specific driver
    public function show($driverId)
    {
        $driver = Driver::findOrFail($driverId); // Retrieve the driver by ID
        return view('vendor.pages.drivers.show', compact('driver')); // Return the driver details view
    }

    // Edit the driver details
    public function edit($driverId)
    {
        $driver = Driver::findOrFail($driverId); // Retrieve the driver by ID
        return view('vendor.pages.drivers.edit', compact('driver')); // Return the edit view
    }

    // Update the driver details
    public function update(Request $request, $driverId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15|unique:drivers,phone,' . $driverId,
            'license_number' => 'required|string|unique:drivers,license_number,' . $driverId,
            'availability_status' => 'required|in:available,unavailable',
            'rating' => 'nullable|numeric|min:0|max:5',
            'image' => 'nullable|image|max:1024'
        ]);

        $driver = Driver::findOrFail($driverId); // Find the driver

        // Handle image upload (if any)
        if ($request->hasFile('image')) {
            $imagePath = $request->image->store('drivers', 'public');
            $driver->image = $imagePath;
        }

        // Update driver details
        $driver->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'license_number' => $request->license_number,
            'availability_status' => $request->availability_status,
            'rating' => $request->rating,
        ]);

        return redirect()->route('vendor.drivers.index')->with('success', 'Driver updated successfully!');
    }

    // Assign a driver to a booking
    public function assignDriver(Request $request, $bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        // Ensure booking has no driver assigned already
        if ($booking->driver_id) {
            return back()->withErrors(['driver' => 'This booking already has a driver assigned.']);
        }

        $driver = Driver::findOrFail($request->driver_id);

        // Ensure the driver is not removed
        if ($driver->availability_status !== 'available') {
            return back()->withErrors(['driver' => 'Driver is not available.']);
        }

        // Check if driver has overlapping bookings
        $driverBusy = Booking::where('driver_id', $driver->id)
            ->whereIn('status', ['pending', 'confirmed', 'active'])
            ->where('pickup_datetime', '<=', $booking->drop_datetime)
            ->where('drop_datetime', '>=', $booking->pickup_datetime)
            ->exists();

        if ($driverBusy) {
            return back()->withErrors(['driver' => 'Driver is already booked for this time period.']);
        }

        // Assign the driver to the booking
        $booking->update([
            'driver_id' => $driver->id,
            'status' => 'confirmed',
        ]);

        return redirect()->route('vendor.bookings.index')->with('success', 'Driver assigned to booking!');
    }
}
