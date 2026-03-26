@extends('user.pages.vendor-register.layout', ['title' => 'Vendor Register - Step 3', 'currentStep' => 3])

@section('register-content')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <h3 class="section-title mb-1">Business Information</h3>
    <p class="text-muted mb-4">Tell us about your business and mark its exact location on the map.</p>

    <form action="{{ route('vendor.register.step3.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label fw-semibold">Business Name</label>
            <input type="text" name="business_name" class="form-control form-control-lg rounded-3"
                   value="{{ old('business_name', $vendorProfile->business_name) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Business Type</label>
            <select name="business_type" class="form-select form-select-lg rounded-3" required>
                <option value="">Select Business Type</option>
                <option value="individual" {{ old('business_type', $vendorProfile->business_type) == 'individual' ? 'selected' : '' }}>Individual / Sole Proprietor</option>
                <option value="partnership" {{ old('business_type', $vendorProfile->business_type) == 'partnership' ? 'selected' : '' }}>Partnership</option>
                <option value="company" {{ old('business_type', $vendorProfile->business_type) == 'company' ? 'selected' : '' }}>Company</option>
            </select>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Business Registration Number</label>
                <input type="text" name="business_registration_number" class="form-control form-control-lg rounded-3"
                       value="{{ old('business_registration_number', $vendorProfile->business_registration_number) }}">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Tax / VAT Number</label>
                <input type="text" name="tax_id_number" class="form-control form-control-lg rounded-3"
                       value="{{ old('tax_id_number', $vendorProfile->tax_id_number) }}">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Business Address</label>
            <textarea id="business_address" name="business_address" rows="3" class="form-control form-control-lg rounded-3" required>{{ old('business_address', $vendorProfile->business_address) }}</textarea>
            <div class="form-text">Search a place, click on the map, or drag the marker to set the exact business location.</div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Search Location</label>
            <div class="input-group">
                <input type="text" id="location_search" class="form-control form-control-lg rounded-start-3" placeholder="Search business location">
                <button type="button" id="search_location_btn" class="btn btn-outline-dark">Search</button>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Pin Business Location</label>
            <div id="map" style="height: 380px; border-radius: 16px; overflow: hidden; border: 1px solid #dee2e6;"></div>
            <div class="form-text">Click on the map or drag the marker to set the exact business location.</div>
        </div>

        <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $vendorProfile->latitude) }}">
        <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $vendorProfile->longitude) }}">

        <div class="row mb-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Latitude</label>
                <input type="text" id="latitude_display" class="form-control rounded-3"
                       value="{{ old('latitude', $vendorProfile->latitude) }}" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Longitude</label>
                <input type="text" id="longitude_display" class="form-control rounded-3"
                       value="{{ old('longitude', $vendorProfile->longitude) }}" readonly>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('vendor.register.step2') }}" class="btn btn-outline-secondary btn-lg rounded-3 px-4">Previous</a>
            <button type="submit" class="btn btn-dark btn-lg rounded-3 px-4">Next Step</button>
        </div>
    </form>

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

            const defaultLat = parseFloat(latitudeInput.value) || 28.2096;
            const defaultLng = parseFloat(longitudeInput.value) || 83.9856;

            const map = L.map('map').setView([defaultLat, defaultLng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            const marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

            function updateLocation(lat, lng) {
                latitudeInput.value = lat.toFixed(7);
                longitudeInput.value = lng.toFixed(7);
                latitudeDisplay.value = lat.toFixed(7);
                longitudeDisplay.value = lng.toFixed(7);
            }

            async function updateAddressFromCoordinates(lat, lng) {
                try {
                    const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`);
                    const data = await response.json();

                    if (data && data.display_name) {
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

                    if (results.length > 0) {
                        const lat = parseFloat(results[0].lat);
                        const lng = parseFloat(results[0].lon);

                        map.setView([lat, lng], 16);
                        marker.setLatLng([lat, lng]);
                        updateLocation(lat, lng);

                        if (results[0].display_name) {
                            businessAddress.value = results[0].display_name;
                        }
                    } else {
                        alert('Location not found. Please try another search.');
                    }
                } catch (error) {
                    console.error('Search failed:', error);
                    alert('Unable to search location right now.');
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

            searchButton.addEventListener('click', async function () {
                const query = searchInput.value.trim();
                if (!query) return;
                await searchLocation(query);
            });

            searchInput.addEventListener('keydown', async function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const query = searchInput.value.trim();
                    if (!query) return;
                    await searchLocation(query);
                }
            });

            updateLocation(defaultLat, defaultLng);
        });
    </script>
@endsection
