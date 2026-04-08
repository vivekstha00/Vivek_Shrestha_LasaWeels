<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Review;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorReportController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date'   => ['nullable', 'date', 'after_or_equal:from_date'],
        ]);

        $vendorId = Auth::id();
        $fromDate = !empty($filters['from_date']) ? Carbon::parse($filters['from_date'])->startOfDay() : null;
        $toDate   = !empty($filters['to_date'])   ? Carbon::parse($filters['to_date'])->endOfDay()     : null;

        // ── Bookings ──────────────────────────────────────────────────────────
        $bookingBaseQuery = Booking::query()->whereHas('vehicle', function (Builder $q) use ($vendorId) {
            $q->where('vendor_id', $vendorId);
        });

        $this->applyDateRange($bookingBaseQuery, 'pickup_datetime', $fromDate, $toDate);

        $bookingStatusMap = (clone $bookingBaseQuery)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $bookingStatusSummary = [
            'pending'   => (int) ($bookingStatusMap['pending']   ?? 0),
            'confirmed' => (int) ($bookingStatusMap['confirmed'] ?? 0),
            'active'    => (int) ($bookingStatusMap['active']    ?? 0),
            'completed' => (int) ($bookingStatusMap['completed'] ?? 0),
            'cancelled' => (int) (($bookingStatusMap['cancelled'] ?? 0) + ($bookingStatusMap['cancel_requested'] ?? 0)),
        ];

        $totalBookings = array_sum($bookingStatusSummary);

        $bookingStatusChart = [
            'labels' => ['Pending', 'Confirmed', 'Active', 'Completed', 'Cancelled'],
            'data'   => array_values($bookingStatusSummary),
        ];

        // ── Payments ──────────────────────────────────────────────────────────
        $paymentBaseQuery = Payment::query()
            ->forVendor($vendorId)
            ->where(function (Builder $q) {
                $q->whereNull('refund_status')
                    ->orWhere('refund_status', '!=', 'refunded');
            });

        $this->applyDateRange($paymentBaseQuery, 'created_at', $fromDate, $toDate);

        $totalRevenue = (clone $paymentBaseQuery)
            ->where('status', 'completed')
            ->sum('vendor_amount');

        // ── Earnings by Vehicle ───────────────────────────────────────────────
        $earningsByVehicle = Booking::query()
            ->join('payments', 'payments.booking_id', '=', 'bookings.id')
            ->whereHas('vehicle', function (Builder $q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })
            ->where('payments.status', 'completed')
            ->where(function (Builder $q) {
                $q->whereNull('payments.refund_status')
                    ->orWhere('payments.refund_status', '!=', 'refunded');
            })
            ->select('bookings.vehicle_id')
            ->selectRaw('COUNT(*) as total_bookings')
            ->selectRaw('SUM(payments.vendor_amount) as total_earnings')
            ->with('vehicle')
            ->groupBy('bookings.vehicle_id')
            ->orderByDesc('total_earnings')
            ->limit(6)
            ->get();

        $maxVehicleEarning = $earningsByVehicle->max('total_earnings') ?: 1;

        // ── Payout ────────────────────────────────────────────────────────────
        $paidAmount = (clone $paymentBaseQuery)
            ->where('payout_status', 'paid')
            ->sum('vendor_amount');

        $payoutReadyAmount = Payment::query()
            ->forVendor($vendorId)
            ->eligibleForPayout()
            ->whereIn('payout_status', ['unpaid', 'pending', 'ready_for_payout'])
            ->sum('vendor_amount');

        $pendingPayoutAmount = (clone $paymentBaseQuery)
            ->whereIn('payout_status', ['unpaid', 'pending'])
            ->sum('vendor_amount');

        // ── Vehicles ──────────────────────────────────────────────────────────
        $vehiclesQuery = Vehicle::query()->where('vendor_id', $vendorId);

        $totalVehicles  = (clone $vehiclesQuery)->count();
        $listedVehicles = (clone $vehiclesQuery)->where('status', 'approved')->where('is_active', true)->count();

        $bookedVehicles = Booking::query()
            ->whereIn('status', ['pending', 'confirmed', 'active'])
            ->whereHas('vehicle', function (Builder $q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })
            ->distinct('vehicle_id')
            ->count('vehicle_id');

        $availableVehicles   = max(0, $listedVehicles - $bookedVehicles);
        $unavailableVehicles = max(0, $totalVehicles - $availableVehicles - $bookedVehicles);

        $vehicleAvailabilityChart = [
            'labels' => ['Available', 'Booked', 'Unavailable'],
            'data'   => [$availableVehicles, $bookedVehicles, $unavailableVehicles],
        ];

        // ── Payment Summary ───────────────────────────────────────────────────
        $paidPaymentsCount = Payment::query()->forVendor($vendorId)->where('status', 'completed')->count();
        $pendingPaymentsCount = Payment::query()->forVendor($vendorId)->where('status', 'pending')->count();
        $refundedPaymentsCount = Payment::query()->forVendor($vendorId)
            ->where(function (Builder $q) {
                $q->where('status', 'refunded')
                    ->orWhere('refund_status', 'refunded');
            })
            ->count();

        $totalReceivedAmount = Payment::query()
            ->forVendor($vendorId)
            ->where('status', 'completed')
            ->where(function (Builder $q) {
                $q->whereNull('refund_status')
                    ->orWhere('refund_status', '!=', 'refunded');
            })
            ->sum('amount');

        $paymentSummary = [
            'paid'     => $paidPaymentsCount,
            'pending'  => $pendingPaymentsCount,
            'refunded' => $refundedPaymentsCount,
            'received' => (float) $totalReceivedAmount,
        ];

        // ── Reviews ───────────────────────────────────────────────────────────
        $reviewQuery   = Review::query()->where('vendor_id', $vendorId);
        $averageRating = (clone $reviewQuery)->avg('overall_rating') ?? 0;
        $totalReviews  = (clone $reviewQuery)->count();

        return view('vendor.pages.reports.index', compact(
            'filters',
            'totalBookings',
            'bookingStatusSummary',
            'bookingStatusChart',
            'totalRevenue',
            'earningsByVehicle',
            'maxVehicleEarning',
            'paidAmount',
            'payoutReadyAmount',
            'pendingPayoutAmount',
            'totalVehicles',
            'availableVehicles',
            'bookedVehicles',
            'unavailableVehicles',
            'vehicleAvailabilityChart',
            'paymentSummary',
            'averageRating',
            'totalReviews'
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
