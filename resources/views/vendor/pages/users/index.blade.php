@extends('vendor.layouts.master')

@section('title', 'Users')
@section('page_title', 'Users')
@section('page_subtitle', 'Customer list (click a user to view full details)')

@section('vendor-content')
<div class="mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <form class="d-flex gap-2" method="GET" action="{{ route('vendor.users.index') }}">
            <input type="text" name="q" class="form-control" style="max-width: 340px;"
                   placeholder="Search name, email, phone..." value="{{ $q ?? '' }}">
            <button class="btn btn-outline-secondary">Search</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">User</th>
                        <th>Contact</th>
                        <th>Join Date</th>
                        <th>Total Bookings</th>
                        <th class="pe-4">Document Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        @php
                            $totalBookings = $u->bookings_count ?? 0;
                            $status = $u->selfDriveVerificationStatus();
                            $docStatus = $status === 'approved' ? 'Verified' : 'Not Verified';
                        @endphp
                        <tr role="button"
                            onclick="window.location='{{ route('vendor.users.show', $u->id) }}'">
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                                         style="width:42px;height:42px;">
                                        <i class="fas fa-user text-muted"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $u->name }}</div>
                                        <small class="text-muted">ID: {{ $u->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>{{ $u->email }}</div>
                                <small class="text-muted">{{ $u->phone ?? '—' }}</small>
                            </td>
                            <td>{{ optional($u->created_at)->format('d M Y') }}</td>
                            <td>{{ $totalBookings }}</td>
                            <td class="pe-4">
                                <span class="badge
                                    {{ $docStatus === 'Verified' ? 'bg-success' : 'bg-warning' }}">
                                    {{ $docStatus }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-top">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
