@extends('layouts.app')

@section('main-class', 'mx-auto w-full max-w-[1440px] flex-1 px-0 py-0')

@section('content')
<div class="fms-shell">
    @include('layouts.partials.sidebar', [
        'portalTitle' => 'College Staff',
        'portalSubtitle' => optional(auth()->user())->college_name ?? 'College not set',
        'sections' => [
            [
                'heading' => 'Overview',
                'links' => [
                    ['label' => 'Dashboard', 'route' => 'college.dashboard'],
                    ['label' => 'Booking calendar', 'route' => 'college.calendar'],
                    ['label' => 'Bookings', 'route' => 'college.bookings.index', 'routes' => 'college.bookings.*'],
                ],
            ],
            [
                'heading' => 'Facilities',
                'links' => [
                    ['label' => 'My facilities', 'route' => 'college.facilities.index'],
                    ['label' => 'Add facility', 'route' => 'college.facilities.create'],
                ],
            ],
            [ 
                'heading' => 'Requests',
                'links' => [
                    ['label' => 'My requests', 'route' => 'college.requests.index'],
                    ['label' => 'New request', 'route' => 'college.requests.facilities.create', 'routes' => 'college.requests.facilities.*'],
                ],
            ],
        ],
        'footer' => '<span class="text-black font-medium">Signed in as</span><br>' . e(optional(auth()->user())->name),
    ])

    <section class="fms-main">
        @yield('college-content')
    </section>
</div>
@endsection
