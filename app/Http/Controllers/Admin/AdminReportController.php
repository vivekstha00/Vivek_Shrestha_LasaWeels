<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\SubscriptionPayment;
use App\Models\SubscriptionPlan;
use App\Models\VendorProfile;
use App\Models\VendorSubscription;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date'   => ['nullable', 'date', 'after_or_equal:from_date'],
        ]);

        $fromDate = !empty($filters['from_date']) ? Carbon::parse($filters['from_date'])->startOfDay() : null;
        $toDate   = !empty($filters['to_date'])   ? Carbon::parse($filters['to_date'])->endOfDay()     : null;

        // ── Bookings ──────────────────────────────────────────────────────────
        $bookingBaseQuery = Booking::query();
        $this->applyDateRange($bookingBaseQuery, 'pickup_datetime', $fromDate, $toDate);

        $bookingStatusBreakdown = (clone $bookingBaseQuery)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $bookingStatusSummary = [
            'pending'   => (int) ($bookingStatusBreakdown['pending']   ?? 0),
            'confirmed' => (int) ($bookingStatusBreakdown['confirmed'] ?? 0),
            'active'    => (int) ($bookingStatusBreakdown['active']    ?? 0),
            'completed' => (int) ($bookingStatusBreakdown['completed'] ?? 0),
            'cancelled' => (int) (($bookingStatusBreakdown['cancelled'] ?? 0) + ($bookingStatusBreakdown['cancel_requested'] ?? 0)),
        ];

        $totalBookings = array_sum($bookingStatusSummary);

        $bookingStatusChart = [
            'labels' => ['Pending', 'Confirmed', 'Active', 'Completed', 'Cancelled'],
            'data'   => array_values($bookingStatusSummary),
        ];

        // ── Top Booked Vehicles ───────────────────────────────────────────────
        $topBookedVehicles = (clone $bookingBaseQuery)
            ->select('vehicle_id')
            ->selectRaw('COUNT(*) as total_bookings')
            ->with('vehicle')
            ->groupBy('vehicle_id')
            ->orderByDesc('total_bookings')
            ->limit(7)
            ->get();

        $topVehiclesChart = [
            'labels' => $topBookedVehicles->map(function ($b) {
                $v = $b->vehicle;
                if (!$v) return 'Vehicle #' . $b->vehicle_id;

                // Build label from whichever columns exist
                return trim(collect([
                    $v->make  ?? null,
                    $v->model ?? null,
                    $v->year  ?? null,
                ])->filter()->implode(' ')) ?: 'Vehicle #' . $b->vehicle_id;
            })->values(),
            'data' => $topBookedVehicles->pluck('total_bookings')->values(),
        ];

        // ── Vendors ───────────────────────────────────────────────────────────
        $totalVendors    = VendorProfile::count();
        $activeVendors   = VendorProfile::where('status', 'approved')->count();
        $pendingVendors  = VendorProfile::whereIn('status', ['pending', 'resubmit'])->count();
        $inactiveVendors = max(0, $totalVendors - $activeVendors - $pendingVendors);

        // ── Payments ──────────────────────────────────────────────────────────
        $paymentBaseQuery = Payment::query()
            ->where('status', 'completed')
            ->where(function (Builder $q) {
                $q->whereNull('refund_status')
                    ->orWhere('refund_status', '!=', 'refunded');
            });

        $this->applyDateRange($paymentBaseQuery, 'created_at', $fromDate, $toDate);

        $grossBookingRevenue = (clone $paymentBaseQuery)->sum('amount');
        $platformCommission  = (clone $paymentBaseQuery)->sum('platform_commission');
        $vendorEarnings      = (clone $paymentBaseQuery)->sum('vendor_amount');

        $revenueComparisonChart = [
            'labels' => ['Gross Revenue', 'Platform Commission', 'Vendor Earnings'],
            'data'   => [
                (float) $grossBookingRevenue,
                (float) $platformCommission,
                (float) $vendorEarnings,
            ],
        ];

        // ── Subscriptions ─────────────────────────────────────────────────────
        $activeSubscriptions = VendorSubscription::query()
            ->where('status', 'active')
            ->where(function (Builder $q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->count();

        $expiredSubscriptions = VendorSubscription::query()
            ->where(function (Builder $q) {
                $q->where('status', 'expired')->orWhere('ends_at', '<', now());
            })
            ->count();

        $monthlyPlans = SubscriptionPlan::where('billing_cycle', 'monthly')->count();
        $yearlyPlans  = SubscriptionPlan::where('billing_cycle', 'yearly')->count();

        $subscriptionPaymentsQuery = SubscriptionPayment::query()->where('status', 'completed');
        $this->applyDateRange($subscriptionPaymentsQuery, 'created_at', $fromDate, $toDate);

        $subscriptionRevenueTotal = (clone $subscriptionPaymentsQuery)->sum('amount');

        $subscriptionSummaryChart = [
            'labels' => ['Active', 'Expired'],
            'data'   => [$activeSubscriptions, $expiredSubscriptions],
        ];

        return view('admin.reports.index', compact(
            'filters',
            'totalBookings',
            'bookingStatusSummary',
            'bookingStatusChart',
            'topVehiclesChart',
            'totalVendors',
            'activeVendors',
            'pendingVendors',
            'inactiveVendors',
            'grossBookingRevenue',
            'platformCommission',
            'vendorEarnings',
            'revenueComparisonChart',
            'activeSubscriptions',
            'expiredSubscriptions',
            'monthlyPlans',
            'yearlyPlans',
            'subscriptionRevenueTotal',
            'subscriptionSummaryChart'
        ));
    }

    private function applyDateRange(Builder $query, string $column, ?Carbon $fromDate, ?Carbon $toDate): void
    {
        if ($fromDate) {
            $query->where($column, '>=', $fromDate);
        }
        if ($toDate) {
            $query->where($column, '<=', $toDate);
        }
    }
}
