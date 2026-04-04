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

    @if(session('error'))
        <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
            {{ session('error') }}
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
                    @if($vendor->vendor_status == 'pending') bg-warning text-dark
                    @elseif($vendor->vendor_status == 'approved') bg-success
                    @elseif($vendor->vendor_status == 'resubmit') bg-info text-dark
                    @elseif($vendor->vendor_status == 'rejected') bg-danger
                    @else bg-secondary
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

            @php
                $documents = $vendor->vendorDocuments->keyBy('type');
                $docLabels = [
                    'national_id' => 'National ID',
                    'business_license' => 'Business License',
                    'tax_certificate' => 'Tax Certificate',
                    'proof_of_address' => 'Proof of Address',
                ];
                $rejectedDocs = collect($docLabels)
                    ->filter(fn ($_label, $type) => isset($documents[$type]) && $documents[$type]->status === 'rejected');
            @endphp

            <h5 class="mt-4 mb-3">Submitted Documents</h5>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Document</th>
                            <th>Status</th>
                            <th>Remarks</th>
                            <th>File</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($docLabels as $type => $label)
                            @php
                                $doc = $documents[$type] ?? null;
                                $status = $doc->status ?? 'not_uploaded';
                                $statusClass = match($status) {
                                    'approved' => 'bg-success',
                                    'pending' => 'bg-warning text-dark',
                                    'rejected' => 'bg-danger',
                                    default => 'bg-secondary',
                                };
                            @endphp
                            <tr>
                                <td>{{ $label }}</td>
                                <td><span class="badge {{ $statusClass }}">{{ str_replace('_', ' ', ucfirst($status)) }}</span></td>
                                <td>{{ $doc->remarks ?: '—' }}</td>
                                <td>
                                    @if($doc)
                                        <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($vendor->vendor_status === 'pending')
                <div class="alert alert-warning mt-3">
                    Your documents are under review. Please wait for admin action.
                </div>
            @endif

            @if($rejectedDocs->isNotEmpty())
                <h5 class="mt-4 mb-3">Resubmit Rejected Documents</h5>

                @foreach($rejectedDocs as $type => $label)
                    <div class="border rounded p-3 mb-3 bg-light">
                        <h6 class="mb-2">{{ $label }}</h6>
                        <div class="small text-muted mb-3">
                            Reason: {{ $documents[$type]->remarks ?: 'Please upload corrected document.' }}
                        </div>

                        <form action="{{ route('vendor.verification.resubmit') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="type" value="{{ $type }}">

                            <div class="row g-2 align-items-end">
                                <div class="col-md-8">
                                    <label class="form-label">Upload Corrected File</label>
                                    <input
                                        type="file"
                                        name="document"
                                        class="form-control @error('document') is-invalid @enderror"
                                        accept=".jpg,.jpeg,.png,.pdf"
                                        required
                                    >
                                    @error('document')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary w-100">Resubmit {{ $label }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endforeach
            @elseif($vendor->vendor_status === 'resubmit')
                <div class="alert alert-info mt-3">
                    No rejected documents currently. Please wait for admin review updates.
                </div>
            @endif

            @if($vendor->vendor_status == 'rejected')
                <div class="alert alert-danger mt-3">
                    Your application was rejected. Please contact admin.
                </div>
            @endif

        </div>
    </div>

</div>
@endsection
