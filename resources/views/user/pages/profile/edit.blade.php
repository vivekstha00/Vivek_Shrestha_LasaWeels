@extends('user.layouts.master')

@section('user-content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
        <div>
            <h3 class="fw-bold mb-1">Profile Settings</h3>
            <p class="text-muted mb-0">Manage account details, security, profile photo, and verification documents.</p>
        </div>
        <a href="{{ route('user.profile') }}" class="btn btn-outline-secondary rounded-3">Back to Profile</a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-8 d-flex align-items-center gap-3">
                    @if($user->profile_image)
                        <img
                            src="{{ asset('storage/'.$user->profile_image) }}"
                            alt="Profile Image"
                            class="rounded-circle border"
                            style="width:84px;height:84px;object-fit:cover;"
                        >
                    @else
                        <div class="rounded-circle bg-dark text-white d-inline-flex align-items-center justify-content-center"
                             style="width:84px;height:84px;font-size:28px;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                        <div class="text-muted">{{ $user->email }}</div>
                        <div class="small text-muted">Member since {{ $user->created_at->format('M Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Account Information</h5>
                    <span class="small text-muted">Profile + Photo</span>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Profile Image</label>
                                <input type="file" name="profile_image" class="form-control">
                                <small class="text-muted">JPG/PNG/WEBP • max 2MB</small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Address</label>
                                <input type="text" name="address" class="form-control" value="{{ old('address', $user->address) }}">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mt-4 rounded-3">Save Profile Changes</button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="mb-0 fw-bold">Security</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('user.profile.password.update') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label" for="current_password">Current Password</label>
                                <div class="input-group">
                                    <input type="password" id="current_password" name="current_password" class="form-control password-input @error('current_password') is-invalid @enderror" required>
                                    <span class="input-group-text">
                                        <i class="bi bi-eye password-toggle"></i>
                                    </span>
                                </div>
                                @error('current_password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="new_password">New Password</label>
                                <div class="input-group">
                                    <input type="password" id="new_password" name="password" class="form-control password-input @error('password') is-invalid @enderror" required>
                                    <span class="input-group-text">
                                        <i class="bi bi-eye password-toggle"></i>
                                    </span>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="new_password_confirmation">Confirm Password</label>
                                <div class="input-group">
                                    <input type="password" id="new_password_confirmation" name="password_confirmation" class="form-control password-input" required>
                                    <span class="input-group-text">
                                        <i class="bi bi-eye password-toggle"></i>
                                    </span>
                                </div>
                            </div>
                        </div>


                        <button type="submit" class="btn btn-dark mt-4 rounded-3">Update Password</button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="mb-0 fw-bold">Verification Documents</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    @php
                        $license = $documents['license'] ?? null;
                        $citizenship = $documents['citizenship'] ?? null;
                    @endphp

                    <div class="border rounded-3 p-3 mb-3 bg-light-subtle">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0 fw-bold">Driving License</h6>
                            @if($license)
                                <span class="badge {{ $license->status == 'approved' ? 'bg-success' : ($license->status == 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                    {{ ucfirst($license->status) }}
                                </span>
                            @else
                                <span class="badge bg-secondary">Not Uploaded</span>
                            @endif
                        </div>

                        @if($license)
                            <div class="mb-2 small text-muted">Document Number: <span class="fw-semibold text-dark">{{ $license->document_number ?: 'N/A' }}</span></div>
                            <a href="{{ asset('storage/'.$license->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary mb-3">View Document</a>
                        @endif

                        <form action="{{ $license ? route('user.documents.update', $license) : route('user.documents.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @if($license)
                                @method('PUT')
                            @endif
                            <input type="hidden" name="type" value="license">

                            <div class="row g-2">
                                <div class="col-md-3"><input type="file" name="file" class="form-control form-control-sm" {{ $license ? '' : 'required' }}></div>
                                <div class="col-md-3"><input type="text" name="document_number" class="form-control form-control-sm" placeholder="License Number" value="{{ $license->document_number ?? '' }}"></div>
                                <div class="col-md-2"><input type="date" name="issued_at" class="form-control form-control-sm" value="{{ $license->issued_at ?? '' }}"></div>
                                <div class="col-md-2"><input type="date" name="expires_at" class="form-control form-control-sm" value="{{ $license->expires_at ?? '' }}"></div>
                                <div class="col-md-2"><button type="submit" class="btn btn-primary btn-sm w-100">Save</button></div>
                            </div>
                        </form>
                    </div>

                    <div class="border rounded-3 p-3 bg-light-subtle">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0 fw-bold">Citizenship</h6>
                            @if($citizenship)
                                <span class="badge {{ $citizenship->status == 'approved' ? 'bg-success' : ($citizenship->status == 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                    {{ ucfirst($citizenship->status) }}
                                </span>
                            @else
                                <span class="badge bg-secondary">Not Uploaded</span>
                            @endif
                        </div>

                        @if($citizenship)
                            <div class="mb-2 small text-muted">Document Number: <span class="fw-semibold text-dark">{{ $citizenship->document_number ?: 'N/A' }}</span></div>
                            <a href="{{ asset('storage/'.$citizenship->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary mb-3">View Document</a>
                        @endif

                        <form action="{{ $citizenship ? route('user.documents.update', $citizenship) : route('user.documents.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @if($citizenship)
                                @method('PUT')
                            @endif
                            <input type="hidden" name="type" value="citizenship">

                            <div class="row g-2">
                                <div class="col-md-4"><input type="file" name="file" class="form-control form-control-sm" {{ $citizenship ? '' : 'required' }}></div>
                                <div class="col-md-4"><input type="text" name="document_number" class="form-control form-control-sm" placeholder="Citizenship Number" value="{{ $citizenship->document_number ?? '' }}"></div>
                                <div class="col-md-4"><button type="submit" class="btn btn-primary btn-sm w-100">Save</button></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

