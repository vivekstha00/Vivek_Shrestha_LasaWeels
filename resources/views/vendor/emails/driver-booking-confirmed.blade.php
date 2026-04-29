<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Driver Booking Confirmed</title>
</head>
<body style="margin:0; padding:0; background:#f5f5f5; font-family: Arial, sans-serif;">
    @php
        $vehicleName = trim(($booking->vehicle?->brand ?? '') . ' ' . ($booking->vehicle?->model ?? ''));
        $vehicleName = $vehicleName ?: ($booking->vehicle?->title ?? 'N/A');
        $customerName = $booking->user?->name ?? 'Customer';
        $customerEmail = $booking->user?->email ?? 'N/A';
        $customerPhone = $booking->user?->phone ?? 'N/A';
        $pickupTime = optional($booking->pickup_datetime)->format('d M Y, h:i A') ?? '-';
        $dropTime = optional($booking->drop_datetime)->format('d M Y, h:i A') ?? '-';
    @endphp
    <div style="max-width:600px; margin:30px auto; background:#ffffff; border-radius:10px; overflow:hidden; border:1px solid #e5e5e5;">
        <div style="padding:20px; background:#111827; color:#ffffff;">
            <h2 style="margin:0;">New Confirmed Driver Booking</h2>
        </div>

        <div style="padding:20px; color:#111827;">
            <p style="font-size:16px;">Hello <b>{{ $booking->driver?->name ?? 'Driver' }}</b>,</p>

            <p style="font-size:15px; line-height:1.6;">
                A booking assigned to you has been confirmed after successful payment.
            </p>

            <div style="background:#f9fafb; padding:12px; border-radius:8px; font-size:14px; line-height:1.8;">
                <b>Booking ID:</b> #{{ $booking->id }} <br>
                <b>Customer:</b> {{ $customerName }} <br>
                <b>Customer Email:</b> {{ $customerEmail }} <br>
                <b>Customer Phone:</b> {{ $customerPhone }} <br>
                <b>Vehicle:</b> {{ $vehicleName }} <br>
                <b>Pickup:</b> {{ $pickupTime }} <br>
                <b>Return:</b> {{ $dropTime }} <br>
                <b>Pickup Location:</b> {{ $booking->pickup_location ?? 'N/A' }}
            </div>

            @if(!empty($booking->special_request))
                <div style="background:#f1f5f9; padding:12px; border-radius:8px; margin-top:14px; font-size:14px;">
                    <b>Booking Notes:</b><br>
                    {{ $booking->special_request }}
                </div>
            @endif

            <p style="margin:18px 0 0 0; font-size:14px;">
                Please prepare for the trip and contact the customer if needed.
            </p>
        </div>

        <div style="padding:15px; background:#f9fafb; color:#6b7280; font-size:12px; text-align:center;">
            © {{ date('Y') }} LasaWheels.
        </div>
    </div>
</body>
</html>
