<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\VendorProfile;
use App\Notifications\VendorApprovedNotification;
use App\Notifications\VendorReSubmissionNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class AdminDocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = Document::with(['user'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;

        $query->whereHas('user', function ($q) use ($search) {
            $q->where('role', 'user')
              ->where(function ($sub) use ($search) {
                  $sub->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
              });
            });
        }

        $documents = $query->paginate(10)->withQueryString();

        return view('admin.documents.index', compact('documents'));
    }

    public function show(Document $document)
    {
        $document->load('user');

        return view('admin.documents.show', compact('document'));
    }

    public function approve(Request $request, Document $document)
    {
        $document->update([
            'status'      => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'remarks'     => null,
        ]);

        $this->syncVendorVerificationState($document);

        return $this->redirectAfterReview(
            $request,
            ucfirst($document->type) . ' approved successfully.'
        );
    }

    public function reject(Request $request, Document $document)
    {
        $request->validate([
            'remarks' => ['required', 'string', 'max:500'],
        ]);

        $document->update([
            'status'      => 'rejected',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'remarks'     => $request->remarks,
        ]);

        if ($document->purpose === 'vendor_verification' && $document->user && !empty($document->user->email)) {
            Notification::sendNow(
                $document->user,
                new VendorReSubmissionNotification(
                    ucfirst(str_replace('_', ' ', $document->type)) . ': ' . $request->remarks
                )
            );
        }

        $this->syncVendorVerificationState($document);

        return $this->redirectAfterReview(
            $request,
            ucfirst($document->type) . ' rejected successfully.'
        );
    }

    private function redirectAfterReview(Request $request, string $message)
    {
        $redirectTo = $request->input('redirect_to');

        if (is_string($redirectTo) && str_starts_with($redirectTo, url('/'))) {
            return redirect()->to($redirectTo)->with('success', $message);
        }

        return back()->with('success', $message);
    }

    private function syncVendorVerificationState(Document $document): void
    {
        if ($document->purpose !== 'vendor_verification' || !$document->user || $document->user->role !== 'vendor') {
            return;
        }

        $vendor = $document->user;

        $requiredTypes = ['national_id', 'business_license', 'proof_of_address'];
        $requiredDocs = Document::query()
            ->where('user_id', $vendor->id)
            ->where('purpose', 'vendor_verification')
            ->whereIn('type', $requiredTypes)
            ->get()
            ->keyBy('type');

        $hasMissingRequired = collect($requiredTypes)->contains(fn ($type) => !isset($requiredDocs[$type]));
        $hasRejected = $requiredDocs->contains(fn ($doc) => $doc->status === 'rejected');
        $allApproved = !$hasMissingRequired && $requiredDocs->every(fn ($doc) => $doc->status === 'approved');

        /** @var VendorProfile|null $profile */
        $profile = $vendor->vendorProfile;
        $currentVendorStatus = $vendor->vendor_status;

        if ($allApproved) {
            $vendor->update([
                'vendor_status' => 'approved',
                'verification_note' => null,
            ]);

            if ($profile) {
                $profile->update([
                    'status' => 'approved',
                    'reviewed_by' => Auth::id(),
                    'reviewed_at' => now(),
                    'remarks' => null,
                ]);
            }

            if ($currentVendorStatus !== 'approved' && !empty($vendor->email)) {
                Notification::sendNow($vendor, new VendorApprovedNotification());
            }

            return;
        }

        if ($hasRejected) {
            $vendor->update([
                'vendor_status' => 'resubmit',
            ]);

            if ($profile) {
                $profile->update([
                    'status' => 'resubmit',
                    'reviewed_by' => Auth::id(),
                    'reviewed_at' => now(),
                ]);
            }

            return;
        }

        $vendor->update([
            'vendor_status' => 'pending',
            'verification_note' => null,
        ]);

        if ($profile) {
            $profile->update([
                'status' => 'pending',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'remarks' => null,
            ]);
        }
    }
}
