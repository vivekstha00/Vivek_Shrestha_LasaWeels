{{-- resources/views/user/pages/vehicles/search-results.blade.php --}}
@extends('user.layouts.master')

@section('title', 'Available Vehicles')

@section('user-content')
@php
    $service = $search['service'] ?? request('service', 'self');
    $wheelType = $search['wheel_type'] ?? request('wheel_type', '');
@endphp

<div class="container pb-5" style="padding-top: 2px;"
     x-data="{
        openEdit: false,
        service: @js($service),
        wheelType: @js($wheelType),

        pickup_location: @js($search['pickup_location'] ?? request('pickup_location','')),
        drop_location: @js($search['drop_location'] ?? request('drop_location','')),
        pickup_datetime: @js($search['pickup_datetime'] ?? request('pickup_datetime','')),
        drop_datetime: @js($search['drop_datetime'] ?? request('drop_datetime','')),

        setService(val) {
            if (this.wheelType === '2_wheeler') {
                this.service = 'self';
                return;
            }

            this.service = val;

            if (this.service === 'self') {
                this.pickup_location = 'Pokhara Matepani';
            }
        },

        setWheelType(val) {
            this.wheelType = val;

            if (val === '2_wheeler') {
                this.service = 'self';
            }

            if (this.service === 'self') {
                this.pickup_location = 'Pokhara Matepani';
            }
        }
     }"
     x-cloak
