@extends('admin.layouts.master')

@section('admin-content')
<div class="mb-5">
    <h2 class="fw-bold mb-1">User Documents</h2>
    <p class="text-muted">Review and verify uploaded user documents</p>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.documents.index') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control"
                           placeholder="Search by user name or email"
                           value="{{ request('search') }}">
                </div>

                <div class="col-md-3">
                    <select name="type" class="form-select">
            <form method="GET" action="{{ route('admin.documents.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control"
                               placeholder="Search by user name or email"
                               value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3">
                        <select name="type" class="form-select">
                            <option value="">All Types</option>
                            <option value="license" {{ request('type') == 'license' ? 'selected' : '' }}>License</option>
                            <option value="citizenship" {{ request('type') == 'citizenship' ? 'selected' : '' }}>Citizenship</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if($documents->isEmpty())
                <div class="text-center py-5">
                    <h5 class="fw-bold">No documents found</h5>
                    <p class="text-muted">Uploaded documents will appear here.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Type</th>
                                <th>Document No.</th>
                                <th>Status</th>
                                <th>Uploaded</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documents as $document)
                                @php
                                    $statusClass = match($document->status) {
                                        'approved' => 'bg-success',
                                        'rejected' => 'bg-danger',
                                        default => 'bg-warning text-dark',
                                    };
                                @endphp
                                <tr>
                                    <td>#{{ $document->id }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $document->user->name ?? 'N/A' }}</div>
                                        <div class="small text-muted">{{ $document->user->email ?? 'N/A' }}</div>
                                    </td>
                                    <td>{{ ucfirst($document->type) }}</td>
                                    <td>{{ $document->document_number ?: 'N/A' }}</td>
                                    <td>
                                        <span class="badge {{ $statusClass }}">
                                            {{ ucfirst($document->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $document->created_at?->format('d M Y, h:i A') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.documents.show', $document) }}" class="btn btn-sm btn-outline-primary">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3 border-top">
                    {{ $documents->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
