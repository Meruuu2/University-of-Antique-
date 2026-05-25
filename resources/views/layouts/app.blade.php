<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'UA Facility Management System'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans+Flex:opsz,wght@8..144,100..1000&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white text-black antialiased">
<div class="flex min-h-screen flex-col">
    <header class="border-b border-black bg-white">
        <div class="mx-auto flex w-full max-w-[1440px] items-center justify-between px-6 py-4">
            <a href="{{ auth()->check() ? match(optional(auth()->user())->role) {
                'admin' => route('admin.dashboard'),
                'college_staff' => route('college.dashboard'),
                'org_staff' => route('org.dashboard'),
                default => url('/'),
            } : url('/') }}" class="text-lg font-semibold text-black">
                UA Facility Management
            </a>
            <nav class="flex items-center gap-4 text-sm">
                @if(auth()->check())
                    <a href="{{ route('notifications.index') }}" class="text-neutral-700 hover:underline">
                        Notifications
                        @if(($unreadNotificationsCount ?? 0) > 0)
                            <span style="background-color: #dc2626; color: white; padding: 2px 6px; border-radius: 12px; font-size: 11px;">
                                {{ $unreadNotificationsCount }}
                            </span>
                        @endif
                    </a>

                    <span class="text-neutral-600"> 
                        {{ optional(auth()->user())->name }}
                        <span class="text-neutral-400">·</span>
                        {{ str_replace('_', ' ', optional(auth()->user())->role ?? 'user') }}
                    </span>
                    <form class="inline" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="fms-btn-secondary px-3 py-1.5 text-xs">Logout</button>
                    </form>
                @endif
            </nav>
        </div>
    </header>

    <main class="@yield('main-class', 'mx-auto w-full max-w-6xl flex-1 px-4 py-6')">
        @if (session('status'))
            <div class="fms-alert-success mb-4">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div class="fms-alert-error mb-4">{{ session('error') }}</div>
        @endif

        <div class="mb-4 flex justify-between items-center">
            {{-- Nav buttons (Back & Refresh) --}}
            <div class="flex items-center space-x-2">
                @php
                    // Routes where "Back" should be hidden (main dashboards)
                    $noBackRoutes = ['admin.dashboard', 'college.dashboard', 'org.dashboard', 'home'];
                    $showBack = !in_array(optional(request()->route())->getName(), $noBackRoutes, true);
                @endphp

                @if($showBack)
                    <button type="button"
                            onclick="window.history.back()"
                            class="px-3 py-1.5 border border-gray-300 rounded text-xs text-gray-700 hover:bg-gray-50">
                        ← Back
                    </button>
                @endif

                <button type="button"
                        onclick="window.location.reload()"
                        class="px-3 py-1.5 border border-gray-300 rounded text-xs text-gray-700 hover:bg-gray-50">
                    Refresh
                </button>
            </div>

            {{-- Simple status or placeholder for future notifications --}}
            {{-- You can later populate this with real notification counts --}}
        </div>

        @yield('content')
    </main>

    <footer class="border-t border-black bg-white">
        <div class="mx-auto w-full max-w-[1440px] px-6 py-4 text-xs text-neutral-600">
            University of Antique · GSU Facility & Equipment Management System
        </div>
    </footer>
</div>
</body>
</html>
