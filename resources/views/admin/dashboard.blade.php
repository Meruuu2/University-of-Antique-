@extends('layouts.admin')

@section('admin-content')
    <div class="fms-card">
        <div class="fms-page-header border-0 pb-0 mb-6">
            <h1 class="fms-page-title">Admin Dashboard</h1>
        </div>
        <p class="mb-6 text-sm text-neutral-600">
            Welcome to the GSU Admin Portal. Manage facilities, requests, and users from the sidebar.
        </p>

        <h2 class="mb-3 text-sm font-semibold uppercase tracking-widest text-neutral-500">Quick access</h2>
        <div class="fms-stat-grid mb-8">
            <a href="{{ route('admin.facilities.index') }}" class="fms-stat-card">
                <h3>Facilities</h3>
                <p>Manage campus facilities and equipment</p>
            </a>
            <a href="{{ route('admin.forms.facilities.index') }}" class="fms-stat-card">
                <h3>Utilization requests</h3>
                <p>Review and approve facility utilization submissions</p>
            </a>
            <a href="{{ route('admin.bookings.index') }}" class="fms-stat-card">
                <h3>Bookings</h3>
                <p>View and adjust confirmed bookings</p>
            </a>
            <a href="{{ route('admin.users.index') }}" class="fms-stat-card">
                <h3>User management</h3>
                <p>Create and manage portal accounts</p>
            </a>
        </div>
 
        {{-- GSU forms (PDF) hidden from main admin dashboard to reduce confusion.
        <h2 class="mb-3 text-sm font-semibold uppercase tracking-widest text-neutral-500">GSU forms (PDF)</h2>
        <div class="fms-stat-grid">
            <a href="{{ route('forms.facilities.show') }}" class="fms-stat-card">
                <h3>Facilities &amp; utilization form</h3>
                <p>Generate PDF for facility utilization requests</p>
            </a>
            <a href="{{ route('forms.repair.show') }}" class="fms-stat-card">
                <h3>Repair &amp; maintenance form</h3>
                <p>Generate PDF for repair and maintenance requests</p>
            </a>
        </div>
        --}}
    </div>
@endsection
