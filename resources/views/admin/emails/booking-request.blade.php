<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Booking Created</title>
</head>
<body style="margin:0; padding:0; background:#f5f5f5; font-family: Arial, sans-serif;">
    <div style="max-width:600px; margin:30px auto; background:#ffffff; border-radius:10px; overflow:hidden; border:1px solid #e5e5e5;">
        <div style="padding:20px; background:#111827; color:#ffffff;">
            <h2 style="margin:0;">New Booking Created </h2>
        </div>

        <div style="padding:20px; color:#111827;">
            <p style="font-size:15px; line-height:1.6;">
                A new booking has been created on LasaWheels.
            </p>

            <div style="background:#f9fafb; padding:12px; border-radius:8px; font-size:14px; line-height:1.8;">
                <b>Booking ID:</b> {{ $booking->id }} <br>
                <b>User ID:</b> {{ $booking->user_id ?? '-' }} <br>
                <b>Vehicle ID:</b> {{ $booking->vehicle_id ?? '-' }} <br>
                <b>Status:</b> {{ $booking->status ?? 'pending' }} <br>
                <b>Payment Status:</b> {{ $booking->payment_status ?? 'pending' }}
            </div>

            <p style="margin:25px 0;">
                <a href="{{ url('/admin/bookings') }}"
                   style="background:#2563eb; color:#fff; text-decoration:none; padding:12px 18px; border-radius:8px; display:inline-block;">
                    View Admin Bookings
                </a>
            </p>
        </div>

        <div style="padding:15px; background:#f9fafb; color:#6b7280; font-size:12px; text-align:center;">
            © {{ date('Y') }} LasaWheels.
        </div>
    </div>
</body>
</html>
