<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\User;
use App\Models\VendorProfile;
use App\Notifications\VendorRequestSubmittedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class VendorRegisterController extends Controller
{
    private const SESSION_KEY = 'vendor_register_user_id';
    private const DOCUMENT_PURPOSE = 'vendor_verification';

    private const REQUIRED_DOCUMENT_TYPES = [
        'national_id',
        'business_license',
        'proof_of_address',
    ];

    private const ALL_DOCUMENT_TYPES = [
        'national_id',
        'business_license',
        'tax_certificate',
        'proof_of_address',
    ];

    public function showStep1(Request $request)
    {
        if (session()->has(self::SESSION_KEY) && !$request->boolean('edit')) {
            return redirect($this->resolveRegistrationRoute());
        }

        $registrationUser = session()->has(self::SESSION_KEY)
            ? User::find(session(self::SESSION_KEY))
            : null;

        return view('user.pages.vendor-register.step1', [
            'registrationUser' => $registrationUser,
            'hasDraft' => (bool) $registrationUser,
        ]);
    }

    public function storeStep1(Request $request)
    {
        $existingUser = session()->has(self::SESSION_KEY)
            ? User::with('vendorProfile')->find(session(self::SESSION_KEY))
            : null;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . ($existingUser?->id ?? 'NULL')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        DB::beginTransaction();

        try {
            if ($existingUser) {
                $existingUser->update([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                ]);

                $existingUser->vendorProfile()->update([
                    'full_name' => $validated['name'],
                    'current_step' => 1,
                ]);

                $user = $existingUser;
            } else {
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'role' => 'vendor',
                    'status' => 'active',
                    'vendor_status' => 'draft',
                ]);

                VendorProfile::create([
                    'user_id' => $user->id,
                    'full_name' => $validated['name'],
                    'phone' => '',
                    'national_id_number' => '',
                    'residential_address' => '',
                    'business_name' => '',
                    'business_type' => '',
                    'business_registration_number' => null,
                    'tax_id_number' => null,
                    'business_address' => '',
                    'current_step' => 1,
                    'is_submitted' => false,
                    'status' => 'draft',
                ]);
            }

            session([self::SESSION_KEY => $user->id]);

            DB::commit();

            return redirect()->route('vendor.register.step2')
                ->with('success', 'Account information saved successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withErrors(['error' => 'Unable to save account information. Please try again.'])
                ->withInput();
        }
    }

    public function exitDraft()
    {
        $userId = session(self::SESSION_KEY);

        if (!$userId) {
            return redirect()->route('vendor.register.landing');
        }

        DB::beginTransaction();

        try {
            $user = User::with(['vendorProfile', 'vendorDocuments'])->find($userId);

            if ($user && $user->role === 'vendor' && $user->vendor_status === 'draft') {
                $documents = Document::where('user_id', $user->id)
                    ->where('purpose', self::DOCUMENT_PURPOSE)
                    ->get();

                foreach ($documents as $document) {
                    if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                        Storage::disk('public')->delete($document->file_path);
                    }
                }

                Document::where('user_id', $user->id)
                    ->where('purpose', self::DOCUMENT_PURPOSE)
                    ->delete();

                $user->vendorProfile()->delete();
                $user->delete();
            }

            session()->forget(self::SESSION_KEY);

            DB::commit();

            return redirect()->route('vendor.register.landing')
                ->with('success', 'Vendor registration draft deleted successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors([
                'error' => 'Unable to exit registration right now. Please try again.',
            ]);
        }
    }

    public function showStep2()
    {
        $user = $this->getRegistrationUser();
        $vendorProfile = $user->vendorProfile;

    return view('user.pages.vendor-register.step2', compact('user', 'vendorProfile'));
    }

    public function storeStep2(Request $request)
    {
        $user = $this->getRegistrationUser();

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'regex:/^\d{10}$/'],
            'national_id_number' => ['required', 'string', 'max:100'],
            'residential_address' => ['required', 'string', 'max:1000'],
        ], [
            'phone.regex' => 'Phone number must be exactly 10 digits.',
        ]);

        DB::beginTransaction();

        try {
            $user->update([
                'name' => $validated['full_name'],
                'phone' => $validated['phone'],
            ]);

            $user->vendorProfile()->update([
                'full_name' => $validated['full_name'],
                'phone' => $validated['phone'],
                'national_id_number' => $validated['national_id_number'],
                'residential_address' => $validated['residential_address'],
                'current_step' => 2,
            ]);

            DB::commit();

            return redirect()->route('vendor.register.step3')
                ->with('success', 'Personal information saved successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withErrors(['error' => 'Unable to save personal information. Please try again.'])
                ->withInput();
        }
    }

    public function showStep3()
    {
        $user = $this->getRegistrationUser();
        $vendorProfile = $user->vendorProfile;

    return view('user.pages.vendor-register.step3', compact('user', 'vendorProfile'));
    }

    public function storeStep3(Request $request)
    {
        $user = $this->getRegistrationUser();

        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'business_type' => ['required', 'string', 'max:100'],
            'business_registration_number' => ['required', 'string', 'max:100'],
            'tax_id_number' => ['required', 'string', 'max:100'],
            'business_address' => ['required', 'string', 'max:1000'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
        ]);

        DB::beginTransaction();

        try {
            $user->vendorProfile()->update([
                'business_name' => $validated['business_name'],
                'business_type' => $validated['business_type'],
                'business_registration_number' => $validated['business_registration_number'],
                'tax_id_number' => $validated['tax_id_number'],
                'business_address' => $validated['business_address'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'current_step' => 3,
            ]);
            DB::commit();

            return redirect()->route('vendor.register.step4')
                ->with('success', 'Business information saved successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withErrors(['error' => 'Unable to save business information. Please try again.'])
                ->withInput();
        }
    }

    public function showStep4()
    {
        $user = $this->getRegistrationUser();
        $documents = $user->vendorDocuments()->get()->keyBy('type');

    return view('user.pages.vendor-register.step4', compact('user', 'documents'));
    }

    public function storeStep4(Request $request)
    {
        $user = $this->getRegistrationUser();

        $request->validate([
            'national_id' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'business_license' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'tax_certificate' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'proof_of_address' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $existingDocs = $user->vendorDocuments()->pluck('type')->toArray();

        foreach (self::REQUIRED_DOCUMENT_TYPES as $type) {
            if (!$request->hasFile($type) && !in_array($type, $existingDocs)) {
                return back()
                    ->withErrors([$type => ucfirst(str_replace('_', ' ', $type)) . ' is required.'])
                    ->withInput();
            }
        }

        DB::beginTransaction();

        try {
            foreach (self::ALL_DOCUMENT_TYPES as $type) {
                if ($request->hasFile($type)) {
                    $this->storeOrUpdateDocument($user, $request->file($type), $type);
                }
            }

            $user->vendorProfile()->update([
                'current_step' => 4,
            ]);

            DB::commit();

            return redirect()->route('vendor.register.review')
                ->with('success', 'Documents uploaded successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors([
                'upload' => 'Document upload failed. Please try again.',
            ]);
        }
    }

    public function showReview()
    {
        $user = $this->getRegistrationUser();
        $vendorProfile = $user->vendorProfile;
        $documents = $user->vendorDocuments()->get()->keyBy('type');

        foreach (self::REQUIRED_DOCUMENT_TYPES as $docType) {
            if (!isset($documents[$docType])) {
                return redirect()->route('vendor.register.step4')
                    ->withErrors(['documents' => 'Please upload all required documents before review.']);
            }
        }

    return view('user.pages.vendor-register.review', compact('user', 'vendorProfile', 'documents'));
    }

    public function submit()
    {
        $user = $this->getRegistrationUser();
        $vendorProfile = $user->vendorProfile;
        $documents = $user->vendorDocuments()->pluck('type')->toArray();

        foreach (self::REQUIRED_DOCUMENT_TYPES as $docType) {
            if (!in_array($docType, $documents)) {
                return redirect()->route('vendor.register.step4')
                    ->withErrors(['documents' => 'Please upload all required documents before submitting.']);
            }
        }

        DB::beginTransaction();

        try {
            $vendorProfile->update([
                'current_step' => 5,
                'is_submitted' => true,
                'status' => 'pending',
            ]);

            $user->update([
                'vendor_status' => 'pending',
            ]);

            session()->forget(self::SESSION_KEY);

            DB::commit();

            $admins = User::query()
                ->where('role', 'admin')
                ->whereNotNull('email')
                ->get();

            if ($admins->isNotEmpty()) {
                Notification::sendNow($admins, new VendorRequestSubmittedNotification($user));
            }

            return redirect()->route('login')
                ->with('success', 'Vendor application submitted successfully. Please wait for admin review.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors([
                'submit' => 'Unable to submit application. Please try again.',
            ]);
        }
    }

    public function verification()
    {
        $vendor = Auth::user();
        /** @var \App\Models\User $vendor */

        $vendor->load([
            'vendorProfile',
            'vendorDocuments' => function ($query) {
                $query->latest();
            },
        ]);

        return view('vendor.pages.verification', compact('vendor'));
    }

    public function resubmit(Request $request)
    {
        $vendor = Auth::user();
        /** @var \App\Models\User $vendor */

        $validated = $request->validate([
            'type' => ['required', 'in:national_id,business_license,tax_certificate,proof_of_address'],
            'document' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        DB::beginTransaction();

        try {
            $this->storeOrUpdateDocument($vendor, $request->file('document'), $validated['type']);

            $vendor->update([
                'vendor_status' => 'pending',
                'verification_note' => null,
            ]);

            $vendor->vendorProfile()->update([
                'status' => 'pending',
                'remarks' => null,
                'reviewed_by' => null,
                'reviewed_at' => null,
            ]);

            DB::commit();

            return back()->with('success', ucfirst(str_replace('_', ' ', $validated['type'])) . ' resubmitted successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors([
                'document' => 'Document resubmission failed. Please try again.',
            ]);
        }
    }

    private function getRegistrationUser(): User
    {
        $userId = session(self::SESSION_KEY);

        abort_unless($userId, 404);

        return User::with(['vendorProfile', 'vendorDocuments'])->findOrFail($userId);
    }

    private function storeOrUpdateDocument(User $user, $file, string $type): void
    {
        $oldDocument = Document::where('user_id', $user->id)
            ->where('purpose', self::DOCUMENT_PURPOSE)
            ->where('type', $type)
            ->first();

        if ($oldDocument && $oldDocument->file_path && Storage::disk('public')->exists($oldDocument->file_path)) {
            Storage::disk('public')->delete($oldDocument->file_path);
        }

        $path = $file->store('vendor-documents', 'public');

        Document::updateOrCreate(
            [
                'user_id' => $user->id,
                'purpose' => self::DOCUMENT_PURPOSE,
                'type' => $type,
            ],
            [
                'document_number' => null,
                'issued_at' => null,
                'expires_at' => null,
                'file_path' => $path,
                'file_type' => $file->getClientMimeType(),
                'status' => 'pending',
                'reviewed_by' => null,
                'reviewed_at' => null,
                'remarks' => null,
            ]
        );
    }
    public function landing()
    {
        return view('user.pages.vendor-register.index', [
            'startRoute' => $this->resolveRegistrationRoute(),
            'ctaLabel' => session()->has(self::SESSION_KEY) ? 'Continue Application' : 'Become a Partner',
        ]);
    }

    private function resolveRegistrationRoute(): string
    {
        $userId = session(self::SESSION_KEY);

        if (!$userId) {
            return route('vendor.register.step1');
        }

        $user = User::with('vendorProfile')->find($userId);

        if (!$user || !$user->vendorProfile) {
            return route('vendor.register.step1');
        }

        $step = (int) $user->vendorProfile->current_step;

        return match ($step) {
            1 => route('vendor.register.step2'),
            2 => route('vendor.register.step3'),
            3 => route('vendor.register.step4'),
            4 => route('vendor.register.review'),
            default => route('vendor.register.step1'),
        };
    }
}
