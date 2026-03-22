@extends('admin.layouts.master')

@section('admin-content')
<div class="mb-3">
    <h2 class="fw-bold mb-1">Edit Discount Code</h2>
    <p class="text-muted mb-0">Update special offer campaign details</p>
</div>

@if($errors->any())
    <div class="alert alert-danger rounded-3">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <form action="{{ route('admin.discount-codes.update', $discountCode) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Title</label>
                    <input type="text" name="title" class="form-control"
                           value="{{ old('title', $discountCode->title) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Code</label>
                    <input type="text" name="code" class="form-control text-uppercase"
                           value="{{ old('code', $discountCode->code) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Type</label>
                    <select name="type" class="form-select" required>
                        <option value="percentage" {{ old('type', $discountCode->type) === 'percentage' ? 'selected' : '' }}>Percentage</option>
                        <option value="fixed" {{ old('type', $discountCode->type) === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Value</label>
                    <input type="number" step="0.01" min="1" name="value" class="form-control"
                           value="{{ old('value', $discountCode->value) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Max Discount Amount</label>
                    <input type="number" step="0.01" min="0" name="max_discount_amount" class="form-control"
                           value="{{ old('max_discount_amount', $discountCode->max_discount_amount) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Usage Limit</label>
                    <input type="number" min="1" name="usage_limit" class="form-control"
                           value="{{ old('usage_limit', $discountCode->usage_limit) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Valid From</label>
                    <input type="datetime-local" name="valid_from" class="form-control"
                           value="{{ old('valid_from', optional($discountCode->valid_from)->format('Y-m-d\TH:i')) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Valid Until</label>
                    <input type="datetime-local" name="valid_until" class="form-control"
                           value="{{ old('valid_until', optional($discountCode->valid_until)->format('Y-m-d\TH:i')) }}">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $discountCode->description) }}</textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Used Count</label>
                    <input type="text" class="form-control" value="{{ $discountCode->used_count }}" disabled>
                </div>

                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
                            {{ old('is_active', $discountCode->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Active
                        </label>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill px-4">
                    Update Discount Code
                </button>
                <a href="{{ route('admin.discount-codes.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
