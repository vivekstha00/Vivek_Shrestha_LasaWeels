<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPayment;
use App\Models\SubscriptionPlan;
use App\Models\VendorSubscription;
use App\Services\VendorSubscriptionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;

class VendorSubscriptionPaymentController extends Controller
{
    public function index()
    {
        $vendorId = Auth::id();

        $plans = SubscriptionPlan::query()
            ->where('is_active', true)
            ->whereIn('billing_cycle', ['monthly', 'yearly'])
            ->where('price', '>', 0)
            ->orderBy('price')
            ->get();

        $subscriptionSummary = app(VendorSubscriptionService::class)->getSummary($vendorId);

        $currentActiveSubscription = VendorSubscription::with('plan')
            ->where('vendor_id', $vendorId)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->latest('ends_at')
            ->first();

        $currentActivePlanId = $currentActiveSubscription?->subscription_plan_id;

        $subscriptionPayments = SubscriptionPayment::with('plan')
            ->where('vendor_id', $vendorId)
            ->latest()
            ->paginate(10);

        return view('vendor.pages.subscriptions.index', compact(
            'plans',
            'subscriptionSummary',
            'subscriptionPayments',
            'currentActivePlanId'
        ));
    }

    public function initiate(Request $request, SubscriptionPlan $subscriptionPlan)
    {
        $vendor = Auth::user();

        abort_unless($vendor && $vendor->role === 'vendor', 403);

        if (! $subscriptionPlan->is_active || ! in_array($subscriptionPlan->billing_cycle, ['monthly', 'yearly'])) {
            return redirect()
                ->route('vendor.subscriptions.index')
                ->withErrors(['subscription' => 'Selected subscription plan is not available for purchase.']);
        }

        $subscriptionPayment = SubscriptionPayment::create([
            'vendor_id' => $vendor->id,
            'subscription_plan_id' => $subscriptionPlan->id,
            'amount' => $subscriptionPlan->price,
            'payment_gateway' => 'khalti',
            'status' => 'pending',
        ]);

        $purchaseOrderId = 'SUB-' . $subscriptionPayment->id . '-' . time();

        $subscriptionPayment->update([
            'purchase_order_id' => $purchaseOrderId,
        ]);

        $payload = [
            'return_url' => route('vendor.subscriptions.callback'),
            'website_url' => url('/'),
            'amount' => (int) round($subscriptionPlan->price * 100),
            'purchase_order_id' => $purchaseOrderId,
            'purchase_order_name' => 'Vendor Subscription - ' . $subscriptionPlan->name,
            'customer_info' => [
                'name' => $vendor->name,
                'email' => $vendor->email,
            ],
        ];

        $response = Http::withHeaders([
            'Authorization' => 'key ' . config('services.khalti.secret'),
            'Content-Type' => 'application/json',
        ])->post('https://dev.khalti.com/api/v2/epayment/initiate/', $payload);

        if (! $response->successful() || empty($response->json('payment_url'))) {
            $subscriptionPayment->update([
                'status' => 'failed',
                'gateway_payload' => $response->json(),
            ]);

            return redirect()
                ->route('vendor.subscriptions.index')
                ->withErrors([
                    'subscription' => 'Unable to initiate subscription payment. Please try again.'
                ]);
        }

        Cookie::queue('khalti_subscription_payment_id', $subscriptionPayment->id, 30);

        return redirect()->away($response->json('payment_url'));
    }

