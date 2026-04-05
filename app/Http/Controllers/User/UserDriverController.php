<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class UserDriverController extends Controller
{
    // Show available drivers for a specific vehicle booking
    public function availableDrivers(Request $request)
    {
        $vehicleId       = $request->get('vehicle_id');
        $pickupDatetime  = $request->get('pickup_datetime');
        $dropDatetime    = $request->get('drop_datetime');
        $pickupLocation  = $request->get('pickup_location');
        $dropLocation    = $request->get('drop_location');
        $service         = $request->get('service', 'driver');

        $vehicle = null;
        if (!empty($vehicleId)) {
            $vehicle = Vehicle::find($vehicleId);
        }

        $query = Driver::where('availability_status', 'available')
            ->where('status', 'approved');

        if ($vehicle) {
            $query->where('vendor_id', $vehicle->vendor_id);
        }

        // Filter out drivers who have overlapping bookings
        if ($pickupDatetime && $dropDatetime) {
            $pickup = \Carbon\Carbon::parse($pickupDatetime);
            $drop   = \Carbon\Carbon::parse($dropDatetime);

            $query->whereDoesntHave('bookings', function ($q) use ($pickup, $drop) {
                $q->whereIn('status', ['pending', 'confirmed', 'active'])
                  ->where('pickup_datetime', '<=', $drop)
                  ->where('drop_datetime', '>=', $pickup);
            });
        }

        $availableDrivers = $query->get();

        // Pass booking context so we can link back to checkout
        $bookingData = [
            'vehicle_id'       => $vehicleId,
            'pickup_datetime'  => $pickupDatetime,
            'drop_datetime'    => $dropDatetime,
            'pickup_location'  => $pickupLocation,
            'drop_location'    => $dropLocation,
            'service'          => $service,
        ];

        return view('user.pages.driver.index', compact('availableDrivers', 'bookingData'));
    }

    // Show driver details
    public function showDriver($driverId)
    {
        $vehicle = null;
        if (request()->has('vehicle_id')) {
            $vehicle = Vehicle::find(request()->get('vehicle_id'));
        }

        $driverQuery = Driver::query();

        if ($vehicle) {
            $driverQuery->where('vendor_id', $vehicle->vendor_id);
        }

        $driver = $driverQuery->findOrFail($driverId);

        $bookingData = [
            'vehicle_id'       => request()->get('vehicle_id'),
            'pickup_datetime'  => request()->get('pickup_datetime'),
            'drop_datetime'    => request()->get('drop_datetime'),
            'pickup_location'  => request()->get('pickup_location'),
            'drop_location'    => request()->get('drop_location'),
            'service'          => request()->get('service', 'driver'),
        ];

        return view('user.pages.driver.show', compact('driver', 'vehicle', 'bookingData'));
    }
}
