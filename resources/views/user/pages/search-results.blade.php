@extends('user.layouts.master')

@section('title', 'Available Vehicles')

@section('user-content')
<div class="container py-5">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h3 class="mb-0">Available Vehicles</h3>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary">Search Again</a>
    </div>

    @if($vehicles->count() === 0)
        <div class="alert alert-warning">
            No vehicles available for your selected date/time.
        </div>
    @else
        <div class="row g-4">
            @foreach($vehicles as $v)
                @php
                    $service = $search['service'];
                    $pricePerDay = $service === 'driver'
                        ? (float) ($v->with_driver_price_per_day ?? $v->price_per_day ?? 0)
                        : (float) ($v->price_per_day ?? 0);
                @endphp

                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <img src="{{ asset('storage/'.$v->image_url) }}" class="card-img-top" style="height:180px;object-fit:cover;" alt="">
                        <div class="card-body">
                            <h5 class="card-title">{{ $v->brand }} {{ $v->model }}</h5>
                            <p class="text-muted mb-2">
                                {{ ucfirst($v->fuel_type) }} • {{ ucfirst($v->transmission) }} • Seats {{ $v->seating_capacity }}
                            </p>

                            <div class="fw-bold">Rs. {{ number_format($pricePerDay, 2) }} / day</div>
                        </div>

                        <div class="card-footer bg-white border-0">
                            {{-- <a class="btn btn-success w-100"
                               href="{{ route('vehicles.show', $v->vehicle_id) . '?' . http_build_query($search) }}">
                                View Details
                            </a> --}}
                            <a class="btn btn-success w-100"
                               href="#">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $vehicles->links() }}
        </div>
    @endif
</div>
@endsection
