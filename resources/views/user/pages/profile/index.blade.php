@extends('user.layouts.master')

@section('user-content')

<div class="container py-4">

    {{-- Success message --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif


    {{-- ================= PROFILE SECTION ================= --}}
    <div class="card mb-4">
        <div class="card-header">
            <h4>My Profile</h4>
        </div>

        <div class="card-body">

            <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">

                    {{-- Profile Image --}}
                    <div class="col-md-3 text-center">

                        <img
                            src="{{ $user->profile_image ? asset('storage/'.$user->profile_image) : asset('images/default-avatar.png') }}"
                            class="img-fluid rounded-circle mb-3"
                            style="width:150px;height:150px;object-fit:cover;"
                        >

                        <input type="file" name="profile_image" class="form-control">
                    </div>


                    {{-- Profile Fields --}}
                    <div class="col-md-9">

                        <div class="mb-3">
                            <label>Name</label>
                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                value="{{ old('name', $user->name) }}"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label>Email</label>
                            <input
                                type="email"
                                class="form-control"
                                value="{{ $user->email }}"
                                disabled
                            >
                        </div>

                        <div class="mb-3">
                            <label>Phone</label>
                            <input
                                type="text"
                                name="phone"
                                class="form-control"
                                value="{{ old('phone', $user->phone) }}"
                            >
                        </div>

                        <div class="mb-3">
                            <label>Address</label>
                            <input
                                type="text"
                                name="address"
                                class="form-control"
                                value="{{ old('address', $user->address) }}"
                            >
                        </div>

                        <button class="btn btn-primary">
                            Update Profile
                        </button>

                    </div>
                </div>

            </form>

        </div>
    </div>



    {{-- ================= DOCUMENT SECTION ================= --}}
    <div class="card mb-4">

        <div class="card-header">
            <h4>Documents</h4>
        </div>

        <div class="card-body">

            @php
                $license = $documents['license'] ?? null;
                $citizenship = $documents['citizenship'] ?? null;
            @endphp


            {{-- ===== LICENSE ===== --}}
            <div class="border p-3 mb-3">

                <h5>
                    Driving License

                    @if($license)
                        <span class="badge
                            {{ $license->status == 'approved' ? 'bg-success' :
                               ($license->status == 'rejected' ? 'bg-danger' : 'bg-warning') }}">
                            {{ ucfirst($license->status) }}
                        </span>
                    @endif
                </h5>

                @if($license)
                    <p>
                        <strong>Document Number:</strong> {{ $license->document_number }}
                    </p>

                    <a href="{{ asset('storage/'.$license->file_path) }}" target="_blank" class="btn btn-sm btn-info">
                        View Document
                    </a>
                @endif


                {{-- Upload / Update form --}}
                <form
                    action="{{ $license ? route('user.documents.update', $license) : route('user.documents.store') }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="mt-3"
                >

                    @csrf
                    @if($license)
                        @method('PUT')
                    @endif

                    <input type="hidden" name="type" value="license">

                    <div class="row">

                        <div class="col-md-3">
                            <input type="file" name="file" class="form-control" {{ $license ? '' : 'required' }}>
                        </div>

                        <div class="col-md-3">
                            <input
                                type="text"
                                name="document_number"
                                class="form-control"
                                placeholder="License Number"
                                value="{{ $license->document_number ?? '' }}"
                            >
                        </div>

                        <div class="col-md-2">
                            <input
                                type="date"
                                name="issued_at"
                                class="form-control"
                                value="{{ $license->issued_at ?? '' }}"
                            >
                        </div>

                        <div class="col-md-2">
                            <input
                                type="date"
                                name="expires_at"
                                class="form-control"
                                value="{{ $license->expires_at ?? '' }}"
                            >
                        </div>

                        <div class="col-md-2">
                            <button class="btn btn-primary w-100">
                                {{ $license ? 'Update' : 'Upload' }}
                            </button>
                        </div>

                    </div>

                </form>

            </div>



            {{-- ===== CITIZENSHIP ===== --}}
            <div class="border p-3">

                <h5>
                    Citizenship

                    @if($citizenship)
                        <span class="badge
                            {{ $citizenship->status == 'approved' ? 'bg-success' :
                               ($citizenship->status == 'rejected' ? 'bg-danger' : 'bg-warning') }}">
                            {{ ucfirst($citizenship->status) }}
                        </span>
                    @endif
                </h5>

                @if($citizenship)
                    <p>
                        <strong>Document Number:</strong> {{ $citizenship->document_number }}
                    </p>

                    <a href="{{ asset('storage/'.$citizenship->file_path) }}" target="_blank" class="btn btn-sm btn-info">
                        View Document
                    </a>
                @endif


                <form
                    action="{{ $citizenship ? route('user.documents.update', $citizenship) : route('user.documents.store') }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="mt-3"
                >

                    @csrf
                    @if($citizenship)
                        @method('PUT')
                    @endif

                    <input type="hidden" name="type" value="citizenship">

                    <div class="row">

                        <div class="col-md-4">
                            <input type="file" name="file" class="form-control" {{ $citizenship ? '' : 'required' }}>
                        </div>

                        <div class="col-md-4">
                            <input
                                type="text"
                                name="document_number"
                                class="form-control"
                                placeholder="Citizenship Number"
                                value="{{ $citizenship->document_number ?? '' }}"
                            >
                        </div>

                        <div class="col-md-4">
                            <button class="btn btn-primary w-100">
                                {{ $citizenship ? 'Update' : 'Upload' }}
                            </button>
                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>




    {{-- ================= BOOKING HISTORY ================= --}}
    <div class="card">

        <div class="card-header">
            <h4>Booking History</h4>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Vehicle</th>
                            <th>Driver</th>
                            <th>Pickup Date</th>
                            <th>Return Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($bookings as $booking)

                            <tr>

                                <td>#{{ $booking->id }}</td>

                                <td>
                                    {{ $booking->vehicle->name ?? 'N/A' }}
                                </td>

                                <td>
                                    {{ $booking->driver->name ?? 'Self Drive' }}
                                </td>

                                <td>
                                    {{ $booking->pickup_date ?? '-' }}
                                </td>

                                <td>
                                    {{ $booking->return_date ?? '-' }}
                                </td>

                                <td>
                                    <span class="badge
                                        {{ $booking->status == 'completed' ? 'bg-success' :
                                           ($booking->status == 'cancelled' ? 'bg-danger' : 'bg-warning') }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="text-center">
                                    No bookings found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- Pagination --}}
            <div class="mt-3">
                {{ $bookings->links() }}
            </div>

        </div>

    </div>

</div>

@endsection
