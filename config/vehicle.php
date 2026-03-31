<?php

return [

    // Rental billing follows 24-hour blocks with this grace window.
    // Example: with 2h grace, 25 hours is billed as 1 day, 27 hours as 2 days.
    'billing_grace_hours' => 2,

    'wheel_types' => [
        '2_wheeler' => '2 wheeler',
        '4_wheeler' => '4 wheeler',
    ],

    'vehicle_types' => [
        'car' => 'Car',
        'sedan' => 'Sedan',
        'pickup' => 'Pickup',
        'suv' => 'SUV',
    ],

    'fuel_types' => [
        'petrol' => 'Petrol',
        'diesel' => 'Diesel',
        'electric' => 'Electric',
    ],

    'transmissions' => [
        'manual' => 'Manual',
        'automatic' => 'Automatic',
    ],

    'status_options' => [
        'available' => 'Available',
        'rented' => 'Rented',
        'maintenance' => 'Maintenance',
        'inactive' => 'Inactive',
    ],

];
