<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Subscription Activated</title>
</head>
<body style="margin:0; padding:0; background:#f3f4f6; font-family:Arial, sans-serif;">
    <div style="max-width:620px; margin:24px auto; background:#ffffff; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden;">
        <div style="padding:18px 22px; background:#065f46; color:#fff;">
            <h2 style="margin:0; font-size:20px;">{{ $isRenewal ? 'Subscription Renewed ✅' : 'Subscription Activated ✅' }}</h2>
        </div>

        <div style="padding:22px; color:#111827;">
            <p style="margin:0 0 14px; font-size:15px;">Hello {{ $vendorUser->name ?? 'Vendor' }},</p>
            <p style="margin:0 0 16px; font-size:14px; line-height:1.6; color:#374151;">
                {{ $isRenewal ? 'Your subscription has been renewed successfully.' : 'Your subscription plan is now active.' }}
            </p>

            <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:10px; padding:14px; font-size:14px; line-height:1.8;">
                <b>Plan:</b> {{ $plan->name ?? 'N/A' }}<br>
                <b>Billing Cycle:</b> {{ ucfirst($plan->billing_cycle ?? 'N/A') }}<br>
                <b>Start Date:</b> {{ optional($subscription->starts_at)->format('d M Y') ?? 'N/A' }}<br>
                <b>End Date:</b> {{ optional($subscription->ends_at)->format('d M Y') ?? 'N/A' }}<br>
                <b>Amount Paid:</b> NPR {{ number_format((float) ($subscription->amount_paid ?? 0), 2) }}
            </div>

            <p style="margin:22px 0 0;">
                <a href="{{ $subscriptionsUrl }}" style="display:inline-block; background:#2563eb; color:#fff; text-decoration:none; padding:11px 16px; border-radius:8px; font-size:14px;">
                    View Subscription
                </a>
            </p>
        </div>

        <div style="padding:12px 18px; background:#f9fafb; color:#6b7280; font-size:12px; text-align:center;">
            © {{ date('Y') }} LasaWheels
        </div>
    </div>
</body>
</html>
