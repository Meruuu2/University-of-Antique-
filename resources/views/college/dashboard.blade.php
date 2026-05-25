@extends('layouts.college')

@section('college-content')
    <div class="fms-card">
        <div class="fms-page-header border-0 pb-0 mb-6">
            <h1 class="fms-page-title">College Staff Dashboard</h1>
        </div>
        <p class="mb-6 text-sm text-neutral-600">
            Manage your college facilities, bookings, and utilization requests from the sidebar.
        </p>

        <h2 class="mb-3 text-sm font-semibold uppercase tracking-widest text-neutral-500">Quick access</h2>
        <div class="fms-stat-grid">
            <a href="{{ route('college.facilities.index') }}" class="fms-stat-card">
                <h3>My facilities</h3>
                <p>View and manage facilities owned by your college</p>
            </a>
            <a href="{{ route('college.bookings.index') }}" class="fms-stat-card">
                <h3>Bookings</h3>
                <p>Review booking requests for your facilities</p>
            </a>
            <a href="{{ route('college.requests.facilities.create') }}" class="fms-stat-card">
                <h3>Utilization request</h3>
                <p>Submit a facilities utilization request to GSU</p>
            </a>
        </div>
    </div>
@endsection
