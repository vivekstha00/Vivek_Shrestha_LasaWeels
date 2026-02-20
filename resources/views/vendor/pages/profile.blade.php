@extends('vendor.layouts.master')

@section('vendor-content')

<div class="container-fluid py-4">

    <h3 class="mb-4">Vendor Profile</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card card-soft p-4" style="max-width:600px;">
        <form method="POST" action="{{ route('vendor.profile.update') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $vendor->name) }}"
                       class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email"
                       value="{{ $vendor->email }}"
                       class="form-control"
                       disabled>
            </div>

            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="text"
                       name="phone"
                       value="{{ old('phone', $vendor->phone ?? '') }}"
                       class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Address</label>
                <input type="text"
                       name="address"
                       value="{{ old('address', $vendor->address ?? '') }}"
                       class="form-control">
            </div>

            <button class="btn btn-primary">Update Profile</button>
        </form>
    </div>

</div>

@endsection
