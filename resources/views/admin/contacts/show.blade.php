@extends('admin.layouts.master')

@section('admin-content')
<div class="container-fluid">

    <h3 class="fw-bold mb-4">Contact Details</h3>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <p><strong>User:</strong> {{ $contact->user->name }}</p>
            <p><strong>Email:</strong> {{ $contact->user->email }}</p>
            <p><strong>Subject:</strong> {{ $contact->subject }}</p>
            <p><strong>Message:</strong></p>
            <div class="border rounded p-3 bg-light">
                {{ $contact->message }}
            </div>

            <p class="mt-3">
                <strong>Status:</strong>
                @if($contact->status == 'pending')
                    <span class="badge bg-warning text-dark">Pending</span>
                @elseif($contact->status == 'replied')
                    <span class="badge bg-success">Replied</span>
                @elseif($contact->status == 'closed')
                    <span class="badge bg-secondary">Closed</span>
                @endif
            </p>

        </div>
    </div>

    @if($contact->status != 'replied')
    <div class="card shadow-sm">
        <div class="card-body">

            <h5 class="mb-3">Reply to User</h5>

            <form method="POST"
                  action="{{ route('admin.contacts.reply', $contact->id) }}">
                @csrf

                <div class="mb-3">
                    <textarea name="reply_message"
                              class="form-control"
                              rows="5"
                              required></textarea>
                </div>

                <button class="btn btn-dark">
                    Send Reply
                </button>

                <a href="{{ route('admin.contacts.index') }}"
                   class="btn btn-secondary">
                    Back
                </a>
            </form>

        </div>
    </div>
    @else
        <div class="card shadow-sm">
            <div class="card-body">
                <h5>Reply Sent</h5>
                <div class="border rounded p-3 bg-light">
                    {{ $contact->reply_message }}
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
