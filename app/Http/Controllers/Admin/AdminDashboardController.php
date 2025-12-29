<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUsersCount = User::where('role', 'user')->count();

        $recentUsers = User::where('role', 'user')
            ->latest()
            ->take(5)
            ->get();

        $statistics = [
            'totalUsersCount' => $totalUsersCount,
            'recentUsers' => $recentUsers,
        ];

        return view('admin.pages.dashboard', compact('statistics'));
    }
}
