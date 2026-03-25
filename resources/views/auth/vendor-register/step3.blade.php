@extends('auth.vendor-register.layout', ['title' => 'Vendor Register - Step 3', 'currentStep' => 3])

@section('content')
    <h3 class="section-title mb-1">Business Information</h3>
    <p class="text-muted mb-4">Tell us about your business.</p>

    <form action="{{ route('vendor.register.step3.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label fw-semibold">Business Name</label>
            <input type="text" name="business_name" class="form-control form-control-lg rounded-3"
                   value="{{ old('business_name', $vendorProfile->business_name) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Business Type</label>
            <select name="business_type" class="form-select form-select-lg rounded-3" required>
                <option value="">Select Business Type</option>
                <option value="individual" {{ old('business_type', $vendorProfile->business_type) == 'individual' ? 'selected' : '' }}>Individual / Sole Proprietor</option>
                <option value="partnership" {{ old('business_type', $vendorProfile->business_type) == 'partnership' ? 'selected' : '' }}>Partnership</option>
                <option value="company" {{ old('business_type', $vendorProfile->business_type) == 'company' ? 'selected' : '' }}>Company</option>
            </select>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Business Registration Number</label>
                <input type="text" name="business_registration_number" class="form-control form-control-lg rounded-3"
                       value="{{ old('business_registration_number', $vendorProfile->business_registration_number) }}">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Tax / VAT Number</label>
                <input type="text" name="tax_id_number" class="form-control form-control-lg rounded-3"
                       value="{{ old('tax_id_number', $vendorProfile->tax_id_number) }}">
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Business Address</label>
            <textarea name="business_address" rows="4" class="form-control form-control-lg rounded-3" required>{{ old('business_address', $vendorProfile->business_address) }}</textarea>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('vendor.register.step2') }}" class="btn btn-outline-secondary btn-lg rounded-3 px-4">Previous</a>
            <button class="btn btn-dark btn-lg rounded-3 px-4">Next Step</button>
        </div>
    </form>
@endsection
