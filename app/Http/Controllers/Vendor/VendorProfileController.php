<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\User;
use App\Notifications\VendorRequestSubmittedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class VendorProfileController extends Controller
{
    private const DOCUMENT_PURPOSE = 'vendor_verification';

    private const DOCUMENT_TYPES = [
        'national_id',
        'business_license',
        'tax_certificate',
        'proof_of_address',
    ];

    private const DOCUMENT_LABELS = [
        'national_id' => 'National ID',
        'business_license' => 'Business License',
        'tax_certificate' => 'Tax Certificate',
        'proof_of_address' => 'Proof of Address',
    ];

    public function edit()
    {
        /** @var \App\Models\User $vendor */
        $vendor = Auth::user();
        $vendor->load('vendorProfile');

        $documents = $vendor->vendorDocuments()
            ->where('purpose', self::DOCUMENT_PURPOSE)
            ->latest()
            ->get()
            ->keyBy('type');

        $docLabels = self::DOCUMENT_LABELS;

        return view('vendor.pages.profile', compact('vendor', 'documents', 'docLabels'));
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $vendor */
        $vendor = Auth::user();
        $vendor->load('vendorProfile');

        if (! $vendor->vendorProfile) {
            return back()->withErrors([
                'profile' => 'Vendor profile is not initialized yet. Please complete vendor registration first.',
            ]);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => [
                'nullable',
                'regex:/^\d{10}$/',
                Rule::unique('users', 'phone')->ignore($vendor->id),
            ],
            'address' => ['nullable', 'string', 'max:255'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'business_name' => ['required', 'string', 'max:255'],
            'business_type' => ['required', 'string', 'max:100'],
            'business_registration_number' => ['required', 'string', 'max:100'],
            'tax_id_number' => ['required', 'string', 'max:100'],
            'business_address' => ['required', 'string', 'max:1000'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'national_id' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'business_license' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'tax_certificate' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'proof_of_address' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'phone.regex' => 'Phone number must be exactly 10 digits.',
            'profile_image.image' => 'Please upload a valid image file.',
            'profile_image.max' => 'Profile picture size must be less than 2MB.',
        ]);

        $majorBusinessChanged =
            $vendor->vendorProfile->business_name !== $data['business_name'] ||
            $vendor->vendorProfile->business_type !== $data['business_type'] ||
            $vendor->vendorProfile->business_registration_number !== $data['business_registration_number'] ||
            $vendor->vendorProfile->tax_id_number !== $data['tax_id_number'] ||
            $vendor->vendorProfile->business_address !== $data['business_address'] ||
            (string) $vendor->vendorProfile->latitude !== (string) $data['latitude'] ||
            (string) $vendor->vendorProfile->longitude !== (string) $data['longitude'];

        $documentChanged = false;

        DB::beginTransaction();

        try {
            $userData = [
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
            ];

            if ($request->hasFile('profile_image')) {
                if ($vendor->profile_image && Storage::disk('public')->exists($vendor->profile_image)) {
                    Storage::disk('public')->delete($vendor->profile_image);
                }

                $userData['profile_image'] = $request->file('profile_image')
                    ->store('profile-images', 'public');
            }

            $vendor->update($userData);

            $vendor->vendorProfile()->update([
                'full_name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'business_name' => $data['business_name'],
                'business_type' => $data['business_type'],
                'business_registration_number' => $data['business_registration_number'],
                'tax_id_number' => $data['tax_id_number'],
                'business_address' => $data['business_address'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
            ]);

            foreach (self::DOCUMENT_TYPES as $type) {
                if ($request->hasFile($type)) {
                    $documentChanged = true;
                    $this->storeOrUpdateVendorDocument($vendor, $request->file($type), $type);
                }
            }

            if ($vendor->vendor_status === 'approved' && ($majorBusinessChanged || $documentChanged)) {
                $vendor->update([
                    'vendor_status' => 'resubmit',
                    'verification_note' => null,
                ]);

                $vendor->vendorProfile()->update([
                    'status' => 'resubmit',
                    'remarks' => null,
                    'reviewed_by' => null,
                    'reviewed_at' => null,
                ]);

                $admins = User::query()
                    ->where('role', 'admin')
                    ->whereNotNull('email')
                    ->get();

                if ($admins->isNotEmpty()) {
                    Notification::sendNow($admins, new VendorRequestSubmittedNotification($vendor->fresh()));
                }

                DB::commit();

                return redirect()
                    ->route('vendor.verification')
                    ->with('success', 'Profile updated. Your business changes were submitted for admin re-verification.');
            }

            DB::commit();

            return back()->with('success', 'Profile updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors([
                'profile' => 'Unable to update profile right now. Please try again.',
            ])->withInput();
        }
    }

    private function storeOrUpdateVendorDocument(User $vendor, $file, string $type): void
    {
        $oldDocument = Document::where('user_id', $vendor->id)
            ->where('purpose', self::DOCUMENT_PURPOSE)
            ->where('type', $type)
            ->first();

        if ($oldDocument && $oldDocument->file_path && Storage::disk('public')->exists($oldDocument->file_path)) {
            Storage::disk('public')->delete($oldDocument->file_path);
        }

        $path = $file->store('vendor-documents', 'public');

        Document::updateOrCreate(
            [
                'user_id' => $vendor->id,
                'purpose' => self::DOCUMENT_PURPOSE,
                'type' => $type,
            ],
            [
                'document_number' => null,
                'issued_at' => null,
                'expires_at' => null,
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'status' => 'pending',
                'reviewed_by' => null,
                'reviewed_at' => null,
                'remarks' => null,
            ]
        );
    }
}
