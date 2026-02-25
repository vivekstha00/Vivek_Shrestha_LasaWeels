@extends('admin.layouts.master')

@section('admin-content')
<div class="container-fluid">

    <h3 class="fw-bold mb-4">Contact Requests</h3>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th width="180">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contacts as $contact)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $contact->user->name ?? 'N/A' }}</td>
                                <td>{{ $contact->subject }}</td>
                                <td>
                                    @if($contact->status == 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($contact->status == 'replied')
                                        <span class="badge bg-success">Replied</span>
                                    @elseif($contact->status == 'closed')
                                        <span class="badge bg-secondary">Closed</span>
                                    @endif
                                </td>
                                <td>{{ $contact->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.contacts.show', $contact->id) }}"
                                       class="btn btn-sm btn-primary">
                                        <i class="fa fa-eye"></i>
                                    </a>

                                    <form action="{{ route('admin.contacts.destroy', $contact->id) }}"
                                          method="POST"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger"
                                                onclick="return confirm('Delete this query?')">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No contact requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $contacts->links() }}
            </div>

        </div>
    </div>

</div>
@endsection