    public function khaltiCallback(Request $request)
    {
        $pidx = $request->query('pidx');
        $purchaseOrderId = $request->query('purchase_order_id');

        if (! $pidx) {
            return redirect()
                ->route('vendor.subscriptions.index')
                ->withErrors(['subscription' => 'Invalid Khalti callback.']);
        }

        $subscriptionPayment = null;

        if ($purchaseOrderId) {
            $subscriptionPayment = SubscriptionPayment::with(['vendor', 'plan'])
                ->where('purchase_order_id', $purchaseOrderId)
                ->first();
        }

        if (! $subscriptionPayment) {
            $subscriptionPaymentId = Cookie::get('khalti_subscription_payment_id');

            if ($subscriptionPaymentId) {
                $subscriptionPayment = SubscriptionPayment::with(['vendor', 'plan'])
                    ->find($subscriptionPaymentId);
            }
        }

        if (! $subscriptionPayment) {
            return redirect()
                ->route('vendor.subscriptions.index')
                ->withErrors(['subscription' => 'Subscription payment record not found.']);
        }

        $lookup = Http::withHeaders([
            'Authorization' => 'key ' . config('services.khalti.secret'),
            'Content-Type' => 'application/json',
        ])->post('https://dev.khalti.com/api/v2/epayment/lookup/', [
            'pidx' => $pidx,
        ]);

        $status = $lookup->successful()
            ? ($lookup->json('status') ?? 'failed')
            : 'failed';

        $alreadyCompleted = $subscriptionPayment->status === 'completed';

        $subscriptionPayment->update([
            'gateway_reference' => $pidx,
            'gateway_payload' => $lookup->json(),
        ]);

        if ($alreadyCompleted) {
            Cookie::queue(Cookie::forget('khalti_subscription_payment_id'));

            return redirect()
                ->route('vendor.subscriptions.index')
                ->with('info', 'This subscription payment has already been processed.');
        }

        if ($status === 'Completed') {
            $subscriptionPayment->update([
                'status' => 'completed',
                'paid_at' => now(),
            ]);

            $plan = $subscriptionPayment->plan;
            $vendorId = $subscriptionPayment->vendor_id;

            $currentActiveSubscription = VendorSubscription::where('vendor_id', $vendorId)
                ->where('status', 'active')
                ->where(function ($q) {
                    $q->whereNull('ends_at')
                        ->orWhere('ends_at', '>=', now());
                })
                ->latest('ends_at')
                ->first();

            // Case 1: same active plan exists -> extend its end date
            if (
                $currentActiveSubscription &&
                $currentActiveSubscription->subscription_plan_id == $plan->id
            ) {
                $baseDate = now();

                if ($currentActiveSubscription->ends_at && $currentActiveSubscription->ends_at->gt(now())) {
                    $baseDate = $currentActiveSubscription->ends_at->copy();
                }

                $newEndsAt = $this->calculateEndDate($plan, $baseDate);

                $currentActiveSubscription->update([
                    'ends_at' => $newEndsAt,
                    'amount_paid' => (float) $currentActiveSubscription->amount_paid + (float) $subscriptionPayment->amount,
                ]);

                Cookie::queue(Cookie::forget('khalti_subscription_payment_id'));

                return redirect()
                    ->route('vendor.subscriptions.index')
                    ->with('success', 'Subscription renewed successfully.');
            }

            // Case 2: different plan or no active plan -> activate new plan now
            VendorSubscription::where('vendor_id', $vendorId)
                ->where('status', 'active')
                ->update(['status' => 'expired']);

            $startsAt = now();
            $endsAt = $this->calculateEndDate($plan, $startsAt);

            VendorSubscription::create([
                'vendor_id' => $vendorId,
                'subscription_plan_id' => $plan->id,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'status' => 'active',
                'amount_paid' => $subscriptionPayment->amount,
            ]);

            Cookie::queue(Cookie::forget('khalti_subscription_payment_id'));

            return redirect()
                ->route('vendor.subscriptions.index')
                ->with('success', 'Subscription payment successful. Your plan is now active.');
        }

        $subscriptionPayment->update([
            'status' => 'failed',
        ]);

        Cookie::queue(Cookie::forget('khalti_subscription_payment_id'));

        return redirect()
            ->route('vendor.subscriptions.index')
            ->withErrors(['subscription' => 'Subscription payment failed. Please try again.']);
    }

    private function calculateEndDate(SubscriptionPlan $plan, Carbon $baseDate): ?Carbon
    {
        return match ($plan->billing_cycle) {
            'monthly' => (clone $baseDate)->addMonth(),
            'yearly' => (clone $baseDate)->addYear(),
            default => null,
        };
    }
}
