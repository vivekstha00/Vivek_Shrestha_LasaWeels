<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Vendor Request</title>
</head>
<body style="margin:0; padding:0; background:#f5f5f5; font-family: Arial, sans-serif;">
    <div style="max-width:600px; margin:30px auto; background:#ffffff; border-radius:10px; overflow:hidden; border:1px solid #e5e5e5;">
        <div style="padding:20px; background:#111827; color:#ffffff;">
            <h2 style="margin:0;">New Vendor Request Submitted 🧾</h2>
        </div>

        <div style="padding:20px; color:#111827;">
            <p style="font-size:15px; line-height:1.6;">
                A new vendor application has been submitted.
            </p>

            <div style="background:#f9fafb; padding:12px; border-radius:8px; font-size:14px; line-height:1.8;">
                <b>Name:</b> {{ $vendorUser->name ?? '-' }} <br>
                <b>Email:</b> {{ $vendorUser->email ?? '-' }} <br>
                <b>Phone:</b> {{ $vendorUser->phone ?? '-' }}
            </div>

            <p style="margin:25px 0;">
                <a href="{{ url('/admin/vendors') }}"
                   style="background:#2563eb; color:#fff; text-decoration:none; padding:12px 18px; border-radius:8px; display:inline-block;">
                    Review Vendor Request
                </a>
            </p>
        </div>

        <div style="padding:15px; background:#f9fafb; color:#6b7280; font-size:12px; text-align:center;">
            © {{ date('Y') }} LasaWheels.
        </div>
    </div>
</body>
</html>
