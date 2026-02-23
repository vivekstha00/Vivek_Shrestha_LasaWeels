@extends('admin.layouts.master')

@section('admin-content')
<div class="container-fluid">

    <!-- Page Title -->
    <div class="mb-4">
        <h2 class="fw-bold mb-1">Vendor Details</h2>
        <p class="text-muted">Review vendor information and documents</p>
    </div>

    <!-- Vendor Info -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">

            <div class="row mb-2">
                <div class="col-md-6">
                    <strong>Company Name:</strong>
                    <div>{{ $profile->company_name }}</div>
                </div>

                <div class="col-md-6">
                    <strong>Contact Person:</strong>
                    <div>{{ $profile->contact_person }}</div>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-6">
                    <strong>Email:</strong>
                    <div>{{ $profile->user->email ?? '' }}</div>
                </div>

                <div class="col-md-6">
                    <strong>Phone:</strong>
                    <div>{{ $profile->phone }}</div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <strong>Status:</strong>
                    <div>
                        @if($profile->status === 'approved')
                            <span class="badge bg-success">Approved</span>
                        @elseif($profile->status === 'pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                        @elseif($profile->status === 'rejected')
                            <span class="badge bg-danger">Rejected</span>
                        @else
                            <span class="badge bg-secondary">
                                {{ ucfirst($profile->status) }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <strong>Joined:</strong>
                    <div>{{ $profile->created_at?->format('M d, Y') }}</div>
                </div>
            </div>

            @if($profile->remarks)
                <hr>
                <strong>Remarks:</strong>
                <div class="text-muted">{{ $profile->remarks }}</div>
            @endif

        </div>
    </div>

    <!-- Documents -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header">
            <strong>Uploaded Documents</strong>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Document Type</th>
                        <th>Status</th>
                        <th>File</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($docs as $index => $doc)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $doc->type }}</td>
                        <td>
                            <span class="badge bg-secondary">
                                {{ ucfirst($doc->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ asset('storage/'.$doc->file_path) }}"
                               target="_blank"
                               class="btn btn-sm btn-outline-primary">
                                Open
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-3">
                            No documents uploaded.
                        </td>
                    </tr>
                @endforelse
                </tbody>

            </table>
        </div>
    </div>

    <!-- Actions -->
    <div class="card shadow-sm">
        <div class="card-header">
            <strong>Actions</strong>
        </div>

        <div class="card-body">

            @if($profile->status === 'pending')
                <form action="{{ route('admin.vendors.approve', $profile->id) }}"
                      method="POST" class="mb-3">
                    @csrf
                    <button class="btn btn-success">
                        Approve
                    </button>
                </form>
            @endif

            <!-- Request Resubmit -->
            <form action="{{ route('admin.vendors.resubmit', $profile->id) }}"
                  method="POST" class="mb-3">
                @csrf
                <label class="form-label">Request Resubmission (Reason)</label>
                <textarea name="remarks"
                          class="form-control mb-2"
                          rows="3"
                          required></textarea>
                <button class="btn btn-warning">
                    Request Resubmit
                </button>
            </form>

            <!-- Reject -->
            <form action="{{ route('admin.vendors.reject', $profile->id) }}"
                  method="POST">
                @csrf
                <label class="form-label">Reject Vendor (Reason)</label>
                <textarea name="remarks"
                          class="form-control mb-2"
                          rows="3"
                          required></textarea>
                <button class="btn btn-danger">
                    Reject
                </button>
            </form>

        </div>
    </div>

</div>
@endsection
