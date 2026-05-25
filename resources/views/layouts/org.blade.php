@extends('layouts.app')

@section('main-class', 'mx-auto w-full max-w-[1440px] flex-1 px-0 py-0')

@section('content')
<div class="fms-shell">
    @include('layouts.partials.sidebar', [
        'portalTitle' => 'Organization Staff',
        'portalSubtitle' => optional(auth()->user())->organization_name ?? 'Organization not set',
        'sections' => [
            [
                'heading' => 'Overview',
                'links' => [
                    ['label' => 'Dashboard', 'route' => 'org.dashboard'],
                    ['label' => 'Booking calendar', 'route' => 'org.bookings.index'],
                ],
            ],
            [
                'heading' => 'Requests',
                'links' => [
                    ['label' => 'My requests', 'route' => 'org.requests.facilities.index', 'routes' => 'org.requests.facilities.*'],
                    ['label' => 'New request', 'route' => 'org.requests.facilities.create', 'routes' => 'org.requests.facilities.*'],
                ],
            ],
        ],
        'footer' => '<span class="text-black font-medium">Organization</span><br>' . e(optional(auth()->user())->organization_name ?? 'Not set'),
    ])

    <section class="fms-main">
        @yield('org-content')
    </section>
</div>
@endsection 
