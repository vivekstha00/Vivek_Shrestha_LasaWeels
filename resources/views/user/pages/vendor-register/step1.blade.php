@extends('user.pages.vendor-register.layout', ['title' => 'Vendor Register - Step 1', 'currentStep' => 1])

@section('register-content')
    <h3 class="section-title mb-1">Account Information</h3>
    <p class="text-muted mb-4">Create your vendor account credentials.</p>

    @if(!empty($hasDraft))
        <form id="exitDraftForm" action="{{ route('vendor.register.exit') }}" method="POST" class="d-none">
            @csrf
        </form>
    @endif

    <form action="{{ route('vendor.register.step1.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label fw-semibold">Full Name</label>
            <input type="text" name="name" class="form-control form-control-lg rounded-3" value="{{ old('name', $registrationUser->name ?? '') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Email Address</label>
            <input type="email" name="email" class="form-control form-control-lg rounded-3" value="{{ old('email', $registrationUser->email ?? '') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Password</label>
            <input type="password" name="password" class="form-control form-control-lg rounded-3" required>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control form-control-lg rounded-3" required>
        </div>

        <div class="d-flex justify-content-between">
            @if(!empty($hasDraft))
                <button type="submit" form="exitDraftForm" class="btn btn-outline-danger btn-lg rounded-3 px-4"
                        onclick="return confirm('Are you sure you want to exit? All saved registration data will be deleted.');">
                    Exit Registration
                </button>
            @else
                <div></div>
            @endif
            <button class="btn btn-dark btn-lg rounded-3 px-4">Next Step</button>
        </div>
    </form>
@endsection
