@extends('admin.layouts.master')

@section('admin-content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="fw-bold mb-1">Subscription Plans</h2>
        <p class="text-muted mb-0">Manage vendor subscription plans</p>
    </div>

    <a href="{{ route('admin.subscription-plans.create') }}" class="btn btn-primary rounded-pill px-4">
        + Add Plan
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success rounded-3">
        {{ session('success') }}
    </div>
@endif

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        @if($plans->count())
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Billing Cycle</th>
                            <th>Price</th>
                            <th>Vehicle Limit</th>
                            <th>Driver Limit</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($plans as $plan)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $plan->name }}</div>
                                    @if($plan->description)
                                        <small class="text-muted">{{ $plan->description }}</small>
                                    @endif
                                </td>

                                <td>
                                    <span class="badge bg-dark">{{ $plan->slug }}</span>
                                </td>

                                <td class="text-capitalize">{{ $plan->billing_cycle }}</td>

                                <td>NPR {{ number_format($plan->price, 2) }}</td>

                                <td>{{ $plan->max_vehicles }}</td>

                                <td>{{ $plan->max_drivers }}</td>

                                <td>
                                    @if($plan->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>

                                <td class="text-end">
                                    <a href="{{ route('admin.subscription-plans.edit', $plan) }}"
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
                {{ $plans->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <h5 class="fw-bold">No subscription plans found</h5>
                <p class="text-muted mb-3">Create your first vendor subscription plan.</p>
                <a href="{{ route('admin.subscription-plans.create') }}" class="btn btn-primary rounded-pill px-4">
                    Create Plan
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
