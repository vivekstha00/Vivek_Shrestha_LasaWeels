Hello {{ $user->name ?? 'there' }},

Your booking request has been created successfully.

Booking ID: #{{ $booking->id }}
Status: {{ ucfirst(str_replace('_', ' ', $booking->status ?? 'pending')) }}
Payment Status: {{ ucfirst($booking->payment_status ?? 'unpaid') }}
Service: {{ ucfirst($booking->service ?? 'self') }}
Pickup: {{ optional($booking->pickup_datetime)->format('d M Y, h:i A') ?? '-' }}
Drop: {{ optional($booking->drop_datetime)->format('d M Y, h:i A') ?? '-' }}
Route: {{ $booking->pickup_location ?? '-' }} -> {{ $booking->drop_location ?? '-' }}
Total Amount: NPR {{ number_format((float) ($booking->total_price ?? 0), 2) }}
@if(!is_null($booking->security_deposit))
Security Deposit: NPR {{ number_format((float) $booking->security_deposit, 2) }}
@endif

View Booking:
{{ route('user.booking.show', $booking->id) }}

If payment is pending, please complete payment to confirm your booking.

© {{ date('Y') }} LasaWheels
