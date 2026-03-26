@extends('user.pages.vendor-register.layout', ['title' => 'Vendor Register - Step 4', 'currentStep' => 4])

@section('register-content')
    <h3 class="section-title mb-1">Document Upload</h3>
    <p class="text-muted mb-4">Upload required documents for vendor verification.</p>

    <form action="{{ route('vendor.register.step4.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-4 upload-box">
            <label class="form-label fw-semibold">National ID Document *</label>
            <input type="file" name="national_id" class="form-control rounded-3">
            @if(isset($documents['national_id']))
                <small class="text-success d-block mt-2">Uploaded: {{ basename($documents['national_id']->file_path) }}</small>
            @endif
        </div>

        <div class="mb-4 upload-box">
            <label class="form-label fw-semibold">Business License *</label>
            <input type="file" name="business_license" class="form-control rounded-3">
            @if(isset($documents['business_license']))
                <small class="text-success d-block mt-2">Uploaded: {{ basename($documents['business_license']->file_path) }}</small>
            @endif
        </div>

        <div class="mb-4 upload-box">
            <label class="form-label fw-semibold">Tax Certificate</label>
            <input type="file" name="tax_certificate" class="form-control rounded-3">
            @if(isset($documents['tax_certificate']))
                <small class="text-success d-block mt-2">Uploaded: {{ basename($documents['tax_certificate']->file_path) }}</small>
            @endif
        </div>

        <div class="mb-4 upload-box">
            <label class="form-label fw-semibold">Proof of Address *</label>
            <input type="file" name="proof_of_address" class="form-control rounded-3">
            @if(isset($documents['proof_of_address']))
                <small class="text-success d-block mt-2">Uploaded: {{ basename($documents['proof_of_address']->file_path) }}</small>
            @endif
        </div>

        <div class="alert alert-info rounded-4">
            <strong>Requirements:</strong>
            <ul class="mb-0 mt-2">
                <li>Allowed formats: PDF, JPG, JPEG, PNG</li>
                <li>Maximum file size: 5MB per file</li>
                <li>Documents must be clear and readable</li>
            </ul>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('vendor.register.step3') }}" class="btn btn-outline-secondary btn-lg rounded-3 px-4">Previous</a>
            <button class="btn btn-dark btn-lg rounded-3 px-4">Next Step</button>
        </div>
    </form>
@endsection
