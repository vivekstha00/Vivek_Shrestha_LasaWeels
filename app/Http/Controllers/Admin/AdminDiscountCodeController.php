<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\DiscountCode;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminDiscountCodeController extends Controller
{
    public function index()
    {
        $discountCodes = DiscountCode::latest()->paginate(10);

        return view('admin.discount-codes.index', compact('discountCodes'));
    }

    public function create()
    {
        return view('admin.discount-codes.create');
    }

    public function show(DiscountCode $discountCode)
    {
        $normalizedCode = strtoupper($discountCode->code);

        $usageBookings = Booking::query()
            ->with(['user', 'vehicle', 'payment'])
            ->where('discount_type', 'code')
            ->whereRaw('UPPER(discount_code) = ?', [$normalizedCode])
            ->latest()
            ->paginate(10, ['*'], 'usage_page');

        $totalUsageBookings = Booking::query()
            ->where('discount_type', 'code')
            ->whereRaw('UPPER(discount_code) = ?', [$normalizedCode])
            ->count();

        $completedUsageBookings = Booking::query()
            ->where('discount_type', 'code')
            ->whereRaw('UPPER(discount_code) = ?', [$normalizedCode])
            ->whereIn('payment_status', ['paid', 'partial'])
            ->count();

        return view('admin.discount-codes.show', compact(
            'discountCode',
            'usageBookings',
            'totalUsageBookings',
            'completedUsageBookings'
        ));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        DiscountCode::create($this->prepareData($data));

        return redirect()
            ->route('admin.discount-codes.index')
            ->with('success', 'Discount code created successfully.');
    }

    public function edit(DiscountCode $discountCode)
    {
        return view('admin.discount-codes.edit', compact('discountCode'));
    }

    public function update(Request $request, DiscountCode $discountCode)
    {
        $data = $this->validateData($request, $discountCode->id);

        $discountCode->update($this->prepareData($data));

        return redirect()
            ->route('admin.discount-codes.index')
            ->with('success', 'Discount code updated successfully.');
    }

    public function destroy(DiscountCode $discountCode)
    {
        $discountCode->delete();

        return redirect()
            ->route('admin.discount-codes.index')
            ->with('success', 'Discount code deleted successfully.');
    }

    private function validateData(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('discount_codes', 'code')->ignore($ignoreId),
            ],
            'type' => ['required', 'in:fixed,percentage'],
            'value' => ['required', 'numeric', 'min:1'],
            'max_discount_amount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'valid_from' => ['nullable', 'date'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:valid_from'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    private function prepareData(array $data): array
    {
        $data['code'] = strtoupper(trim($data['code']));
        $data['max_discount_amount'] = $data['max_discount_amount'] !== null && $data['max_discount_amount'] !== ''
            ? $data['max_discount_amount']
            : null;

        $data['usage_limit'] = $data['usage_limit'] !== null && $data['usage_limit'] !== ''
            ? $data['usage_limit']
            : null;

        $data['valid_from'] = $data['valid_from'] ?: null;
        $data['valid_until'] = $data['valid_until'] ?: null;
        $data['is_active'] = isset($data['is_active']) ? (bool) $data['is_active'] : false;

        return $data;
    }
}
