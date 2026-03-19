@extends('admin.layouts.master')

@section('admin-content')
<div class="container py-4">
    <div class="mb-4">
        <a href="{{ route('admin.documents.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
            ← Back
        </a>
        <h3 class="fw-bold mb-1">Document Details</h3>
        <p class="text-muted mb-0">Review and verify uploaded document</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="mb-0 fw-bold">{{ ucfirst($document->type) }}</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="mb-3">
                        <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="btn btn-primary">
                            View Uploaded Document
                        </a>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="small text-muted">User</div>
                            <div class="fw-semibold">{{ $document->user->name ?? 'N/A' }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted">Email</div>
                            <div class="fw-semibold">{{ $document->user->email ?? 'N/A' }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted">Document Type</div>
                            <div class="fw-semibold">{{ ucfirst($document->type) }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted">Document Number</div>
                            <div class="fw-semibold">{{ $document->document_number ?: 'N/A' }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted">Issued Date</div>
                            <div class="fw-semibold">{{ $document->issued_at ? $document->issued_at->format('d M Y') : 'N/A' }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted">Expiry Date</div>
                            <div class="fw-semibold">{{ $document->expires_at ? $document->expires_at->format('d M Y') : 'N/A' }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted">Status</div>
                            <div class="fw-semibold">{{ ucfirst($document->status) }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted">Uploaded At</div>
                            <div class="fw-semibold">{{ $document->created_at?->format('d M Y, h:i A') }}</div>
                        </div>
                    </div>

                    @if($document->remarks)
                        <div class="alert alert-warning mt-4 mb-0">
                            <div class="fw-semibold mb-1">Remarks</div>
                            <div>{{ $document->remarks }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="mb-0 fw-bold">Approve Document</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <form action="{{ route('admin.documents.approve', $document) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <button type="submit" class="btn btn-success w-100"
                            {{ $document->status === 'approved' ? 'disabled' : '' }}>
                            Approve
                        </button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="mb-0 fw-bold">Reject Document</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <form action="{{ route('admin.documents.reject', $document) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label class="form-label">Reason</label>
                            <textarea name="remarks" rows="4" class="form-control" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-danger w-100"
                            {{ $document->status === 'rejected' ? 'disabled' : '' }}>
                            Reject
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
