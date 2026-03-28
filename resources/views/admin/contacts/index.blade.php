@extends('admin.layouts.master')

@section('title', 'Contact Requests')

@section('admin-content')
<div class="mb-5">
    <h2 class="fw-bold mb-1">Contact Requests</h2>
    <p class="text-muted">View and manage customer inquiries</p>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($contacts->count())
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contacts as $contact)
                            <tr class="cursor-pointer"
                                role="button"
                                tabindex="0"
                                onclick="window.location='{{ route('admin.contacts.show', $contact) }}'"
                                onkeydown="if(event.key==='Enter' || event.key===' '){ event.preventDefault(); window.location='{{ route('admin.contacts.show', $contact) }}'; }">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $contact->user->name ?? 'Guest' }}</td>
                                <td>{{ $contact->subject }}</td>
                                <td>
                                    <span class="badge {{ $contact->status === 'replied' ? 'bg-success' : 'bg-warning' }}">
                                        {{ ucfirst($contact->status) }}
                                    </span>
                                </td>
                                <td>{{ $contact->created_at->format('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top">
                {{ $contacts->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <h5 class="fw-bold">No contact requests found</h5>
                <p class="text-muted">Customer messages will appear here.</p>
            </div>
        @endif
    </div>
</div>
@endsection
