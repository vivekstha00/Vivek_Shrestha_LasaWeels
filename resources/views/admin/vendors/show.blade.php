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

<div class="row g-4">
    <!-- Left Column -->
    <div class="col-lg-8">

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
                </div>

                @if($profile->remarks)
                    <div class="alert alert-warning mt-3 mb-0">
                        <div class="fw-semibold small mb-1"><i class="fa-solid fa-note-sticky me-1"></i> Remarks</div>
                        {{ $profile->remarks }}
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

                @forelse($docs as $index => $doc)
                    <div class="border rounded-3 p-3 mb-3" style="background: #faf8f5;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold mb-0 text-capitalize">{{ $doc->type }}</h6>
                                <small class="text-muted">Uploaded {{ $doc->created_at?->format('d M Y') }}</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                @php
                                    $docStatusClass = match($doc->status) {
                                        'approved' => 'bg-success',
                                        'pending' => 'bg-warning text-dark',
                                        'rejected' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $docStatusClass }}">{{ ucfirst($doc->status) }}</span>
                                <a href="{{ asset('storage/'.$doc->file_path) }}"
                                   target="_blank"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-external-link me-1"></i> Open
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        <i class="fa-solid fa-folder-open fa-2x mb-2 d-block opacity-50"></i>
                        No documents uploaded.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Right Column: Actions -->
    <div class="col-lg-4">

        <!-- Quick Actions -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">
                    <i class="fa-solid fa-bolt me-2 text-primary"></i> Actions
                </h5>

                @if($profile->status === 'pending')
                    <form action="{{ route('admin.vendors.approve', $profile->id) }}"
                          method="POST" class="mb-3">
                        @csrf
                        <button class="btn btn-success w-100">
                            <i class="fa-solid fa-check me-1"></i> Approve Vendor
                        </button>
                    </form>
                @endif

                <!-- Request Resubmit -->
                <div class="border rounded-3 p-3 mb-3" style="background: #fffbeb;">
                    <h6 class="fw-bold mb-2 text-warning">
                        <i class="fa-solid fa-rotate me-1"></i> Request Resubmission
                    </h6>
                    <form action="{{ route('admin.vendors.resubmit', $profile->id) }}" method="POST">
                        @csrf
                        <textarea name="remarks" class="form-control mb-2" rows="2"
                                  placeholder="Reason for resubmission..." required></textarea>
                        <button class="btn btn-warning btn-sm w-100">Request Resubmit</button>
                    </form>
                </div>

                <!-- Reject -->
                <div class="border rounded-3 p-3" style="background: #fef2f2;">
                    <h6 class="fw-bold mb-2 text-danger">
                        <i class="fa-solid fa-ban me-1"></i> Reject Vendor
                    </h6>
                    <form action="{{ route('admin.vendors.reject', $profile->id) }}" method="POST">
                        @csrf
                        <textarea name="remarks" class="form-control mb-2" rows="2"
                                  placeholder="Reason for rejection..." required></textarea>
                        <button class="btn btn-danger btn-sm w-100">Reject Vendor</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