>

    <div class="text-center mb-4">
        <h2 class="fw-bold mb-1">Select Vehicle</h2>
        <div class="text-muted">Choose the best option for your trip</div>
    </div>

    <div class="row g-4 align-items-start">

        {{-- LEFT SIDE --}}
        <div class="col-lg-4">

            {{-- Filters --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">Filters</h5>

                        <a href="{{ route('user.search.vehicles', [
                                'service' => request('service', $service),
                                'wheel_type' => request('wheel_type', $wheelType),
                                'pickup_location' => request('pickup_location', $search['pickup_location'] ?? ''),
                                'drop_location' => request('drop_location', $search['drop_location'] ?? ''),
                                'pickup_datetime' => request('pickup_datetime', $search['pickup_datetime'] ?? ''),
                                'drop_datetime' => request('drop_datetime', $search['drop_datetime'] ?? ''),
                           ]) }}"
                           class="btn btn-outline-secondary w-50 rounded-3">
                            Clear
                        </a>
                    </div>

                    <form method="GET" action="{{ route('user.search.vehicles') }}" class="vstack gap-3">
                        <input type="hidden" name="service" :value="wheelType === '2_wheeler' ? 'self' : service">
                        <input type="hidden" name="pickup_location" :value="pickup_location">
                        <input type="hidden" name="drop_location" :value="drop_location">
                        <input type="hidden" name="pickup_datetime" :value="pickup_datetime">
                        <input type="hidden" name="drop_datetime" :value="drop_datetime">

                        <div>
                            <div>
                                <label class="form-label fw-semibold mb-1">Wheel Type</label>
                                <select name="wheel_type" class="form-select" x-model="wheelType" @change="setWheelType($event.target.value)">
                                    <option value="">All</option>
                                    <option value="4_wheeler" @selected(request('wheel_type')==='4_wheeler')>4 Wheeler</option>
                                    <option value="2_wheeler" @selected(request('wheel_type')==='2_wheeler')>2 Wheeler</option>
                                </select>
                            </div>
                            <label class="form-label fw-semibold mb-1">Vehicle Type</label>
                            <select name="vehicle_type" class="form-select">
                                <option value="">All</option>
                                <option value="car" @selected(request('vehicle_type') === 'car')>Car</option>
                                <option value="suv" @selected(request('vehicle_type') === 'suv')>SUV</option>
                                <option value="pickup" @selected(request('vehicle_type') === 'pickup')>Pickup</option>
                                <option value="jeep" @selected(request('vehicle_type') === 'jeep')>Jeep</option>
                                <option value="van" @selected(request('vehicle_type') === 'van')>Van</option>
                                <option value="ev" @selected(request('vehicle_type') === 'ev')>EV Car</option>
                                <option value="bike" @selected(request('vehicle_type') === 'bike')>Bike</option>
                                <option value="scooter" @selected(request('vehicle_type') === 'scooter')>Scooter</option>
                            </select>
                        </div>

                        <div>
                            <label class="form-label fw-semibold mb-1">Fuel Type</label>
                            <select name="fuel_type" class="form-select">
                                <option value="">All</option>
                                <option value="petrol" @selected(request('fuel_type')==='petrol')>Petrol</option>
                                <option value="diesel" @selected(request('fuel_type')==='diesel')>Diesel</option>
                                <option value="electric" @selected(request('fuel_type')==='electric')>Electric</option>
                            </select>
                        </div>

                        <div x-show="wheelType !== '2_wheeler'" x-cloak>
                            <label class="form-label fw-semibold mb-1">Transmission</label>
                            <select name="transmission" class="form-select">
                                <option value="">All</option>
                                <option value="manual" @selected(request('transmission')==='manual')>Manual</option>
                                <option value="automatic" @selected(request('transmission')==='automatic')>Automatic</option>
                            </select>
                        </div>


                        <div>
                            <label class="form-label fw-semibold mb-1">Price / Day</label>
                            <select name="price_sort" class="form-select">
                                <option value="">Default</option>
                                <option value="low_high" @selected(request('price_sort')==='low_high')>Low → High</option>
                                <option value="high_low" @selected(request('price_sort')==='high_low')>High → Low</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success w-100 fw-semibold">
                            Apply Filters
                        </button>
                    </form>
                </div>
            </div>

            {{-- Booking Summary --}}
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Booking Detail</h5>

                    <div class="small text-muted mb-2">
                        Service:
                        <span class="fw-semibold text-dark"
                              x-text="wheelType === '2_wheeler' ? 'Self Drive' : (service === 'driver' ? 'With Driver' : 'Self Drive')"></span>
                    </div>

                    <div class="mb-2">
                        <div class="fw-semibold" x-text="service === 'self' ? 'From' : 'Pick Up'"></div>
                        <div class="text-muted" x-text="pickup_location || '-'"></div>
                    </div>

                    <div class="mb-2">
                        <div class="fw-semibold" x-text="service === 'self' ? 'To' : 'Drop Off'"></div>
                        <div class="text-muted" x-text="drop_location || '-'"></div>
                    </div>

                    <div class="mb-2">
                        <div class="fw-semibold">Start Date</div>
                        <div class="text-muted" x-text="pickup_datetime || '-'"></div>
                    </div>

                    <div class="mb-3">
                        <div class="fw-semibold">End Date</div>
                        <div class="text-muted" x-text="drop_datetime || '-'"></div>
                    </div>

                    <button type="button" class="btn btn-success w-100 fw-semibold" @click="openEdit = true">
                        Edit Details
                    </button>
                </div>
            </div>

        </div>

        {{-- RIGHT SIDE --}}
        <div class="col-lg-8">

            @if($vehicles->count() === 0)
                <div class="alert alert-warning">
                    No vehicles available for selected dates.
                </div>
            @endif

            @foreach($vehicles as $vehicle)
                @php
                    $pickup = !empty($search['pickup_datetime']) ? \Carbon\Carbon::parse($search['pickup_datetime']) : null;
                    $drop   = !empty($search['drop_datetime']) ? \Carbon\Carbon::parse($search['drop_datetime']) : null;

                    $days = 1;
                    if ($pickup && $drop) {
                        $totalMinutes = max(0, $pickup->diffInMinutes($drop));
                        $minutesPerDay = 24 * 60;
                        $fullDays = intdiv($totalMinutes, $minutesPerDay);
                        $remainingMinutes = $totalMinutes % $minutesPerDay;
                        $graceMinutes = (int) config('vehicle.billing_grace_hours', 2) * 60;

                        if ($remainingMinutes === 0) {
                            $days = max(1, $fullDays);
                        } elseif ($remainingMinutes <= $graceMinutes) {
                            $days = max(1, $fullDays);
                        } else {
                            $days = max(1, $fullDays + 1);
                        }
                    }

                    $pricePerDay = ($vehicle->wheel_type !== '2_wheeler' && $service === 'driver')
                        ? ($vehicle->with_driver_price_per_day ?? $vehicle->price_per_day)
                        : $vehicle->price_per_day;

                    $basePrice = (float) $pricePerDay * $days;
                    $durationDiscountPercent = (float) $vehicle->getDurationDiscountPercent($days);
                    $durationDiscountAmount = round($basePrice * ($durationDiscountPercent / 100), 2);
                    $estimated = max(0, $basePrice - $durationDiscountAmount);

                    $img = $vehicle->primaryImage ?? $vehicle->images->first();
                @endphp

                <div class="card mb-4 shadow-sm border-0">
                    <div class="row g-0 align-items-center">

                        <div class="col-md-4">
                            @if($img)
                                <img
                                    src="{{ asset('storage/' . ltrim($img->path, '/')) }}"
                                    class="img-fluid rounded-start"
                                    style="height:200px; width:100%; object-fit:cover;"
                                    alt="Vehicle Image"
                                >
                            @else
                                <div class="bg-light border rounded-start d-flex align-items-center justify-content-center"
                                     style="height:200px;">
                                    <i class="fa-solid fa-car text-secondary fs-2"></i>
                                </div>
                            @endif
                        </div>

                        <div class="col-md-8">
                            <div class="card-body">
                                <h5 class="card-title fw-bold mb-1">
                                    {{ $vehicle->brand }} {{ $vehicle->model }}
                                </h5>

                                <div class="text-muted small mb-3">
                                    {{ ucfirst(str_replace('_', ' ', $vehicle->wheel_type)) }} •
                                    {{ ucfirst($vehicle->fuel_type) }} •
                                    {{ ucfirst($vehicle->transmission) }} •
                                    {{ $vehicle->wheel_type === '2_wheeler' ? 'Riders' : 'Seats' }} {{ $vehicle->seating_capacity }}
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="text-muted small">Estimated Total</div>
                                        <div class="fs-5 fw-bold text-success">
                                            Rs. {{ number_format($estimated, 2) }}
                                        </div>

                                        @if($durationDiscountPercent > 0)
                                            <div class="small text-success mt-1">
                                                {{ rtrim(rtrim(number_format($durationDiscountPercent, 2), '0'), '.') }}% long booking discount applied
                                            </div>
                                            <div class="small text-muted">
                                                Base: Rs. {{ number_format($basePrice, 2) }}
                                            </div>
                                        @endif
                                    </div>

                                    <a
                                        :href="`{{ route('vehicles.show', $vehicle->id) }}?` + new URLSearchParams({
                                            service: wheelType === '2_wheeler' ? 'self' : service,
                                            wheel_type: wheelType,
                                            pickup_location: pickup_location,
                                            drop_location: drop_location,
                                            pickup_datetime: pickup_datetime,
                                            drop_datetime: drop_datetime
                                        }).toString()"
                                        class="btn btn-success px-4"
                                    >
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach

            <div class="d-flex justify-content-center mt-4">
                {{ $vehicles->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>

    {{-- Modify Search Modal --}}
    <div
        x-show="openEdit"
        x-transition.opacity
        class="position-fixed top-0 start-0 w-100 h-100"
        style="z-index: 1050;"
        aria-modal="true"
        role="dialog"
    >
        <div
            class="position-absolute top-0 start-0 w-100 h-100"
            style="background: rgba(0,0,0,.5);"
            @click="openEdit = false"
        ></div>

        <div class="position-relative d-flex align-items-center justify-content-center h-100 p-3">
            <div class="bg-white rounded-4 shadow w-100" style="max-width: 900px;">
                <div class="d-flex justify-content-between align-items-center p-4 pb-2">
                    <h5 class="fw-bold m-0">Modify Search</h5>
                    <button type="button" class="btn-close" @click="openEdit=false"></button>
                </div>

                <form method="GET" action="{{ route('user.search.vehicles') }}">
                    <div class="p-4 pt-2">

                        <div class="d-flex gap-2 mb-3">
                            <button type="button"
                                    class="btn"
                                    :class="service==='self' ? 'btn-success' : 'btn-outline-success'"
                                    @click="setService('self')">
                                <i class="fa-solid fa-car me-2"></i> Self Drive
                            </button>

                            <button type="button"
                                    class="btn"
                                    :class="service==='driver' ? 'btn-success' : 'btn-outline-success'"
                                    @click="setService('driver')"
                                    :disabled="wheelType === '2_wheeler'">
                                <i class="fa-solid fa-user-tie me-2"></i> With Driver
                            </button>
                        </div>

                        <template x-if="wheelType === '2_wheeler'">
                            <div class="alert alert-light border rounded-3 mb-3">
                                <strong>2 Wheeler:</strong> Self-drive only
                            </div>
                        </template>

                        <input type="hidden" name="service" :value="wheelType === '2_wheeler' ? 'self' : service">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold"
                                       x-text="service==='self' ? 'From' : 'Pickup Location'"></label>

                                <input type="text"
                                       class="form-control"
                                       name="pickup_location"
                                       x-model="pickup_location"
                                       :readonly="service==='self'"
                                       :class="service==='self' ? 'bg-light' : ''"
                                       required>

                                <div class="form-text" x-show="service==='self'">
                                    Self drive pickup is fixed.
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold"
                                       x-text="service==='self' ? 'To' : 'Drop Location'"></label>

                                <input type="text"
                                       class="form-control"
                                       name="drop_location"
                                       x-model="drop_location"
                                       required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold"
                                       x-text="service==='self' ? 'From Date & Time' : 'Pickup Date & Time'"></label>

                                <input type="datetime-local"
                                       class="form-control"
                                       name="pickup_datetime"
                                       x-model="pickup_datetime"
                                       required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold"
                                       x-text="service==='self' ? 'To Date & Time' : 'Drop Date & Time'"></label>

                                <input type="datetime-local"
                                       class="form-control"
                                       name="drop_datetime"
                                       x-model="drop_datetime"
                                       required>
                            </div>
                        </div>

                    </div>

                    <div class="d-flex justify-content-end gap-2 p-4 pt-0">
                        <button type="button" class="btn btn-outline-danger" @click="openEdit=false">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-success px-4">
                            Search
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none !important;}</style>
@endpush
