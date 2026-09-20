<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_vehicle_list_only_shows_active_approved_vehicles_with_valid_compliance(): void
    {
        $vendor = User::factory()->create(['role' => 'vendor']);

        $visibleVehicle = $this->createVehicle($vendor, [
            'model' => 'Visible Model',
            'registration_no' => 'VISIBLE-001',
            'insurance_expiry_date' => today()->addYear(),
            'road_tax_expiry_date' => today()->addYear(),
        ]);

        $expiredVehicle = $this->createVehicle($vendor, [
            'model' => 'Expired Model',
            'registration_no' => 'EXPIRED-001',
            'insurance_expiry_date' => today()->subDay(),
            'road_tax_expiry_date' => today()->addYear(),
        ]);

        $response = $this->get(route('vehicles.index'));

        $response->assertOk();
        $response->assertSee($visibleVehicle->model);
        $response->assertDontSee($expiredVehicle->model);
    }

    public function test_admin_cannot_activate_a_vehicle_with_expired_compliance(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $vendor = User::factory()->create(['role' => 'vendor']);

        $vehicle = $this->createVehicle($vendor, [
            'is_active' => false,
            'insurance_expiry_date' => today()->subDay(),
            'road_tax_expiry_date' => today()->addYear(),
        ]);

        $response = $this->actingAs($admin)->post(route('admin.vehicles.toggleActive', $vehicle));

        $response->assertSessionHas('error');
        $this->assertFalse($vehicle->fresh()->is_active);
    }

    private function createVehicle(User $vendor, array $overrides = []): Vehicle
    {
        return Vehicle::query()->create(array_merge([
            'vendor_id' => $vendor->id,
            'title' => 'Test Vehicle',
            'wheel_type' => '4_wheeler',
            'vehicle_type' => 'car',
            'brand' => 'Test Brand',
            'model' => 'Test Model',
            'registration_no' => 'TEST-' . fake()->unique()->numberBetween(1000, 9999),
            'fuel_type' => 'petrol',
            'transmission' => 'automatic',
            'seating_capacity' => 5,
            'price_per_day' => 5000,
            'location_city' => 'Kathmandu',
            'status' => 'approved',
            'is_active' => true,
        ], $overrides));
    }
}
