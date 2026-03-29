<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Invoice</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px; /* Reduced from 13px */
            color: #222;
            line-height: 1.4; /* Reduced from 1.5 */
        }

        .container {
            width: 100%;
            page-break-inside: avoid;
        }

        .header {
            width: 100%;
            margin-bottom: 20px; /* Reduced from 25px */
            page-break-inside: avoid;
        }

        .header-table,
        .info-table,
        .summary-table,
        .details-table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        .header-table td {
            vertical-align: top;
        }

        .brand {
            font-size: 24px;
            font-weight: bold;
            color: #198754;
        }

        .subtext {
            color: #666;
            font-size: 12px;
        }

        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            text-align: right;
        }

        .section-title {
            font-size: 15px;
            font-weight: bold;
            margin: 15px 0 8px; /* Reduced margins */
            color: #198754;
            page-break-after: avoid;
        }

        .info-box {
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 10px; /* Reduced from 12px */
            margin-bottom: 12px; /* Reduced from 14px */
            page-break-inside: avoid;
        }

        .details-table th,
        .details-table td,
        .summary-table th,
        .summary-table td {
            border: 1px solid #ddd;
            padding: 6px 8px; /* Reduced from 8px 10px */
            page-break-inside: avoid;
        }

        .details-table th,
        .summary-table th {
            background: #f5f5f5;
            text-align: left;
        }

        .summary-table td.amount,
        .summary-table th.amount {
            text-align: right;
        }

        .text-right {
            text-align: right;
        }

        .text-muted {
            color: #666;
        }

        .footer {
            margin-top: 20px; /* Reduced from 30px */
            font-size: 12px;
            color: #666;
            text-align: center;
            page-break-inside: avoid;
        }

        .highlight {
            color: #198754;
            font-weight: bold;
        }

        .danger {
            color: #dc3545;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 11px;
            border-radius: 10px;
            background: #e9f7ef;
            color: #198754;
        }

        /* Prevent page breaks within tables and sections */
        table {
            page-break-inside: avoid;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        .summary-table {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container">

    <table class="header-table">
        <tr>
            <td>
                <div class="brand">LasaWheels</div>
                <div class="subtext">Vehicle Rental Booking Invoice</div>
            </td>
            <td class="text-right">
                <div class="invoice-title">INVOICE</div>
                <div><strong>Invoice No:</strong> {{ $invoiceNumber }}</div>
                <div><strong>Invoice Date:</strong> {{ now()->format('Y-m-d h:i A') }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Invoice Information</div>
    <div class="info-box">
        <table class="info-table">
            <tr>
                <td width="50%">
                    <strong>Booking ID:</strong> #{{ $booking->id }}<br>
                    <strong>Payment ID:</strong> #{{ $payment->id }}<br>
                    <strong>Payment Type:</strong>
                    {{ $payment->payment_type === 'deposit_cash' ? '20% Deposit + Remaining Cash' : 'Full Online Payment' }}<br>
                    <strong>Payment Status:</strong> {{ ucfirst($payment->status) }}
                </td>
                <td width="50%">
                    <strong>Customer Name:</strong> {{ $booking->user->name ?? 'N/A' }}<br>
                    <strong>Customer Email:</strong> {{ $booking->user->email ?? 'N/A' }}<br>
                    <strong>Payment Date:</strong>
                    {{ $payment->paid_at ? $payment->paid_at->format('Y-m-d h:i A') : 'N/A' }}<br>
                    @if($payment->gateway_reference)
                        <strong>Gateway Ref:</strong> {{ $payment->gateway_reference }}
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">Booking Details</div>
    <table class="details-table">
        <tr>
            <th width="30%">Vehicle</th>
            <td>{{ $booking->vehicle->brand ?? '' }} {{ $booking->vehicle->model ?? '' }}</td>
        </tr>
        <tr>
            <th>Service Type</th>
            <td>{{ ucfirst($booking->service) }}</td>
        </tr>
        @if($booking->service === 'driver' && $booking->driver)
            <tr>
                <th>Driver</th>
                <td>{{ $booking->driver->name }}</td>
            </tr>
        @endif
        <tr>
            <th>Pickup Location</th>
            <td>{{ $booking->pickup_location }}</td>
        </tr>
        <tr>
            <th>Drop Location</th>
            <td>{{ $booking->drop_location }}</td>
        </tr>
        <tr>
            <th>Pickup Date & Time</th>
            <td>{{ \Carbon\Carbon::parse($booking->pickup_datetime)->format('Y-m-d h:i A') }}</td>
        </tr>
        <tr>
            <th>Drop Date & Time</th>
            <td>{{ \Carbon\Carbon::parse($booking->drop_datetime)->format('Y-m-d h:i A') }}</td>
        </tr>
        <tr>
            <th>Total Days</th>
            <td>{{ $days }} day(s)</td>
        </tr>
        <tr>
            <th>Daily Rate</th>
            <td>Rs. {{ number_format($pricePerDay, 2) }}</td>
        </tr>
    </table>

    <div class="section-title">Price Summary</div>
    <table class="summary-table">
        <tr>
            <th>Description</th>
            <th class="amount">Amount (Rs.)</th>
        </tr>

        <tr>
            <td>Base Amount</td>
            <td class="amount">{{ number_format($baseAmount, 2) }}</td>
        </tr>

        @if($durationDiscountPercent > 0)
            <tr>
                <td>
                    Long Duration Discount
                    <span class="text-muted">({{ rtrim(rtrim(number_format($durationDiscountPercent, 2), '0'), '.') }}%)</span>
                </td>
                <td class="amount danger">- {{ number_format($durationDiscountAmount, 2) }}</td>
            </tr>
        @endif

        <tr>
            <td>Amount After Duration Discount</td>
            <td class="amount">{{ number_format($booking->original_price ?? $booking->total_price, 2) }}</td>
        </tr>

        @if(($booking->discount_amount ?? 0) > 0)
            <tr>
                <td>
                    Checkout Discount
                    @if($booking->discount_type === 'loyalty')
                        <span class="text-muted">(Loyalty Points)</span>
                    @elseif($booking->discount_type === 'code')
                        <span class="text-muted">({{ $booking->discount_code }})</span>
                    @endif
                </td>
                <td class="amount danger">- {{ number_format($booking->discount_amount, 2) }}</td>
            </tr>
        @endif

        <tr>
            <td><strong>Final Payable Amount</strong></td>
            <td class="amount highlight"><strong>{{ number_format($booking->total_price, 2) }}</strong></td>
        </tr>

        @if($booking->security_deposit)
            <tr>
                <td>Refundable Security Deposit</td>
                <td class="amount">{{ number_format($booking->security_deposit, 2) }}</td>
            </tr>
        @endif

        @if($payment->payment_type === 'deposit_cash')
            <tr>
                <td>Deposit Paid Online</td>
                <td class="amount">{{ number_format($payment->deposit_amount, 2) }}</td>
            </tr>
            <tr>
                <td>Remaining Cash to Pay</td>
                <td class="amount">{{ number_format($payment->remaining_amount, 2) }}</td>
            </tr>
        @else
            <tr>
                <td>Amount Paid Online</td>
                <td class="amount">{{ number_format($payment->paid_amount, 2) }}</td>
            </tr>
        @endif
    </table>

    @if(!empty($booking->special_request))
        <div class="section-title">Special Request</div>
        <div class="info-box">
            {{ $booking->special_request }}
        </div>
    @endif

    <div class="footer">
        Thank you for choosing <strong>LasaWheels</strong>.<br>
        This is a system-generated invoice.
    </div>
</div>
</body>
</html>
