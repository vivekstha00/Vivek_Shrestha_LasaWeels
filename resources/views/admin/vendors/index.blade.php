@extends('admin.layouts.master')

@section('title', 'Vendor Management')

@section('admin-content')
<div class="mb-5">
    <h2 class="fw-bold mb-1">Vendor Management</h2>
    <p class="text-muted mb-0">Click a vendor to view details</p>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Vendor</th>
                    <th>Contact</th>
                    <th>Join Date</th>
                    <th>Status</th>
                    <th class="text-end" style="width: 120px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vendors as $vendor)
                    <tr style="cursor: pointer;"
                        onclick="window.location='{{ route('admin.vendors.show', $vendor->id) }}'">
                        <td>
                            <div class="fw-semibold">{{ $vendor->company_name ?? $vendor->name }}</div>
                            <small class="text-muted">{{ $vendor->user->email ?? '' }}</small>
                        </td>
                        <td>
                            <div class="fw-medium">{{ $vendor->contact_person ?? '—' }}</div>
                            <small class="text-muted">{{ $vendor->phone ?? '—' }}</small>
                        </td>
                        <td class="text-muted small">
                            {{ $vendor->created_at?->format('d M Y') }}
                        </td>
                        <td>
                            @php
                                $statusClass = match($vendor->status) {
                                    'approved' => 'bg-success',
                                    'pending' => 'bg-warning text-dark',
                                    'rejected' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $statusClass }}">
                                {{ ucfirst($vendor->status ?? 'pending') }}
                            </span>
                        </td>
                        <td class="text-end" onclick="event.stopImmediatePropagation();">
                            <form action="{{ route('admin.vendors.delete', $vendor->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Are you sure you want to delete this vendor?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            No vendors found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-3 border-top">
        {{ $vendors->links() }}
    </div>
</div>
@endsection
