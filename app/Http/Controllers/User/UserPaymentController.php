<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Notifications\PaymentSuccessNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
use App\Services\LoyaltyService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class UserPaymentController extends Controller
{
    // 1) Show payment page
    public function show(Booking $booking)
    {
        abort_unless($booking->user_id === Auth::id(), 403);

        if ($booking->payment_status === 'paid') {
            return redirect()->route('user.booking.success', $booking->id)
                ->with('success', 'Booking already fully paid.');
        }

        return view('user.pages.booking.booking-payment', compact('booking'));
    }

    // 2) Handle full online / deposit + cash selection
    public function process(Request $request, Booking $booking)
    {
        abort_unless($booking->user_id === Auth::id(), 403);

        $booking->load('vehicle');

        if (! $booking->vehicle) {
            return back()->withErrors([
                'payment' => 'Vehicle information not found for this booking.',
            ]);
        }

        if ($booking->payment_status === 'paid') {
            return redirect()
                ->route('user.booking.show', $booking->id)
                ->with('success', 'This booking has already been paid.');
        }

        $data = $request->validate([
            'payment_option' => 'required|in:full_online,deposit_cash',
        ]);

        $commissionRate = 0.10;
        $depositRate = 0.20;

        // Customer actually pays this amount
        $finalPayableAmount = (float) $booking->total_price;

        // Vendor pricing base = after vendor long-duration discount,
        // but before loyalty/code discount
        $commissionBaseAmount = (float) ($booking->original_price ?? $booking->total_price);

        $platformCommission = round($commissionBaseAmount * $commissionRate, 2);
        $vendorAmount = round($commissionBaseAmount - $platformCommission, 2);

        if ($data['payment_option'] === 'deposit_cash') {
            $depositAmount = round($finalPayableAmount * $depositRate, 2);
            $remainingAmount = round($finalPayableAmount - $depositAmount, 2);
        } else {
            $depositAmount = $finalPayableAmount;
            $remainingAmount = 0;
        }

        $vendorId = $booking->vehicle->vendor_id ?? null;

        $payment = Payment::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'user_id' => Auth::id(),
                'vendor_id' => $vendorId,
                'booking_id' => $booking->id,
                'amount' => $finalPayableAmount,
                'method' => 'khalti',
                'payment_type' => $data['payment_option'],
                'paid_amount' => 0,
                'remaining_amount' => $remainingAmount,
                'deposit_amount' => $depositAmount,
                'status' => 'pending',
                'deposit_status' => 'pending',
                'settlement_status' => $data['payment_option'] === 'deposit_cash'
                    ? 'pending_balance'
                    : 'payout_pending',
                'platform_commission' => $platformCommission,
                'vendor_amount' => $vendorAmount,
                'payout_status' => 'unpaid',
            ]
        );

        $response = Http::withHeaders([
            'Authorization' => 'key ' . config('services.khalti.secret'),
            'Content-Type' => 'application/json',
        ])->post('https://dev.khalti.com/api/v2/epayment/initiate/', [
            'return_url' => route('user.khalti.callback'),
            'website_url' => route('home'),
            'amount' => (int) round($depositAmount * 100),
            'purchase_order_id' => (string) $booking->id,
            'purchase_order_name' => $data['payment_option'] === 'deposit_cash'
                ? "Booking Deposit #{$booking->id}"
                : "Vehicle Booking #{$booking->id}",
        ]);

        if ($response->successful() && $response->json('payment_url')) {
            Cookie::queue('khalti_booking_id', $booking->id, 10);

            return redirect($response->json('payment_url'));
        }

        return back()->withErrors([
            'payment' => 'Khalti initiation failed. Please try again.',
        ]);
    }

    // 3) Khalti callback
    public function khaltiCallback(Request $request)
    {
        $pidx = $request->query('pidx');

        if (!$pidx) {
            return redirect()->route('home')
                ->withErrors(['payment' => 'Invalid Khalti callback.']);
        }

        $bookingId = Cookie::get('khalti_booking_id');
        $booking = Booking::with(['user', 'vehicle'])->find($bookingId);

        if (!$booking) {
            return redirect()->route('home')
                ->withErrors(['payment' => 'Booking not found.']);
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

        $payment = Payment::where('booking_id', $booking->id)->first();

        $alreadyCompleted = $payment && $payment->status === 'completed';

        if ($payment) {
            $payment->gateway_reference = $pidx;
            $payment->gateway_payload = $lookup->json();

            if ($status === 'Completed' && $alreadyCompleted) {
                $payment->save();

                Cookie::queue(Cookie::forget('khalti_booking_id'));

                return redirect()->route('user.booking.success', $booking->id)
                    ->with('success', 'Payment was already confirmed for this booking.');
            }

            if ($status === 'Completed' && ! $alreadyCompleted) {
                $payment->status = 'completed';
                $payment->paid_amount = $payment->deposit_amount;
                $payment->remaining_amount = $payment->amount - $payment->deposit_amount;
                $payment->deposit_status = 'paid';
                $payment->paid_at = now();

                if ($payment->payment_type === 'full_online') {
                    $payment->settlement_status = 'payout_pending';
                } else {
                    $payment->settlement_status = 'pending_balance';
                }

                $payment->save();

                $booking->update([
                    'status' => 'confirmed',
                    'payment_status' => $payment->payment_type === 'full_online' ? 'paid' : 'partial',
                ]);

                if ($booking->loyalty_points_redeemed > 0) {
                    app(LoyaltyService::class)
                        ->redeemPointsForBooking($booking->fresh(), $booking->loyalty_points_redeemed);
                }

                if ($booking->discount_type === 'code' && $booking->discount_code) {
                    $discountCode = \App\Models\DiscountCode::where('code', $booking->discount_code)->first();

                    if ($discountCode && $discountCode->isUsable()) {
                        $discountCode->increment('used_count');
                    }
                }

                $booking->user->notify(new PaymentSuccessNotification($payment));

                Cookie::queue(Cookie::forget('khalti_booking_id'));

                return redirect()->route('user.booking.success', $booking->id)
                    ->with('success', $payment->payment_type === 'deposit_cash'
                        ? 'Deposit payment successful. Booking confirmed!'
                        : 'Full payment successful. Booking confirmed!');
            }

            $payment->status = 'failed';
            $payment->save();
        }

        Cookie::queue(Cookie::forget('khalti_booking_id'));

        return redirect()->route('booking.payment', $booking->id)
            ->withErrors(['payment' => 'Khalti payment failed. Please try again.']);
    }

    public function downloadInvoice(Booking $booking)
    {
        abort_unless($booking->user_id === Auth::id(), 403);

        $booking->load([
            'user',
            'vehicle',
            'driver',
            'payment',
        ]);

        if (! $booking->payment) {
            return back()->withErrors([
                'invoice' => 'Invoice is not available because payment record was not found.',
            ]);
        }

        $isInvoiceAllowed = in_array($booking->payment->status, ['completed', 'refunded'], true)
            || in_array($booking->payment_status, ['paid', 'partial'], true);

        if (! $isInvoiceAllowed) {
            return back()->withErrors([
                'invoice' => 'Invoice is available only after payment is completed.',
            ]);
        }

        $payment = $booking->payment;

        $days = $this->calculateBillableDays(
            Carbon::parse($booking->pickup_datetime),
            Carbon::parse($booking->drop_datetime)
        );

        $pricePerDay = $booking->service === 'driver'
            ? (float) ($booking->vehicle->with_driver_price_per_day ?? $booking->vehicle->price_per_day ?? 0)
            : (float) ($booking->vehicle->price_per_day ?? 0);

        $baseAmount = round($days * $pricePerDay, 2);

        $durationDiscountPercent = $booking->vehicle
            ? (float) $booking->vehicle->getDurationDiscountPercent($days)
            : 0;

        $durationDiscountAmount = round($baseAmount * ($durationDiscountPercent / 100), 2);

        $invoiceNumber = 'INV-BOOK-' . $booking->id . '-' . $payment->id;

        $pdf = Pdf::loadView('user.invoices.booking-invoice', [
            'booking' => $booking,
            'payment' => $payment,
            'invoiceNumber' => $invoiceNumber,
            'days' => $days,
            'pricePerDay' => $pricePerDay,
            'baseAmount' => $baseAmount,
            'durationDiscountPercent' => $durationDiscountPercent,
            'durationDiscountAmount' => $durationDiscountAmount,
        ])->setPaper('a4');

        return $pdf->download($invoiceNumber . '.pdf');
    }

    private function calculateBillableDays(Carbon $pickup, Carbon $drop): int
    {
        $totalMinutes = max(0, $pickup->diffInMinutes($drop));
        $minutesPerDay = 24 * 60;

        $fullDays = intdiv($totalMinutes, $minutesPerDay);
        $remainingMinutes = $totalMinutes % $minutesPerDay;
        $graceMinutes = (int) config('vehicle.billing_grace_hours', 2) * 60;

        if ($remainingMinutes === 0) {
            return max(1, $fullDays);
        }

        if ($remainingMinutes <= $graceMinutes) {
            return max(1, $fullDays);
        }

        return max(1, $fullDays + 1);
    }
};
