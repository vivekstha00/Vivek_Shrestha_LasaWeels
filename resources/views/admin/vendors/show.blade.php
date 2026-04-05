@extends('admin.layouts.master')

@section('admin-content')
<div class="mb-4">
    <a href="{{ route('admin.vendors.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Vendors
    </a>
    <h2 class="fw-bold mb-1">Vendor Details</h2>
    <p class="text-muted mb-0">Review vendor information, documents and manage status</p>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="d-grid gap-4">

        <!-- Profile Header -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-start gap-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 72px; height: 72px; background: linear-gradient(135deg, #3b82f6, #2563eb); color: #fff; font-size: 1.8rem; font-weight: 700;">
                        {{ strtoupper(substr($profile->company_name ?? 'V', 0, 1)) }}
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h4 class="fw-bold mb-1">{{ $profile->company_name }}</h4>
                                <div class="text-muted small">
                                    <i class="fa-solid fa-user me-1"></i> {{ $profile->contact_person }}
                                </div>
                            </div>
                            @php
                                $statusConfig = match($profile->status) {
                                    'approved' => ['class' => 'bg-success', 'icon' => 'fa-circle-check'],
                                    'pending' => ['class' => 'bg-warning text-dark', 'icon' => 'fa-clock'],
                                    'resubmit' => ['class' => 'bg-info text-dark', 'icon' => 'fa-rotate'],
                                    'rejected' => ['class' => 'bg-danger', 'icon' => 'fa-circle-xmark'],
                                    default => ['class' => 'bg-secondary', 'icon' => 'fa-circle-question'],
                                };
                            @endphp
                            <span class="badge {{ $statusConfig['class'] }} px-3 py-2">
                                <i class="fa-solid {{ $statusConfig['icon'] }} me-1"></i>
                                {{ ucfirst($profile->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <hr class="my-3">

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small"><i class="fa-solid fa-envelope me-1"></i> Email</div>
                        <div class="fw-semibold">{{ $profile->user->email ?? '—' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small"><i class="fa-solid fa-phone me-1"></i> Phone</div>
                        <div class="fw-semibold">{{ $profile->phone ?? '—' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small"><i class="fa-solid fa-calendar me-1"></i> Joined</div>
                        <div class="fw-semibold">{{ $profile->created_at?->format('d M Y') }}</div>
                    </div>
                    <div class="col-12">
                        <div class="text-muted small"><i class="fa-solid fa-location-dot me-1"></i> Business Address</div>
                        <div class="fw-semibold">{{ $profile->business_address ?: '—' }}</div>
                    </div>
                </div>

                @if($profile->remarks)
                    <div class="alert alert-warning mt-3 mb-0">
                        <div class="fw-semibold small mb-1"><i class="fa-solid fa-note-sticky me-1"></i> Remarks</div>
                        {{ $profile->remarks }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Vendor Operations Snapshot -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">
                    <i class="fa-solid fa-chart-column me-2 text-primary"></i> Operations Snapshot
                </h5>

                <div class="row g-3">
                    <div class="col-md-6 col-xl-3">
                        <div class="border rounded-3 p-3 h-100 bg-light-subtle">
                            <div class="text-muted small mb-1">
                                <i class="fa-solid fa-car-side me-1"></i> Total Vehicles
                            </div>
                            <div class="fw-bold fs-4">{{ $totalVehicles }}</div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="border rounded-3 p-3 h-100 bg-light-subtle">
                            <div class="text-muted small mb-1">
                                <i class="fa-solid fa-route me-1"></i> Vehicles on Trip
                            </div>
                            <div class="fw-bold fs-4 text-success">{{ $vehiclesOnTrip }}</div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="border rounded-3 p-3 h-100 bg-light-subtle">
                            <div class="text-muted small mb-1">
                                <i class="fa-solid fa-id-card me-1"></i> Total Drivers
                            </div>
                            <div class="fw-bold fs-4">{{ $totalDrivers }}</div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="border rounded-3 p-3 h-100 bg-light-subtle">
                            <div class="text-muted small mb-1">
                                <i class="fa-solid fa-user-check me-1"></i> Drivers on Trip
                            </div>
                            <div class="fw-bold fs-4 text-success">{{ $driversOnTrip }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vendor Location -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">
                    <i class="fa-solid fa-map-location-dot me-2 text-primary"></i> Vendor Location
                </h5>

                @if($profile->latitude && $profile->longitude)
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="text-muted small">Latitude</div>
                            <div class="fw-semibold">{{ number_format((float) $profile->latitude, 7) }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Longitude</div>
                            <div class="fw-semibold">{{ number_format((float) $profile->longitude, 7) }}</div>
                        </div>
                    </div>

                    <div class="ratio ratio-16x9 rounded-3 overflow-hidden border mb-3">
                        <iframe
                            src="https://maps.google.com/maps?q={{ $profile->latitude }},{{ $profile->longitude }}&z=15&output=embed"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            style="border:0;"
                            allowfullscreen>
                        </iframe>
                    </div>

                    <a href="https://maps.google.com/?q={{ $profile->latitude }},{{ $profile->longitude }}"
                       target="_blank"
                       class="btn btn-outline-primary btn-sm">
                        <i class="fa-solid fa-up-right-from-square me-1"></i> Open in Google Maps
                    </a>
                @else
                    <div class="alert alert-light border mb-0">
                        <i class="fa-solid fa-circle-info me-1"></i>
                        Location coordinates were not provided by this vendor.
                    </div>
                @endif
            </div>
        </div>

        <!-- Documents -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">
                    <i class="fa-solid fa-file-lines me-2 text-primary"></i> Uploaded Documents
                </h5>

                @if($docs->isEmpty())
                    <div class="text-center text-muted py-4">
                        <i class="fa-solid fa-folder-open fa-2x mb-2 d-block opacity-50"></i>
                        No documents uploaded.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 170px;">Document</th>
                                    <th style="min-width: 130px;">Uploaded</th>
                                    <th style="min-width: 120px;">Status</th>
                                    <th style="min-width: 220px;">Remark</th>
                                    <th style="min-width: 360px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($docs as $doc)
                                    @php
                                        $docStatusClass = match($doc->status) {
                                            'approved' => 'bg-success',
                                            'pending' => 'bg-warning text-dark',
                                            'rejected' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <tr>
                                        <td class="fw-semibold">{{ ucwords(str_replace('_', ' ', $doc->type)) }}</td>
                                        <td>{{ $doc->created_at?->format('d M Y') ?? '—' }}</td>
                                        <td>
                                            <span class="badge {{ $docStatusClass }}">{{ ucfirst($doc->status) }}</span>
                                        </td>
                                        <td>{{ $doc->remarks ?: '—' }}</td>
                                        <td>
                                            <div class="d-flex flex-column gap-2">
                                                <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="fa-solid fa-external-link me-1"></i> View Document
                                                </a>

                                                @if($doc->status !== 'approved')
                                                    <form action="{{ route('admin.documents.approve', $doc->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="redirect_to" value="{{ url()->full() }}">
                                                        <button class="btn btn-success btn-sm w-100" type="submit">
                                                            <i class="fa-solid fa-check me-1"></i> Approve
                                                        </button>
                                                    </form>

                                                    <form action="{{ route('admin.documents.reject', $doc->id) }}" method="POST" class="d-flex gap-2">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="redirect_to" value="{{ url()->full() }}">
                                                        <input
                                                            type="text"
                                                            name="remarks"
                                                            class="form-control form-control-sm"
                                                            placeholder="Reason for rejection"
                                                            required
                                                        >
                                                        <button class="btn btn-warning btn-sm" type="submit">
                                                            Reject
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    <!-- Manual Override Actions -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Manual Override Actions</h5>

            <div class="row g-3 align-items-end">
                @if($profile->status !== 'approved')
                    <div class="col-md-4">
                        <form action="{{ route('admin.vendors.approve', $profile->id) }}" method="POST">
                            @csrf
                            <button class="btn btn-success w-100">Approve Vendor</button>
                        </form>
                    </div>
                @endif

                <div class="col-md-8">
                    <form action="{{ route('admin.vendors.reject', $profile->id) }}" method="POST" class="d-flex gap-2">
                        @csrf
                        <input
                            type="text"
                            name="remarks"
                            class="form-control"
                            placeholder="Reason for rejection"
                            required
                        >
                        <button class="btn btn-danger">Reject Vendor</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
