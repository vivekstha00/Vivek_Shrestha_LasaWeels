@extends('admin.layouts.master')

@section('admin-content')
<div class="container-fluid p-4">
    <h3 class="mb-3">Vendor Details</h3>

    <div class="card mb-4">
        <div class="card-body">
            <p><strong>Company:</strong> {{ $profile->company_name }}</p>
            <p><strong>Contact:</strong> {{ $profile->contact_person }} ({{ $profile->phone }})</p>
            <p><strong>Email:</strong> {{ $profile->user->email ?? '' }}</p>
            <p><strong>Status:</strong> {{ $profile->status }}</p>
            @if($profile->remarks)
                <p><strong>Remarks:</strong> {{ $profile->remarks }}</p>
            @endif
        </div>
    </div>

    <h5>Documents</h5>
    <table class="table table-bordered bg-white">
        <thead>
            <tr>
                <th>#</th>
                <th>Type</th>
                <th>Status</th>
                <th>File</th>
            </tr>
        </thead>
        <tbody>
            @foreach($docs as $i => $d)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $d->type }}</td>
                <td>{{ $d->status }}</td>
                <td>
                    <a href="{{ asset('storage/'.$d->file_path) }}" target="_blank">Open</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="card mt-4">
        <div class="card-body">
            <form action="{{ route('admin.vendors.resubmit', $profile->id) }}" method="POST" class="mb-2">
                @csrf
                <label class="form-label">Request Resubmission Remarks</label>
                <textarea class="form-control mb-2" name="remarks" rows="2"></textarea>
                <button class="btn btn-warning">Request Resubmit</button>
            </form>

            <form action="{{ route('admin.vendors.reject', $profile->id) }}" method="POST">
                @csrf
                <label class="form-label">Reject Remarks</label>
                <textarea class="form-control mb-2" name="remarks" rows="2"></textarea>
                <button class="btn btn-danger">Reject</button>
            </form>
        </div>
    </div>

</div>
@endsection
