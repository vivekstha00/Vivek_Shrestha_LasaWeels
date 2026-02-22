@extends('vendor.layouts.master')

@section('vendor-content')
<div class="container mt-4">

    <h3>Vendor Verification Status</h3>

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
                        <label class="form-label">Upload Corrected Document</label>
                        <input type="file" name="document" class="form-control" required>
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
