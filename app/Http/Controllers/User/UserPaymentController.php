<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;

class UserPaymentController extends Controller
{
    // 1) Show payment page
    public function show(Booking $booking)
    {
        abort_unless($booking->user_id === Auth::id(), 403);

        // Optional: prevent re-paying
        if ($booking->payment_status === 'paid') {
            return redirect()->route('user.booking.success', $booking->id)
                ->with('success', 'Booking already paid.');
        }

        return view('user.pages.booking.booking-payment', compact('booking'));
    }

    // 2) Handle cash / khalti selection
    public function process(Request $request, Booking $booking)
    {
        abort_unless($booking->user_id === Auth::id(), 403);

        $data = $request->validate([
            'payment_method' => 'required|in:cash,khalti',
        ]);

        // (Optional) commission calc - you can adjust later
        $commissionRate = 0.10;
        $platformCommission = round($booking->total_price * $commissionRate, 2);
        $vendorAmount = round($booking->total_price - $platformCommission, 2);

        //  CASH ON PICKUP
        if ($data['payment_method'] === 'cash') {

            Payment::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'user_id' => Auth::id(),
                    'booking_id' => $booking->id,
                    'amount' => $booking->total_price,
                    'method' => 'cash',
                    'status' => 'pending',
                    'platform_commission' => $platformCommission,
                    'vendor_amount' => $vendorAmount,
                    'payout_status' => 'unpaid',
                ]
            );

            $booking->update([
                'status' => 'confirmed',
                'payment_status' => 'unpaid', // still unpaid until pickup
            ]);

            return redirect()->route('user.booking.success', $booking->id)
                ->with('success', 'Booking confirmed. Pay cash on pickup.');
        }

        // ✅ KHALTI INITIATE
        $payment = Payment::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'user_id' => Auth::id(),
                'booking_id' => $booking->id,
                'amount' => $booking->total_price,
                'method' => 'khalti',
                'status' => 'pending',
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
            'amount' => (int) round($booking->total_price * 100),
            'purchase_order_id' => (string) $booking->id,
            'purchase_order_name' => "Vehicle Booking #{$booking->id}",
        ]);

        if ($response->successful() && $response->json('payment_url')) {
            Cookie::queue('khalti_booking_id', $booking->id, 10);
            return redirect($response->json('payment_url'));
        }

        return back()->withErrors(['payment' => 'Khalti initiation failed.']);
    }

    // 3) Khalti callback
    public function khaltiCallback(Request $request)
    {
        $pidx = $request->query('pidx');
        if (!$pidx) {
            return redirect()->route('home')->withErrors(['payment' => 'Invalid Khalti callback.']);
        }

        $bookingId = Cookie::get('khalti_booking_id');
        $booking = Booking::find($bookingId);

        if (!$booking) {
            return redirect()->route('home')->withErrors(['payment' => 'Booking not found.']);
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

        if ($payment) {
            $payment->status = $status === 'Completed' ? 'completed' : 'failed';
            $payment->gateway_reference = $pidx; // if column exists
            $payment->gateway_payload = $lookup->json(); // if json exists
            $payment->save();
        }

        Cookie::queue(Cookie::forget('khalti_booking_id'));

        if ($status === 'Completed') {
            $booking->update([
                'status' => 'confirmed',
                'payment_status' => 'paid',
            ]);

            return redirect()->route('user.booking.success', $booking->id)
                ->with('success', 'Khalti payment successful. Booking confirmed!');
        }

        return redirect()->route('user.booking.payment', $booking->id)
            ->withErrors(['payment' => 'Khalti payment failed. Try again.']);
    }
}
