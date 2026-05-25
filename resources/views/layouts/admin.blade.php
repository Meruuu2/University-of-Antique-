@extends('layouts.app')

@section('main-class', 'mx-auto w-full max-w-[1440px] flex-1 px-0 py-0')

@section('content')
<div class="fms-shell">
    @include('layouts.partials.sidebar', [
        'portalTitle' => 'GSU Admin',
        'portalSubtitle' => optional(auth()->user())->name,
        'sections' => [
            [
                'heading' => 'Overview',
                'links' => [
                    ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
                ],
            ],
            [
                'heading' => 'Facilities',
                'links' => [
                    ['label' => 'All facilities', 'route' => 'admin.facilities.index'],
                    ['label' => 'Add facility', 'route' => 'admin.facilities.create'],
                ],
            ],
[
                'heading' => 'Requests',
                'links' => [
                    [
                        'label'  => 'Utilization requests',
                        'route'  => 'admin.forms.facilities.index',
                        'routes' => 'admin.forms.facilities.*',
                    ],
                    [
                        'label'  => 'Bookings',
                        'route'  => 'admin.bookings.index',
                        // Only highlight on index, not on create-direct
                        'routes' => ['admin.bookings.index'],
                    ],
                    [
                        'label'  => 'Create Direct Booking',
                        'route'  => 'admin.bookings.create-direct',
                        // Highlight only on the direct booking form
                        'routes' => ['admin.bookings.create-direct'],
                    ],
                    [
                        'label' => 'Booking calendar',
                        'route' => 'admin.calendar',
                    ],
                    [
                        'label' => 'Monthly overview',
                        'route' => 'admin.overview',
                    ],
                ],
            ],
            [
                'heading' => 'GSU forms (PDF)',
                'links' => [
                    ['label' => 'Facilities utilization form', 'route' => 'forms.facilities.show', 'routes' => 'forms.facilities.*'],
                    ['label' => 'Repair & maintenance form', 'route' => 'forms.repair.show', 'routes' => 'forms.repair.*'],
                ],
            ], 
            [
                'heading' => 'Administration',
                'links' => [
                    ['label' => 'Users', 'route' => 'admin.users.index'],
                    ['label' => 'Create user', 'route' => 'admin.users.create'],
                ],
            ],
        ],
    ])

    <section class="fms-main">
        @yield('admin-content')
    </section>
</div>
@endsection