@extends('vendor.layouts.master')

@section('title', 'Users - Vendor')
@section('page_title', 'Users')
@section('page_subtitle', 'Customer list (click a user to view full details)')

@section('vendor-content')
<div class="container-fluid">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <form class="d-flex gap-2" method="GET" action="{{ route('vendor.users.index') }}">
            <input type="text" name="q" class="form-control" style="max-width:320px"
                   placeholder="Search name, email, phone..." value="{{ $q }}">
            <button class="btn btn-outline-secondary">Search</button>
        </form>
    </div>

    <div class="card card-soft">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3">User</th>
                            <th>Contact</th>
                            <th>Join Date</th>
                            <th>Total Bookings</th>
                            <th class="pe-3">Document Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($users as $u)
                            @php
                                // placeholders for now
                                $totalBookings = 0;
                                $docStatus = 'Verified'; // or Pending/Not Verified
                            @endphp

                            <tr role="button"
                                onclick="window.location='{{ route('vendor.users.show', $u->id) }}'"
                                class="user-row">
                                <td class="ps-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                                             style="width:34px;height:34px;">
                                            <i class="fas fa-user text-muted"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $u->name }}</div>
                                            <div class="text-muted small">ID: {{ $u->id }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="small">{{ $u->email }}</div>
                                    <div class="text-muted small">{{ $u->phone ?? '—' }}</div>
                                </td>

                                <td class="small">
                                    {{ optional($u->created_at)->format('M d, Y') ?? '—' }}
                                </td>

                                <td class="small">{{ $totalBookings }}</td>

                                <td class="pe-3">
                                    @if($docStatus === 'Verified')
                                        <span class="badge badge-soft badge-approved">Verified</span>
                                    @elseif($docStatus === 'Pending')
                                        <span class="badge badge-soft badge-pending">Pending</span>
                                    @else
                                        <span class="badge badge-soft badge-rejected">Not Verified</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>

</div>

{{-- Tiny Bootstrap-friendly hover --}}
<style>
    .user-row:hover { background: #f8fafc; }
</style>
@endsection
