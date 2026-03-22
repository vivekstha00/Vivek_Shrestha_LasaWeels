@extends('admin.layouts.master')

@section('admin-content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="fw-bold mb-1">Discount Codes</h2>
        <p class="text-muted mb-0">Manage special offer discount campaigns</p>
    </div>

    <a href="{{ route('admin.discount-codes.create') }}" class="btn btn-primary rounded-pill px-4">
        + Add Discount Code
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success rounded-3">
        {{ session('success') }}
    </div>
@endif

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        @if($discountCodes->count())
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Code</th>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Usage</th>
                            <th>Validity</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($discountCodes as $discountCode)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $discountCode->title }}</div>
                                    @if($discountCode->description)
                                        <small class="text-muted">{{ $discountCode->description }}</small>
                                    @endif
                                </td>

                                <td>
                                    <span class="badge bg-dark">{{ $discountCode->code }}</span>
                                </td>

                                <td class="text-capitalize">{{ $discountCode->type }}</td>

                                <td>
                                    @if($discountCode->type === 'percentage')
                                        {{ rtrim(rtrim(number_format($discountCode->value, 2), '0'), '.') }}%
                                    @else
                                        NPR {{ number_format($discountCode->value, 2) }}
                                    @endif

                                    @if($discountCode->max_discount_amount)
                                        <br>
                                        <small class="text-muted">
                                            Max: NPR {{ number_format($discountCode->max_discount_amount, 2) }}
                                        </small>
                                    @endif
                                </td>

                                <td>
                                    {{ $discountCode->used_count }}
                                    @if(!is_null($discountCode->usage_limit))
                                        / {{ $discountCode->usage_limit }}
                                    @else
                                        / Unlimited
                                    @endif
                                </td>

                                <td>
                                    <small>
                                        From:
                                        {{ $discountCode->valid_from ? $discountCode->valid_from->format('Y-m-d h:i A') : 'Anytime' }}
                                        <br>
                                        To:
                                        {{ $discountCode->valid_until ? $discountCode->valid_until->format('Y-m-d h:i A') : 'No expiry' }}
                                    </small>
                                </td>

                                <td>
                                    @if($discountCode->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>

                                <td class="text-end">
                                    <a href="{{ route('admin.discount-codes.edit', $discountCode) }}"
                                       class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $discountCodes->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <h5 class="fw-bold">No discount codes found</h5>
                <p class="text-muted mb-3">Create your first special-offer code for users.</p>
                <a href="{{ route('admin.discount-codes.create') }}" class="btn btn-primary rounded-pill px-4">
                    Create Discount Code
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
