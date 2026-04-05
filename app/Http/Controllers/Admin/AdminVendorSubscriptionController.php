<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\VendorProfile;
use App\Models\VendorSubscription;
use App\Notifications\VendorSubscriptionActivatedNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class AdminVendorSubscriptionController extends Controller
{
    public function index()
    {
        $vendorSubscriptions = VendorSubscription::with(['vendor', 'plan'])
            ->latest()
            ->paginate(10);

        return view('admin.vendor-subscriptions.index', compact('vendorSubscriptions'));
    }

    public function create()
    {
        $vendors = VendorProfile::with('user')
            ->where('status', 'approved')
            ->latest()
            ->get();

        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('price')
            ->get();

        return view('admin.vendor-subscriptions.create', compact('vendors', 'plans'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $preparedData = $this->prepareData($data);

        if ($preparedData['status'] === 'active') {
            $this->deactivateOtherActiveSubscriptions($preparedData['vendor_id']);
        }

        $vendorSubscription = VendorSubscription::create($preparedData);

        if ($preparedData['status'] === 'active') {
            $vendorSubscription->load(['vendor', 'plan']);

            if ($vendorSubscription->vendor && !empty($vendorSubscription->vendor->email)) {
                Notification::sendNow(
                    $vendorSubscription->vendor,
                    new VendorSubscriptionActivatedNotification($vendorSubscription, false)
                );
            }
        }

        return redirect()
            ->route('admin.vendor-subscriptions.index')
            ->with('success', 'Vendor subscription created successfully.');
    }

    public function edit(VendorSubscription $vendorSubscription)
    {
        $vendors = VendorProfile::with('user')
            ->where('status', 'approved')
            ->latest()
            ->get();

        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('price')
            ->get();

        return view('admin.vendor-subscriptions.edit', compact(
            'vendorSubscription',
            'vendors',
            'plans'
        ));
    }

    public function show(VendorSubscription $vendorSubscription)
    {
        $vendorSubscription->load(['vendor', 'plan']);

        return view('admin.vendor-subscriptions.show', compact('vendorSubscription'));
    }

    public function update(Request $request, VendorSubscription $vendorSubscription)
    {
        $data = $this->validateData($request);

        $preparedData = $this->prepareData($data);

        $oldStatus = $vendorSubscription->status;
        $oldPlanId = $vendorSubscription->subscription_plan_id;
        $oldStartsAt = optional($vendorSubscription->starts_at)?->toDateTimeString();
        $oldEndsAt = optional($vendorSubscription->ends_at)?->toDateTimeString();

        if ($preparedData['status'] === 'active') {
            $this->deactivateOtherActiveSubscriptions(
                $preparedData['vendor_id'],
                $vendorSubscription->id
            );
        }

        $vendorSubscription->update($preparedData);

        if ($preparedData['status'] === 'active') {
            $newStartsAt = optional($vendorSubscription->starts_at)?->toDateTimeString();
            $newEndsAt = optional($vendorSubscription->ends_at)?->toDateTimeString();

            $shouldNotify =
                $oldStatus !== 'active'
                || (int) $oldPlanId !== (int) $vendorSubscription->subscription_plan_id
                || $oldStartsAt !== $newStartsAt
                || $oldEndsAt !== $newEndsAt;

            if ($shouldNotify) {
                $vendorSubscription->load(['vendor', 'plan']);

                if ($vendorSubscription->vendor && !empty($vendorSubscription->vendor->email)) {
                    Notification::sendNow(
                        $vendorSubscription->vendor,
                        new VendorSubscriptionActivatedNotification($vendorSubscription, false)
                    );
                }
            }
        }

        return redirect()
            ->route('admin.vendor-subscriptions.index')
            ->with('success', 'Vendor subscription updated successfully.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'vendor_id' => ['required', 'exists:users,id'],
            'subscription_plan_id' => ['required', 'exists:subscription_plans,id'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'status' => ['required', 'in:active,expired,cancelled'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
        ]);
    }

    private function prepareData(array $data): array
    {
        $plan = SubscriptionPlan::findOrFail($data['subscription_plan_id']);
        $startsAt = Carbon::parse($data['starts_at']);

        $endsAt = !empty($data['ends_at'])
            ? Carbon::parse($data['ends_at'])
            : $this->calculateEndDate($plan, $startsAt);

        return [
            'vendor_id' => $data['vendor_id'],
            'subscription_plan_id' => $data['subscription_plan_id'],
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'status' => $data['status'],
            'amount_paid' => $data['amount_paid'],
        ];
    }

    private function calculateEndDate(SubscriptionPlan $plan, Carbon $startsAt): ?Carbon
    {
        return match ($plan->billing_cycle) {
            'monthly' => (clone $startsAt)->addMonth(),
            'yearly' => (clone $startsAt)->addYear(),
            default => null,
        };
    }

    private function deactivateOtherActiveSubscriptions(int $vendorId, ?int $exceptId = null): void
    {
        VendorSubscription::where('vendor_id', $vendorId)
            ->where('status', 'active')
            ->when($exceptId, function ($query) use ($exceptId) {
                $query->where('id', '!=', $exceptId);
            })
            ->update(['status' => 'expired']);
    }
}
