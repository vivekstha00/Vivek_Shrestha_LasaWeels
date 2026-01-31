@extends('vendor.layouts.master')

@section('vendor-content')
<div class="container-fluid">
    <h3 class="mb-3">Vendor Dashboard</h3>

    <div class="card shadow-sm">
        <div class="card-body">
            <p class="mb-1"><strong>Name:</strong> {{ Auth::user()->name }}</p>
            <p class="mb-1"><strong>Email:</strong> {{ Auth::user()->email }}</p>
            <p class="mb-0"><strong>Role:</strong> {{ Auth::user()->role }}</p>
        </div>
    </div>

    <div class="alert alert-info mt-3">
        Vendor panel is ready. Next you can add: My Vehicles, Bookings, Profile.
    </div>
</div>
@endsection
