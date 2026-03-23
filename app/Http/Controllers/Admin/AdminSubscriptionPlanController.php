<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminSubscriptionPlanController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::latest()->paginate(10);

        return view('admin.subscription-plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.subscription-plans.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        SubscriptionPlan::create($this->prepareData($data));

        return redirect()
            ->route('admin.subscription-plans.index')
            ->with('success', 'Subscription plan created successfully.');
    }

    public function edit(SubscriptionPlan $subscriptionPlan)
    {
        return view('admin.subscription-plans.edit', compact('subscriptionPlan'));
    }

    public function update(Request $request, SubscriptionPlan $subscriptionPlan)
    {
        $data = $this->validateData($request, $subscriptionPlan->id);

        $subscriptionPlan->update($this->prepareData($data));

        return redirect()
            ->route('admin.subscription-plans.index')
            ->with('success', 'Subscription plan updated successfully.');
    }

    private function validateData(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subscription_plans', 'slug')->ignore($ignoreId),
            ],
            'billing_cycle' => ['required', 'in:free,monthly,yearly'],
            'price' => ['required', 'numeric', 'min:0'],
            'max_vehicles' => ['required', 'integer', 'min:1'],
            'max_drivers' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    private function prepareData(array $data): array
    {
        $data['slug'] = Str::slug($data['slug']);
        $data['is_active'] = isset($data['is_active']) ? (bool) $data['is_active'] : false;

        return $data;
    }
}
