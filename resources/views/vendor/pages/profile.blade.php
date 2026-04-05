@extends('vendor.layouts.master')

@section('vendor-content')

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

<div class="container-fluid py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
        <div>
            <h3 class="mb-1">Vendor Profile</h3>
            <p class="text-muted mb-0">Manage account info, business location, and verification documents.</p>
        </div>
        <div>
            @php
                $statusClass = match($vendor->vendor_status) {
                    'approved' => 'bg-success',
                    'pending' => 'bg-warning text-dark',
                    'resubmit' => 'bg-info text-dark',
                    'rejected' => 'bg-danger',
                    default => 'bg-secondary',
                };
            @endphp
            <span class="badge {{ $statusClass }} px-3 py-2">Status: {{ ucfirst($vendor->vendor_status ?? 'unknown') }}</span>
        </div>
    </div>

    @if($vendor->verification_note)
        <div class="alert alert-warning">
            <strong>Admin Remark:</strong> {{ $vendor->verification_note }}
        </div>
    @endif

    @if($errors->has('profile'))
        <div class="alert alert-danger">{{ $errors->first('profile') }}</div>
    @endif

    <div class="card card-soft p-4" style="max-width:1400px;">
        <form method="POST" action="{{ route('vendor.profile.update') }}" enctype="multipart/form-data">
            @csrf

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="border rounded-3 p-3 h-100">
                        <h6 class="fw-bold mb-3">Account Profile</h6>

                        <div class="mb-3 text-center">
                            <div class="mb-2">
                    @if(!empty($vendor->profile_image))
                        <img
                            src="{{ asset('storage/' . ltrim($vendor->profile_image, '/')) }}"
                            alt="Vendor Profile Image"
                            class="rounded-circle border"
                            style="width: 110px; height: 110px; object-fit: cover;"
                        >
                    @else
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center border"
                             style="width: 110px; height: 110px; background: #f1f5f9; font-size: 2rem; font-weight: 700; color: #334155;">
                            {{ strtoupper(substr($vendor->name ?? 'V', 0, 1)) }}
                        </div>
                    @endif
                            </div>

                            <label class="form-label">Profile Picture</label>
                            <input
                                type="file"
                                name="profile_image"
                                accept="image/png,image/jpeg,image/jpg,image/webp"
                                class="form-control @error('profile_image') is-invalid @enderror"
                            >
                            @error('profile_image')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Allowed: JPG, JPEG, PNG, WEBP (max 2MB)</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text"
                                   name="name"
                                   value="{{ old('name', $vendor->name) }}"
                                   class="form-control @error('name') is-invalid @enderror">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email"
                                   value="{{ $vendor->email }}"
                                   class="form-control"
                                   disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text"
                                   name="phone"
                                   value="{{ old('phone', $vendor->phone ?? '') }}"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   placeholder="98XXXXXXXX">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Residential Address</label>
                            <input type="text"
                                   name="address"
                                   value="{{ old('address', $vendor->address ?? '') }}"
                                   class="form-control @error('address') is-invalid @enderror">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="border rounded-3 p-3 mb-4">
                        <h6 class="fw-bold mb-3">Business Information</h6>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Business Name</label>
                                <input type="text" name="business_name" class="form-control @error('business_name') is-invalid @enderror"
                                       value="{{ old('business_name', $vendor->vendorProfile->business_name) }}">
                                @error('business_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Business Type</label>
                                <select name="business_type" class="form-select @error('business_type') is-invalid @enderror">
                                    <option value="">Select Business Type</option>
                                    <option value="individual" {{ old('business_type', $vendor->vendorProfile->business_type) == 'individual' ? 'selected' : '' }}>Individual / Sole Proprietor</option>
                                    <option value="partnership" {{ old('business_type', $vendor->vendorProfile->business_type) == 'partnership' ? 'selected' : '' }}>Partnership</option>
                                    <option value="company" {{ old('business_type', $vendor->vendorProfile->business_type) == 'company' ? 'selected' : '' }}>Company</option>
                                </select>
                                @error('business_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Business Registration Number</label>
                                <input type="text" name="business_registration_number" class="form-control @error('business_registration_number') is-invalid @enderror"
                                       value="{{ old('business_registration_number', $vendor->vendorProfile->business_registration_number) }}">
                                @error('business_registration_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tax / VAT Number</label>
                                <input type="text" name="tax_id_number" class="form-control @error('tax_id_number') is-invalid @enderror"
                                       value="{{ old('tax_id_number', $vendor->vendorProfile->tax_id_number) }}">
                                @error('tax_id_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Business Address</label>
                                <textarea id="business_address" name="business_address" rows="2" class="form-control @error('business_address') is-invalid @enderror">{{ old('business_address', $vendor->vendorProfile->business_address) }}</textarea>
                                @error('business_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text">If location/business details change, admin re-verification will be triggered.</div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label">Search Location</label>
                            <div class="input-group">
                                <input type="text" id="location_search" class="form-control" placeholder="Search business location">
                                <button type="button" id="search_location_btn" class="btn btn-outline-dark">Search</button>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label">Pin Business Location</label>
                            <div id="map" style="height: 340px; border-radius: 12px; border: 1px solid #dee2e6;"></div>
                        </div>

                        <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $vendor->vendorProfile->latitude) }}">
                        <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $vendor->vendorProfile->longitude) }}">

                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="form-label">Latitude</label>
                                <input type="text" id="latitude_display" class="form-control @error('latitude') is-invalid @enderror"
                                       value="{{ old('latitude', $vendor->vendorProfile->latitude) }}" readonly>
                                @error('latitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Longitude</label>
                                <input type="text" id="longitude_display" class="form-control @error('longitude') is-invalid @enderror"
                                       value="{{ old('longitude', $vendor->vendorProfile->longitude) }}" readonly>
                                @error('longitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="border rounded-3 p-3">
                        <h6 class="fw-bold mb-3">Verification Documents</h6>

                        <div class="table-responsive mb-3">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Document</th>
                                        <th>Status</th>
                                        <th>Remark</th>
                                        <th>Current File</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($docLabels as $type => $label)
                                        @php
                                            $doc = $documents[$type] ?? null;
                                            $status = $doc->status ?? 'not_uploaded';
                                            $statusClass = match($status) {
                                                'approved' => 'bg-success',
                                                'pending' => 'bg-warning text-dark',
                                                'rejected' => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <tr>
                                            <td>{{ $label }}</td>
                                            <td><span class="badge {{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</span></td>
                                            <td>{{ $doc->remarks ?: '—' }}</td>
                                            <td>
                                                @if($doc)
                                                    <a href="{{ asset('storage/' . ltrim($doc->file_path, '/')) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                                @else
                                                    —
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="row g-3">
                            @foreach($docLabels as $type => $label)
                                <div class="col-md-6">
                                    <label class="form-label">Replace {{ $label }} (optional)</label>
                                    <input type="file" name="{{ $type }}" class="form-control @error($type) is-invalid @enderror" accept=".jpg,.jpeg,.png,.pdf">
                                    @error($type)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-end">
                <button class="btn btn-primary px-4">Update Profile</button>
            </div>
        </form>
    </div>

</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const latitudeInput = document.getElementById('latitude');
        const longitudeInput = document.getElementById('longitude');
        const latitudeDisplay = document.getElementById('latitude_display');
        const longitudeDisplay = document.getElementById('longitude_display');
        const businessAddress = document.getElementById('business_address');
        const searchInput = document.getElementById('location_search');
        const searchButton = document.getElementById('search_location_btn');

        if (!latitudeInput || !longitudeInput || !document.getElementById('map')) {
            return;
        }

        const defaultLat = parseFloat(latitudeInput.value) || 28.2096;
        const defaultLng = parseFloat(longitudeInput.value) || 83.9856;

        const map = L.map('map').setView([defaultLat, defaultLng], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

        function updateLocation(lat, lng) {
            const formattedLat = Number(lat).toFixed(7);
            const formattedLng = Number(lng).toFixed(7);

            latitudeInput.value = formattedLat;
            longitudeInput.value = formattedLng;

            if (latitudeDisplay) latitudeDisplay.value = formattedLat;
            if (longitudeDisplay) longitudeDisplay.value = formattedLng;
        }

        async function updateAddressFromCoordinates(lat, lng) {
            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`);
                const data = await response.json();

                if (data && data.display_name && businessAddress) {
                    businessAddress.value = data.display_name;
                }
            } catch (error) {
                console.error('Reverse geocoding failed:', error);
            }
        }

        async function searchLocation(query) {
            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/search?format=jsonv2&q=${encodeURIComponent(query)}&limit=1`);
                const results = await response.json();

                if (!results.length) {
                    window.showNotification?.('error', 'Location not found. Please try another search.');
                    return;
                }

                const lat = parseFloat(results[0].lat);
                const lng = parseFloat(results[0].lon);

                map.setView([lat, lng], 16);
                marker.setLatLng([lat, lng]);
                updateLocation(lat, lng);

                if (results[0].display_name && businessAddress) {
                    businessAddress.value = results[0].display_name;
                }
            } catch (error) {
                console.error('Search failed:', error);
                window.showNotification?.('error', 'Unable to search location right now.');
            }
        }

        marker.on('dragend', async function (e) {
            const position = e.target.getLatLng();
            updateLocation(position.lat, position.lng);
            await updateAddressFromCoordinates(position.lat, position.lng);
        });

        map.on('click', async function (e) {
            marker.setLatLng(e.latlng);
            updateLocation(e.latlng.lat, e.latlng.lng);
            await updateAddressFromCoordinates(e.latlng.lat, e.latlng.lng);
        });

        if (searchButton) {
            searchButton.addEventListener('click', async function () {
                const query = searchInput?.value?.trim();
                if (!query) return;
                await searchLocation(query);
            });
        }

        if (searchInput) {
            searchInput.addEventListener('keydown', async function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const query = searchInput.value.trim();
                    if (!query) return;
                    await searchLocation(query);
                }
            });
        }

        updateLocation(defaultLat, defaultLng);
    });
</script>

@endsection
