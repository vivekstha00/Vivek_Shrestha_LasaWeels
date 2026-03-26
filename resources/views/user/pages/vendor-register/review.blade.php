@extends('user.pages.vendor-register.layout', ['title' => 'Vendor Register - Review', 'currentStep' => 5])

@section('register-content')
    <h3 class="section-title mb-1">Review & Submit</h3>
    <p class="text-muted mb-4">Please review your information before submitting.</p>

    <div class="border rounded-4 p-4 mb-4">
        <h5 class="fw-bold mb-3">Account Information</h5>
        <p class="mb-2"><strong>Name:</strong> {{ $user->name }}</p>
        <p class="mb-0"><strong>Email:</strong> {{ $user->email }}</p>
    </div>

    <div class="border rounded-4 p-4 mb-4">
        <h5 class="fw-bold mb-3">Personal Information</h5>
        <p class="mb-2"><strong>Full Name:</strong> {{ $vendorProfile->full_name }}</p>
        <p class="mb-2"><strong>Phone:</strong> {{ $vendorProfile->phone }}</p>
        <p class="mb-2"><strong>National ID:</strong> {{ $vendorProfile->national_id_number }}</p>
        <p class="mb-0"><strong>Residential Address:</strong> {{ $vendorProfile->residential_address }}</p>
    </div>

    <div class="border rounded-4 p-4 mb-4">
        <h5 class="fw-bold mb-3">Business Information</h5>
        <p class="mb-2"><strong>Business Name:</strong> {{ $vendorProfile->business_name }}</p>
        <p class="mb-2"><strong>Business Type:</strong> {{ ucfirst($vendorProfile->business_type) }}</p>
        <p class="mb-2"><strong>Registration Number:</strong> {{ $vendorProfile->business_registration_number ?: 'N/A' }}</p>
        <p class="mb-2"><strong>Tax / VAT Number:</strong> {{ $vendorProfile->tax_id_number ?: 'N/A' }}</p>
        <p class="mb-2"><strong>Business Address:</strong> {{ $vendorProfile->business_address }}</p>
        <p class="mb-2"><strong>Latitude:</strong> {{ $vendorProfile->latitude ?: 'N/A' }}</p>
        <p class="mb-0"><strong>Longitude:</strong> {{ $vendorProfile->longitude ?: 'N/A' }}</p>
    </div>

    <div class="border rounded-4 p-4 mb-4">
        <h5 class="fw-bold mb-3">Uploaded Documents</h5>
        <ul class="mb-0">
            <li>National ID: {{ isset($documents['national_id']) ? 'Uploaded' : 'Missing' }}</li>
            <li>Business License: {{ isset($documents['business_license']) ? 'Uploaded' : 'Missing' }}</li>
            <li>Tax Certificate: {{ isset($documents['tax_certificate']) ? 'Uploaded' : 'Not uploaded' }}</li>
            <li>Proof of Address: {{ isset($documents['proof_of_address']) ? 'Uploaded' : 'Missing' }}</li>
        </ul>
    </div>

    <div class="alert alert-warning rounded-4">
        Please make sure all details are correct before submitting. Once submitted, your application will go for admin review.
    </div>

    <div class="d-flex justify-content-between">
        <a href="{{ route('vendor.register.step4') }}" class="btn btn-outline-secondary btn-lg rounded-3 px-4">Previous</a>

        <form action="{{ route('vendor.register.submit') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success btn-lg rounded-3 px-4">
                Submit Application
            </button>
        </form>
    </div>
@endsection
