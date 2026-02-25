@extends('user.layouts.master')

@section('user-content')
<div class="container py-5 mt-5">

    <h3 class="mb-4">Contact Support</h3>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">

            <form method="POST" action="{{ route('contact.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Subject</label>
                    <input type="text"
                           name="subject"
                           class="form-control"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Message</label>
                    <textarea name="message"
                              rows="5"
                              class="form-control"
                              required></textarea>
                </div>

                <button class="btn btn-dark">
                    Submit Query
                </button>

            </form>

        </div>
    </div>
</div>
@endsection
