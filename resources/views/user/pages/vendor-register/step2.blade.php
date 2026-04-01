@extends('user.pages.vendor-register.layout', ['title' => 'Vendor Register - Step 2', 'currentStep' => 2])

@section('register-content')
    <h3 class="section-title mb-1">Personal Information</h3>
    <p class="text-muted mb-4">Provide your personal details for verification.</p>

    <form action="{{ route('vendor.register.step2.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label fw-semibold">Full Name</label>
            <input type="text" name="full_name" class="form-control form-control-lg rounded-3"
                   value="{{ old('full_name', $vendorProfile->full_name) }}" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Phone Number</label>
          <input type="text" name="phone" class="form-control form-control-lg rounded-3"
              value="{{ old('phone', $vendorProfile->phone) }}" required placeholder="Enter 10-digit phone number">
          <div id="phone_error" class="invalid-feedback d-block" style="display:none !important;"></div>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">National ID Number</label>
                <input type="text" name="national_id_number" class="form-control form-control-lg rounded-3"
                       value="{{ old('national_id_number', $vendorProfile->national_id_number) }}" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Residential Address</label>
            <textarea name="residential_address" rows="4" class="form-control form-control-lg rounded-3" required>{{ old('residential_address', $vendorProfile->residential_address) }}</textarea>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('vendor.register.step1', ['edit' => 1]) }}" class="btn btn-outline-secondary btn-lg rounded-3 px-4">Previous</a>
            <button class="btn btn-dark btn-lg rounded-3 px-4">Next Step</button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form[action="{{ route('vendor.register.step2.store') }}"]');
            const phoneInput = form?.querySelector('input[name="phone"]');
            const phoneError = document.getElementById('phone_error');

            if (!form || !phoneInput || !phoneError) {
                return;
            }

            function validatePhoneInline() {
                const value = phoneInput.value.trim();

                if (!value) {
                    phoneInput.classList.remove('is-invalid');
                    phoneError.style.display = 'none';
                    phoneError.textContent = '';
                    return true;
                }

                const onlyDigits = /^\d+$/.test(value);

                if (!onlyDigits) {
                    phoneInput.classList.add('is-invalid');
                    phoneError.style.display = 'block';
                    phoneError.textContent = 'Phone number must contain digits only.';
                    return false;
                }

                if (value.length < 10) {
                    phoneInput.classList.add('is-invalid');
                    phoneError.style.display = 'block';
                    phoneError.textContent = 'Phone number must be at least 10 digits.';
                    return false;
                }

                if (value.length > 10) {
                    phoneInput.classList.add('is-invalid');
                    phoneError.style.display = 'block';
                    phoneError.textContent = 'Phone number cannot be more than 10 digits.';
                    return false;
                }

                phoneInput.classList.remove('is-invalid');
                phoneError.style.display = 'none';
                phoneError.textContent = '';
                return true;
            }

            phoneInput.addEventListener('input', validatePhoneInline);

            form.addEventListener('submit', function (event) {
                if (!validatePhoneInline()) {
                    event.preventDefault();
                }
            });
        });
    </script>
@endsection
