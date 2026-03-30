@extends('vendor.layouts.master')

@section('vendor-content')
<div class="container mt-4">

    <h3>Vendor Verification Status</h3>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger mt-3">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mt-3">
        <div class="card-body">

            <p><strong>Current Status:</strong>
                <span class="badge
                    @if($vendor->vendor_status == 'pending') bg-warning
                    @elseif($vendor->vendor_status == 'approved') bg-success
                    @elseif($vendor->vendor_status == 'resubmit') bg-info
                    @elseif($vendor->vendor_status == 'rejected') bg-danger
                    @endif
                ">
                    {{ ucfirst($vendor->vendor_status) }}
                </span>
            </p>

            @if($vendor->verification_note)
                <div class="alert alert-danger">
                    <strong>Admin Remark:</strong><br>
                    {{ $vendor->verification_note }}
                </div>
            @endif

            @if($vendor->vendor_status == 'pending')
                <div class="alert alert-warning">
                    Your documents are under review. Please wait for admin approval.
                </div>
            @endif

            @if($vendor->vendor_status == 'resubmit')
                <form action="{{ route('vendor.verification.resubmit') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Document Type</label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="">Select document type</option>
                            <option value="national_id" {{ old('type') === 'national_id' ? 'selected' : '' }}>National ID</option>
                            <option value="business_license" {{ old('type') === 'business_license' ? 'selected' : '' }}>Business License</option>
                            <option value="tax_certificate" {{ old('type') === 'tax_certificate' ? 'selected' : '' }}>Tax Certificate</option>
                            <option value="proof_of_address" {{ old('type') === 'proof_of_address' ? 'selected' : '' }}>Proof of Address</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Upload Corrected Document</label>
                        <input type="file" name="document" class="form-control @error('document') is-invalid @enderror" accept=".jpg,.jpeg,.png,.pdf" required>
                        @error('document')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Allowed: JPG, PNG, PDF (max 5MB)</small>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Resubmit Document
                    </button>
                </form>
            @endif

            @if($vendor->vendor_status == 'rejected')
                <div class="alert alert-danger">
                    Your application was rejected. Please contact admin.
                </div>
            @endif

        </div>
    </div>

</div>
@endsection
