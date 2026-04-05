<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Vehicle Rejected</title>
</head>
<body style="margin:0; padding:0; background:#f3f4f6; font-family:Arial, sans-serif;">
    <div style="max-width:620px; margin:24px auto; background:#ffffff; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden;">
        <div style="padding:18px 22px; background:#b91c1c; color:#fff;">
            <h2 style="margin:0; font-size:20px;">Vehicle Review Update ⚠️</h2>
        </div>

        <div style="padding:22px; color:#111827;">
            <p style="margin:0 0 14px; font-size:15px;">Hello {{ $vendorUser->name ?? 'Vendor' }},</p>
            <p style="margin:0 0 16px; font-size:14px; line-height:1.6; color:#374151;">
                Your vehicle submission was reviewed and requires corrections before approval.
            </p>

            <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:10px; padding:14px; font-size:14px; line-height:1.8;">
                <b>Vehicle:</b> {{ trim(($vehicle->brand ?? '') . ' ' . ($vehicle->model ?? '')) ?: 'N/A' }}<br>
                <b>Registration No:</b> {{ $vehicle->registration_no ?? 'N/A' }}<br>
                <b>Reason:</b> {{ $reason ?: 'Please update required details/documents.' }}
            </div>

            <p style="margin:22px 0 0;">
                <a href="{{ $editUrl }}" style="display:inline-block; background:#2563eb; color:#fff; text-decoration:none; padding:11px 16px; border-radius:8px; font-size:14px;">
                    Edit Vehicle
                </a>
            </p>
        </div>

        <div style="padding:12px 18px; background:#f9fafb; color:#6b7280; font-size:12px; text-align:center;">
            © {{ date('Y') }} LasaWheels
        </div>
    </div>
</body>
</html>
