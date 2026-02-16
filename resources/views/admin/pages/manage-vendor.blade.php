@extends('admin.layouts.master')

@section('admin-content')
<div class="container-fluid">

    <div class="mb-3">
        <h2 class="fw-bold mb-1">Vendor Management</h2>
        <p class="text-muted mb-0">Approve and manage vehicle vendors on the platform</p>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Vendor</th>
                        <th>Contact</th>
                        <th>Join Date</th>
                        <th>Status</th>
                        <th style="width:260px;">Actions</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($vendors as $v)
                    @php $status = $v->status ?? 'pending'; @endphp
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $v->company_name }}</div>
                            <div class="text-muted small">{{ $v->user->email ?? '' }}</div>
                        </td>

                        <td>
                            <div class="fw-semibold">{{ $v->contact_person }}</div>
                            <div class="text-muted small">{{ $v->phone }}</div>
                        </td>

                        <td class="text-muted">{{ $v->created_at?->format('M d, Y') }}</td>

                        <td>
                            @if($status === 'approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif($status === 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif($status === 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @elseif($status === 'resubmit')
                                <span class="badge bg-info text-dark">Resubmit</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($status) }}</span>
                            @endif
                        </td>

                        <td>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('admin.vendors.show', $v->id) }}" class="btn btn-sm btn-dark">
                                    View
                                </a>

                                @if($status === 'pending')
                                    <form action="{{ route('admin.vendors.approve', $v->id) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-sm btn-success">Approve</button>
                                    </form>

                                    <form action="{{ route('admin.vendors.reject', $v->id) }}" method="POST" class="d-flex gap-2">
                                        @csrf
                                        <input name="remarks" class="form-control form-control-sm"
                                               placeholder="Reject note" style="width:140px;">
                                        <button class="btn btn-sm btn-danger">Reject</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No vendors found.</td>
                    </tr>
                @endforelse
                </tbody>

            </table>
        </div>
    </div>

</div>
@endsection
