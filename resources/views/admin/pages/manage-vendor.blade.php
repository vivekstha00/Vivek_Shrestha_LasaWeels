@extends('admin.layouts.master')

@section('admin-content')
<div class="container-fluid">

    <div class="mb-4">
        <h2 class="fw-bold mb-1">Vendor Management</h2>
        <p class="text-muted">Click a vendor to view details</p>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Vendor</th>
                        <th>Contact</th>
                        <th>Join Date</th>
                        <th>Status</th>
                        <th width="100">Delete</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($vendors as $v)
                    @php $status = $v->status ?? 'pending'; @endphp
                    <tr style="cursor:pointer;"
                        onclick="window.location='{{ route('admin.vendors.show', $v->id) }}'">

                        <td>
                            <div class="fw-semibold">{{ $v->company_name }}</div>
                            <div class="text-muted small">{{ $v->user->email ?? '' }}</div>
                        </td>

                        <td>
                            <div class="fw-semibold">{{ $v->contact_person }}</div>
                            <div class="text-muted small">{{ $v->phone }}</div>
                        </td>

                        <td class="text-muted">
                            {{ $v->created_at?->format('M d, Y') }}
                        </td>

                        <td>
                            @if($status === 'approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif($status === 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif($status === 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </td>

                        <!-- Delete Button (prevent row click) -->
                        <td onclick="event.stopPropagation();">
                            <form action="{{ route('admin.vendors.delete', $v->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Delete this vendor?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    Delete
                                </button>
                            </form>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            No vendors found.
                        </td>
                    </tr>
                @endforelse
                </tbody>

            </table>
        </div>
    </div>

</div>
@endsection
