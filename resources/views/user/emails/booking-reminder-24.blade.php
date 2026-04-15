<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Trip Reminder</title>
</head>
<body style="margin:0; padding:0; background:#f5f5f5; font-family: Arial, sans-serif;">
    <div style="max-width:600px; margin:30px auto; background:#ffffff; border-radius:10px; overflow:hidden; border:1px solid #e5e5e5;">
        <div style="padding:20px; background:#f59e0b; color:#111827;">
            <h2 style="margin:0;">Reminder: Trip starts in {{ $reminderLabel ?? '24 hours' }}</h2>
        </div>

        <div style="padding:20px; color:#111827;">
            <p style="font-size:16px;">Hello <b>{{ $user->name ?? 'there' }}</b>,</p>

            <p style="font-size:15px; line-height:1.6;">
                This is a friendly reminder that your booking is starting in {{ $reminderLabel ?? '24 hours' }}.
            </p>

            <div style="background:#f9fafb; padding:12px; border-radius:8px; font-size:14px; line-height:1.8;">
                <b>Booking ID:</b> {{ $booking->id }} <br>
                <b>Pickup:</b> {{ optional($booking->pickup_datetime)->format('d M Y, h:i A') ?? '-' }} <br>
                <b>Drop:</b> {{ optional($booking->drop_datetime)->format('d M Y, h:i A') ?? '-' }}
            </div>

            <p style="margin:25px 0;">
                <a href="{{ url('/user/bookings/' . $booking->id) }}"
                   style="background:#2563eb; color:#fff; text-decoration:none; padding:12px 18px; border-radius:8px; display:inline-block;">
                    View Booking
                </a>
            </p>

            <p style="font-size:14px; color:#374151;">
                Have a safe and enjoyable ride!
            </p>
        </div>

        <div style="padding:15px; background:#f9fafb; color:#6b7280; font-size:12px; text-align:center;">
            © {{ date('Y') }} LasaWheels. All rights reserved.
        </div>
    </div>
</body>
</html>
