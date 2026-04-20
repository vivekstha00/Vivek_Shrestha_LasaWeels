<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Booking Created</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:Arial,sans-serif;color:#111827;">
    @php
        $vehicleName = $booking->vehicle?->title
            ?? trim(($booking->vehicle?->brand ?? '') . ' ' . ($booking->vehicle?->model ?? ''));
        $vehicleName = $vehicleName ?: 'N/A';
    @endphp

    <div style="max-width:640px;margin:28px auto;background:#ffffff;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;">
        <div style="padding:16px 20px;background:#0f766e;color:#ffffff;">
            <h2 style="margin:0;font-size:20px;">Booking Created</h2>
        </div>

        <div style="padding:20px;">
            <p style="margin:0 0 12px 0;font-size:15px;">Hello <strong>{{ $user->name ?? 'there' }}</strong>,</p>
            <p style="margin:0 0 16px 0;font-size:14px;line-height:1.6;">Your booking request has been created successfully. You can review all details below.</p>

            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:14px;">
                <tr><td style="padding:8px;border:1px solid #e5e7eb;background:#f9fafb;"><strong>Booking ID</strong></td><td style="padding:8px;border:1px solid #e5e7eb;">#{{ $booking->id }}</td></tr>
                <tr><td style="padding:8px;border:1px solid #e5e7eb;background:#f9fafb;"><strong>Status</strong></td><td style="padding:8px;border:1px solid #e5e7eb;">{{ ucfirst(str_replace('_', ' ', $booking->status ?? 'pending')) }}</td></tr>
                <tr><td style="padding:8px;border:1px solid #e5e7eb;background:#f9fafb;"><strong>Payment Status</strong></td><td style="padding:8px;border:1px solid #e5e7eb;">{{ ucfirst($booking->payment_status ?? 'unpaid') }}</td></tr>
                <tr><td style="padding:8px;border:1px solid #e5e7eb;background:#f9fafb;"><strong>Vehicle</strong></td><td style="padding:8px;border:1px solid #e5e7eb;">{{ $vehicleName }}</td></tr>
                <tr><td style="padding:8px;border:1px solid #e5e7eb;background:#f9fafb;"><strong>Service</strong></td><td style="padding:8px;border:1px solid #e5e7eb;">{{ ucfirst($booking->service ?? 'self') }}</td></tr>
                <tr><td style="padding:8px;border:1px solid #e5e7eb;background:#f9fafb;"><strong>Pickup</strong></td><td style="padding:8px;border:1px solid #e5e7eb;">{{ optional($booking->pickup_datetime)->format('d M Y, h:i A') ?? '-' }}</td></tr>
                <tr><td style="padding:8px;border:1px solid #e5e7eb;background:#f9fafb;"><strong>Drop</strong></td><td style="padding:8px;border:1px solid #e5e7eb;">{{ optional($booking->drop_datetime)->format('d M Y, h:i A') ?? '-' }}</td></tr>
                <tr><td style="padding:8px;border:1px solid #e5e7eb;background:#f9fafb;"><strong>Route</strong></td><td style="padding:8px;border:1px solid #e5e7eb;">{{ $booking->pickup_location ?? '-' }} → {{ $booking->drop_location ?? '-' }}</td></tr>
                <tr><td style="padding:8px;border:1px solid #e5e7eb;background:#f9fafb;"><strong>Total Amount</strong></td><td style="padding:8px;border:1px solid #e5e7eb;">NPR {{ number_format((float) ($booking->total_price ?? 0), 2) }}</td></tr>
                @if(!is_null($booking->security_deposit))
                    <tr><td style="padding:8px;border:1px solid #e5e7eb;background:#f9fafb;"><strong>Security Deposit</strong></td><td style="padding:8px;border:1px solid #e5e7eb;">NPR {{ number_format((float) $booking->security_deposit, 2) }}</td></tr>
                @endif
            </table>

            <div style="margin-top:18px;">
                <a href="{{ route('user.booking.show', $booking->id) }}" style="display:inline-block;padding:10px 14px;background:#2563eb;color:#ffffff;text-decoration:none;border-radius:6px;font-size:14px;">View Booking</a>
            </div>

            <p style="margin:14px 0 0 0;font-size:13px;color:#4b5563;">If payment is pending, please complete payment to confirm your booking.</p>
        </div>

        <div style="padding:12px 20px;background:#f9fafb;color:#6b7280;font-size:12px;text-align:center;">© {{ date('Y') }} LasaWheels. All rights reserved.</div>
    </div>
</body>
</html>
