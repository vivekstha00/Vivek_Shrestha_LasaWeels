@extends('user.layouts.master')

@section('user-content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow">
                <div class="card-body">
                    <h3 class="mb-4">Be a Partner (Vendor Request)</h3>

                    <form method="POST" action="{{ route('vendor.apply') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Company Name *</label>
                            <input type="text" name="company_name" class="form-control" value="{{ old('company_name') }}" required>
                            @error('company_name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Contact Person *</label>
                            <input type="text" name="contact_person" class="form-control" value="{{ old('contact_person') }}" required>
                            @error('contact_person') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone *</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
                            @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" class="form-control" value="{{ old('address') }}">
                            @error('address') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <hr class="my-4">

                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password *</label>
                            <input type="password" name="password" class="form-control" required>
                            @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirm Password *</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Business Documents (PDF/JPG/PNG) *</label>
                            <input type="file" name="documents[]" class="form-control" multiple required>
                            @error('documents') <small class="text-danger">{{ $message }}</small> @enderror
                            @error('documents.*') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <button class="btn btn-primary w-100" type="submit">
                            Submit Vendor Request
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
