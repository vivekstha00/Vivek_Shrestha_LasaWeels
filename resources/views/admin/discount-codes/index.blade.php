@extends('admin.layouts.master')

@section('title', 'Discount Codes')

@section('admin-content')
<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h2 class="fw-bold mb-1">Discount Codes</h2>
        <p class="text-muted">Manage special offer discount campaigns</p>
    </div>
    <a href="{{ route('admin.discount-codes.create') }}" class="btn btn-primary">
        + Add Discount Code
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($discountCodes->count())
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Code</th>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Usage</th>
                            <th>Validity</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($discountCodes as $code)
                            <tr class="cursor-pointer"
                                role="button"
                                tabindex="0"
                                onclick="window.location='{{ route('admin.discount-codes.show', $code) }}'"
                                onkeydown="if(event.key==='Enter' || event.key===' '){ event.preventDefault(); window.location='{{ route('admin.discount-codes.show', $code) }}'; }">
                                <td>
                                    <div class="fw-semibold">{{ $code->title }}</div>
                                    @if($code->description)
                                        <small class="text-muted">{{ $code->description }}</small>
                                    @endif
                                </td>
                                <td><span class="badge bg-dark">{{ $code->code }}</span></td>
                                <td class="text-capitalize">{{ $code->type }}</td>
                                <td>
                                    @if($code->type === 'percentage')
                                        {{ $code->value }}%
                                    @else
                                        NPR {{ number_format($code->value, 2) }}
                                    @endif
                                </td>
                                <td>{{ $code->used_count }} / {{ $code->usage_limit ?? '∞' }}</td>
                                <td class="small text-muted">
                                    {{ $code->valid_from?->format('d M Y') }} - {{ $code->valid_until?->format('d M Y') }}
                                </td>
                                <td>
                                    @if($code->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top">
                {{ $discountCodes->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <h5 class="fw-bold">No discount codes found</h5>
                <p class="text-muted">Create your first discount code.</p>
                <a href="{{ route('admin.discount-codes.create') }}" class="btn btn-primary">Add Discount Code</a>
            </div>
        @endif
    </div>
</div>
@endsection
