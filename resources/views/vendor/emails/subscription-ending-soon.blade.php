<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Subscription Ending Soon</title>
</head>
<body style="margin:0; padding:0; background:#f3f4f6; font-family:Arial, sans-serif;">
    <div style="max-width:620px; margin:24px auto; background:#ffffff; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden;">
        <div style="padding:18px 22px; background:#92400e; color:#fff;">
            <h2 style="margin:0; font-size:20px;">Your Plan is Ending Soon ⏳</h2>
        </div>

        <div style="padding:22px; color:#111827;">
            <p style="margin:0 0 14px; font-size:15px;">Hello {{ $vendorUser->name ?? 'Vendor' }},</p>
            <p style="margin:0 0 16px; font-size:14px; line-height:1.6; color:#374151;">
                Your current subscription is close to expiry. Please renew to avoid service limits.
            </p>

            <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:10px; padding:14px; font-size:14px; line-height:1.8;">
                <b>Plan:</b> {{ $plan->name ?? 'N/A' }}<br>
                <b>End Date:</b> {{ optional($subscription->ends_at)->format('d M Y') ?? 'N/A' }}<br>
                <b>Days Left:</b> {{ $daysLeft }}
            </div>

            <p style="margin:22px 0 0;">
                <a href="{{ $subscriptionsUrl }}" style="display:inline-block; background:#2563eb; color:#fff; text-decoration:none; padding:11px 16px; border-radius:8px; font-size:14px;">
                    Renew Subscription
                </a>
            </p>
        </div>

        <div style="padding:12px 18px; background:#f9fafb; color:#6b7280; font-size:12px; text-align:center;">
            © {{ date('Y') }} LasaWheels
        </div>
    </div>
</body>
</html>
