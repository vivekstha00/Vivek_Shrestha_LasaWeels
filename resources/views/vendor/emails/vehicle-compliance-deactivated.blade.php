<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Vehicle Deactivated</title>
</head>
<body style="margin:0; padding:0; background:#f3f4f6; font-family:Arial, sans-serif;">
    <div style="max-width:620px; margin:24px auto; background:#ffffff; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden;">
        <div style="padding:18px 22px; background:#b91c1c; color:#fff;">
            <h2 style="margin:0; font-size:20px;">Vehicle Temporarily Deactivated</h2>
        </div>

        <div style="padding:22px; color:#111827;">
            <p style="margin:0 0 14px; font-size:15px;">Hello {{ $vendorUser->name ?? 'Vendor' }},</p>
            <p style="margin:0 0 16px; font-size:14px; line-height:1.6; color:#374151;">
                Your vehicle was temporarily deactivated because required compliance document(s) expired.
            </p>

            <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:10px; padding:14px; font-size:14px; line-height:1.8;">
                <b>Vehicle:</b> {{ trim(($vehicle->brand ?? '') . ' ' . ($vehicle->model ?? '')) ?: 'N/A' }}<br>
                <b>Expired Items:</b> {{ implode(', ', $expiredItems) ?: 'N/A' }}
            </div>

            <p style="margin:18px 0 0; font-size:14px; color:#374151;">
                Upload updated documents. After re-submission, the vehicle will go through admin review again.
            </p>

            <p style="margin:14px 0 0;">
                <a href="{{ $updateUrl }}" style="display:inline-block; background:#2563eb; color:#fff; text-decoration:none; padding:11px 16px; border-radius:8px; font-size:14px;">
                    Update Vehicle Documents
                </a>
            </p>
        </div>

        <div style="padding:12px 18px; background:#f9fafb; color:#6b7280; font-size:12px; text-align:center;">
            © {{ date('Y') }} LasaWheels
        </div>
    </div>
</body>
</html>
