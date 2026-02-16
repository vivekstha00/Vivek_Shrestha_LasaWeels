@extends('vendor.layouts.master')

@section('vendor-content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">My Vehicles</h3>
        <a href="{{ route('vendor.vehicles.create') }}" class="btn btn-primary">+ Add Vehicle</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width:70px;">Image</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>City</th>
                            <th>Price / Day</th>
                            <th>Status</th>
                            <th>Active</th>
                            <th style="width:260px;">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($vehicles as $i => $v)
                            @php
                                $thumb = $v->image_url
                                    ? asset('storage/'.$v->image_url)
                                    : null;
                            @endphp

                            <tr>
                                <td>
                                    @if($thumb)
                                        <img src="{{ $thumb }}"
                                             alt="Vehicle"
                                             style="width:60px;height:40px;object-fit:cover;border-radius:6px;">
                                    @else
                                        <div style="width:60px;height:40px;border-radius:6px;"
                                             class="bg-light d-flex align-items-center justify-content-center small text-muted">
                                            N/A
                                        </div>
                                    @endif
                                </td>

                                <td class="fw-semibold">{{ $v->title }}</td>
                                <td>{{ $v->vehicle_type }}</td>
                                <td>{{ $v->location_city }}</td>
                                <td>{{ number_format($v->price_per_day, 2) }} {{ $v->currency }}</td>
                                <td>
                                    @if($v->status === 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($v->status === 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                        @if($v->reject_reason)
                                            <div class="small text-muted mt-1">Reason: {{ $v->reject_reason }}</div>
                                        @endif
                                    @endif
                                </td>
                                <td>{{ $v->is_active ? 'Yes' : 'No' }}</td>

                                <td class="d-flex gap-2 flex-wrap">
                                    <a class="btn btn-sm btn-outline-primary"
                                       href="{{ route('vendor.vehicles.show', $v->id) }}">
                                        View
                                    </a>

                                    <a class="btn btn-sm btn-outline-dark"
                                       href="{{ route('vendor.vehicles.edit', $v->id) }}">
                                        Edit
                                    </a>

                                    <form method="POST" action="{{ route('vendor.vehicles.destroy', $v->id) }}"
                                          onsubmit="return confirm('Delete this vehicle?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No vehicles yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $vehicles->links() }}
            </div>
        </div>
    </div>

</div>
@endsection
