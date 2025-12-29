@extends('admin.layouts.master')

@section('admin-content')
<div class="container-fluid mt-4">

    <div class="row g-3">
        <!-- Total Users -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="mb-1">Total Users</h6>
                    <h3 class="mb-0">{{ $statistics['totalUsersCount'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="card mt-4 shadow-sm">
        <div class="card-header">
            Recent Users
        </div>

        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th style="width: 40%;">Name</th>
                        <th style="width: 60%;">Email</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(($statistics['recentUsers'] ?? []) as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center py-3">No users found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
