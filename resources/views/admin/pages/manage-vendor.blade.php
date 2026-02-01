@extends('admin.layouts.master')

@section('admin-content')
<div class="container-fluid p-4">
    <h3 class="mb-4">Manage Vendors</h3>

    <table class="table table-bordered bg-white">
        <thead>
            <tr>
                <th>#</th>
                <th>Company</th>
                <th>Contact</th>
                <th>Email</th>
                <th>Status</th>
                <th width="220">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vendors as $i => $v)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $v->company_name }}</td>
                <td>{{ $v->contact_person }} ({{ $v->phone }})</td>
                <td>{{ $v->user->email ?? '' }}</td>
                <td><span class="badge bg-secondary">{{ $v->status }}</span></td>
                <td>
                    <a href="{{ route('admin.vendors.show', $v->id) }}" class="btn btn-sm btn-dark">View</a>

                    <form action="{{ route('admin.vendors.approve', $v->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-success">Approve</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
