<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Welcome to LasaWheels</title>
</head>
<body style="margin:0; padding:0; background:#f5f5f5; font-family: Arial, sans-serif;">
    <div style="max-width:600px; margin:30px auto; background:#ffffff; border-radius:10px; overflow:hidden; border:1px solid #e5e5e5;">
        <div style="padding:20px; background:#111827; color:#ffffff;">
            <h2 style="margin:0;">Welcome to LasaWheels </h2>
        </div>

        <div style="padding:20px; color:#111827;">
            <p style="font-size:16px;">Hello <b>{{ $user->name ?? 'there' }}</b>,</p>
            <p style="font-size:15px; line-height:1.6;">
                Your account has been created successfully. You can now book vehicles and manage everything from your dashboard.
            </p>

            <p style="margin:25px 0;">
                <a href="{{ url('/user/dashboard') }}"
                   style="background:#2563eb; color:#fff; text-decoration:none; padding:12px 18px; border-radius:8px; display:inline-block;">
                    Go to Dashboard
                </a>
            </p>

            <p style="font-size:14px; color:#374151;">
                Thanks for choosing <b>LasaWheels</b>!
            </p>
        </div>

        <div style="padding:15px; background:#f9fafb; color:#6b7280; font-size:12px; text-align:center;">
            © {{ date('Y') }} LasaWheels. All rights reserved.
        </div>
    </div>
</body>
</html>
