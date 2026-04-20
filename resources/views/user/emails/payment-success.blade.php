<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payment Successful</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:Arial,sans-serif;color:#111827;">
    @php
        $vehicleName = $booking?->vehicle?->title
            ?? trim(($booking?->vehicle?->brand ?? '') . ' ' . ($booking?->vehicle?->model ?? ''));
        $vehicleName = $vehicleName ?: 'N/A';
    @endphp

    <div style="max-width:640px;margin:28px auto;background:#ffffff;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;">
        <div style="padding:16px 20px;background:#4f46e5;color:#ffffff;">
            <h2 style="margin:0;font-size:20px;">Payment Successful</h2>
        </div>

        <div style="padding:20px;">
            <p style="margin:0 0 12px 0;font-size:15px;">Hello <strong>{{ $user->name ?? 'there' }}</strong>,</p>
            <p style="margin:0 0 16px 0;font-size:14px;line-height:1.6;">We have received your payment successfully. Here is your payment summary.</p>

            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:14px;">
                <tr><td style="padding:8px;border:1px solid #e5e7eb;background:#f9fafb;"><strong>Payment ID</strong></td><td style="padding:8px;border:1px solid #e5e7eb;">#{{ $payment->id }}</td></tr>
                <tr><td style="padding:8px;border:1px solid #e5e7eb;background:#f9fafb;"><strong>Booking ID</strong></td><td style="padding:8px;border:1px solid #e5e7eb;">#{{ $booking->id ?? ($payment->booking_id ?? '-') }}</td></tr>
                <tr><td style="padding:8px;border:1px solid #e5e7eb;background:#f9fafb;"><strong>Amount</strong></td><td style="padding:8px;border:1px solid #e5e7eb;">NPR {{ number_format((float) ($payment->amount ?? 0), 2) }}</td></tr>
                <tr><td style="padding:8px;border:1px solid #e5e7eb;background:#f9fafb;"><strong>Paid Amount</strong></td><td style="padding:8px;border:1px solid #e5e7eb;">NPR {{ number_format((float) ($payment->paid_amount ?? 0), 2) }}</td></tr>
                <tr><td style="padding:8px;border:1px solid #e5e7eb;background:#f9fafb;"><strong>Payment Method</strong></td><td style="padding:8px;border:1px solid #e5e7eb;">{{ strtoupper($payment->method ?? 'N/A') }}</td></tr>
                <tr><td style="padding:8px;border:1px solid #e5e7eb;background:#f9fafb;"><strong>Payment Type</strong></td><td style="padding:8px;border:1px solid #e5e7eb;">{{ ucfirst(str_replace('_', ' ', $payment->payment_type ?? 'full_online')) }}</td></tr>
                <tr><td style="padding:8px;border:1px solid #e5e7eb;background:#f9fafb;"><strong>Payment Status</strong></td><td style="padding:8px;border:1px solid #e5e7eb;">{{ ucfirst($payment->status ?? 'pending') }}</td></tr>
                <tr><td style="padding:8px;border:1px solid #e5e7eb;background:#f9fafb;"><strong>Gateway Reference</strong></td><td style="padding:8px;border:1px solid #e5e7eb;">{{ $payment->gateway_reference ?? 'N/A' }}</td></tr>
                <tr><td style="padding:8px;border:1px solid #e5e7eb;background:#f9fafb;"><strong>Paid At</strong></td><td style="padding:8px;border:1px solid #e5e7eb;">{{ optional($payment->paid_at)->format('d M Y, h:i A') ?? optional($payment->updated_at)->format('d M Y, h:i A') }}</td></tr>
                @if($booking)
                    <tr><td style="padding:8px;border:1px solid #e5e7eb;background:#f9fafb;"><strong>Vehicle</strong></td><td style="padding:8px;border:1px solid #e5e7eb;">{{ $vehicleName }}</td></tr>
                    <tr><td style="padding:8px;border:1px solid #e5e7eb;background:#f9fafb;"><strong>Service</strong></td><td style="padding:8px;border:1px solid #e5e7eb;">{{ ucfirst($booking->service ?? 'self') }}</td></tr>
                    <tr><td style="padding:8px;border:1px solid #e5e7eb;background:#f9fafb;"><strong>Trip Time</strong></td><td style="padding:8px;border:1px solid #e5e7eb;">{{ optional($booking->pickup_datetime)->format('d M Y, h:i A') ?? '-' }} → {{ optional($booking->drop_datetime)->format('d M Y, h:i A') ?? '-' }}</td></tr>
                @endif
            </table>

            <div style="margin-top:18px;">
                <a href="{{ url('/user/bookings/' . ($booking->id ?? $payment->booking_id)) }}" style="display:inline-block;padding:10px 14px;background:#2563eb;color:#ffffff;text-decoration:none;border-radius:6px;font-size:14px;">View Booking</a>
            </div>

            <p style="margin:14px 0 0 0;font-size:13px;color:#4b5563;">Thank you for choosing <strong>LasaWheels</strong>.</p>
        </div>

        <div style="padding:12px 20px;background:#f9fafb;color:#6b7280;font-size:12px;text-align:center;">© {{ date('Y') }} LasaWheels. All rights reserved.</div>
    </div>
</body>
</html>
