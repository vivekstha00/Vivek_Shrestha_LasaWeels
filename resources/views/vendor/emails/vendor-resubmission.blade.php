<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Vendor Resubmission Requested</title>
</head>
<body style="margin:0; padding:0; background:#f5f5f5; font-family: Arial, sans-serif;">
    <div style="max-width:600px; margin:30px auto; background:#ffffff; border-radius:10px; overflow:hidden; border:1px solid #e5e5e5;">
        <div style="padding:20px; background:#f59e0b; color:#111827;">
            <h2 style="margin:0;">Document Resubmission Requested</h2>
        </div>

        <div style="padding:20px; color:#111827;">
            <p style="font-size:16px;">Hello <b>{{ $vendorUser->name ?? 'there' }}</b>,</p>
            <p style="font-size:15px; line-height:1.6;">
                Your vendor verification requires document resubmission. Please upload the corrected document from your verification page.
            </p>

            @if(!empty($reason))
                <div style="background:#fff7ed; padding:12px; border-radius:8px; font-size:14px; margin:15px 0; border:1px solid #fed7aa;">
                    <b>Admin Remark:</b> {{ $reason }}
                </div>
            @endif

            <p style="margin:25px 0;">
                <a href="{{ url('/vendor/verification') }}"
                   style="background:#2563eb; color:#fff; text-decoration:none; padding:12px 18px; border-radius:8px; display:inline-block;">
                    Go to Verification Page
                </a>
            </p>
        </div>

        <div style="padding:15px; background:#f9fafb; color:#6b7280; font-size:12px; text-align:center;">
            © {{ date('Y') }} LasaWheels.
        </div>
    </div>
</body>
</html>
