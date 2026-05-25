@extends('layouts.app')

@section('content')
@php
    use Carbon\Carbon;
    $monthLabel = $currentMonth->format('F Y');
    $daysInMonth = $currentMonth->daysInMonth;
    $firstWeekday = $currentMonth->copy()->startOfMonth()->dayOfWeekIso;
    $prevMonth = $currentMonth->copy()->subMonth()->format('Y-m');
    $nextMonth = $currentMonth->copy()->addMonth()->format('Y-m');
@endphp

<div class="fms-card">
    <div class="fms-page-header border-0 pb-0 mb-4">
        <div>
            <h1 class="fms-page-title">Facility Schedule</h1>
            <p class="text-xs text-neutral-600">
                Public read-only view of confirmed facility bookings. Contact GSU for changes.
            </p>
        </div>
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('home', ['month' => $prevMonth]) }}" class="fms-link">← Prev</a>
            <span class="text-neutral-600">{{ $monthLabel }}</span>
            <a href="{{ route('home', ['month' => $nextMonth]) }}" class="fms-link">Next →</a>
        </div>
    </div>

    <div class="mb-2 text-xs text-neutral-600">
        Only active bookings (booked / rescheduled) are shown here. Pending or merely approved requests are not included.
    </div>

    <div class="mb-8 border border-black overflow-x-auto">
        <table class="min-w-full text-xs">
            <thead class="bg-neutral-100 border-b border-black">
                <tr>
                    @foreach (['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $dow)
                        <th class="px-2 py-2 border-r border-black last:border-r-0 text-center font-semibold">{{ $dow }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php
                    $dayCounter = 1;
                    $cellCount = 0;
                @endphp

                @while ($dayCounter <= $daysInMonth)
                    <tr class="border-t border-black">
                        @for ($col = 1; $col <= 7; $col++)
                            @php $cellCount++; @endphp
                           <td class="align-top border-r border-black last:border-r-0 p-1 h-24 w-[110px]">
                                @if ($cellCount >= $firstWeekday && $dayCounter <= $daysInMonth)
                                    @php
                                        $dateObj = $currentMonth->copy()->day($dayCounter);
                                        $dateKey = $dateObj->toDateString();
                                        $dayBookings = $days[$dateKey] ?? [];
                                    @endphp
                                  <div class="flex flex-col items-center justify-center h-full">
                                        <span class="text-[11px] font-semibold mb-0.5">{{ $dayCounter }}</span>
                                        @if (count($dayBookings) > 0)
                                            <a
                                                href="{{ route('home', ['month' => $currentMonth->format('Y-m'), 'day' => $dayCounter]) }}"
                                                class="text-[10px] text-neutral-700"
                                            >
                                                {{ count($dayBookings) }} booking{{ count($dayBookings) > 1 ? 's' : '' }}
                                            </a>
                                        @endif
                                    </div>

                                    @php $dayCounter++; @endphp
                                @endif
                            </td>
                        @endfor
                    </tr>
                @endwhile
            </tbody>
        </table>
   </div>

    @if(!empty($selectedDate) && $selectedDateBookings->isNotEmpty())
        @php
            $dateLabel = $selectedDate->format('F d, Y');
        @endphp
        <div class="mb-4 border border-black p-3 text-xs">
            <h2 class="mb-2 text-sm font-semibold text-black">Bookings on {{ $dateLabel }}</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs">
                    <thead class="bg-neutral-100 border-b border-black">
                        <tr>
                            <th class="px-2 py-1 text-left">Time</th>
                            <th class="px-2 py-1 text-left">Facilities</th>
                            <th class="px-2 py-1 text-left">Purpose</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($selectedDateBookings as $booking)
                            @php
                                $facilityNames = $booking->facilities->pluck('name')->join(', ');
                            @endphp
                            <tr class="border-b border-black last:border-b-0">
                                <td class="px-2 py-1">
                                    {{ $booking->start_time->format('H:i') }} – {{ $booking->end_time->format('H:i') }}
                                </td>
                                <td class="px-2 py-1">
                                    {{ $facilityNames !== '' ? $facilityNames : 'Unknown facility' }}
                                </td>
                                <td class="px-2 py-1">
                                    {{ \Illuminate\Support\Str::limit($booking->purpose ?? '-', 120) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="mt-2 text-[11px] text-neutral-600">
                This is a read-only public view. Contact GSU for any changes.
            </p>
        </div>
    @endif

    {{-- Facilities currently unavailable / under maintenance --}}
    @if(isset($unavailableFacilities) && $unavailableFacilities->isNotEmpty())
        <div class="mb-4 border border-black p-3 text-xs">
            <h2 class="mb-2 text-sm font-semibold text-black">Facilities not available for booking</h2>
            <table class="min-w-full text-xs">
                <thead class="bg-neutral-100 border-b border-black">
                    <tr>
                        <th class="px-2 py-1 text-left">Facility</th>
                        <th class="px-2 py-1 text-left">Status</th>
                        <th class="px-2 py-1 text-left">Location</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($unavailableFacilities as $facility)
                        <tr class="border-b border-black last:border-b-0">
                            <td class="px-2 py-1">{{ $facility->name }}</td>
                            <td class="px-2 py-1">
                                {{ ucfirst($facility->availability_status ?? 'unavailable') }}
                            </td>
                            <td class="px-2 py-1">{{ $facility->location }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <p class="mt-2 text-[11px] text-neutral-600">
                These facilities are marked as unavailable or under maintenance by GSU or the owning college and cannot be booked.
            </p>
        </div>
    @endif

    @auth
        <p class="text-xs text-neutral-600">
            Signed in as {{ auth()->user()->name }}.
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="fms-link">Logout</a>
        </p>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    @else
        <p class="text-xs text-neutral-600">
            Staff? <a href="{{ route('login') }}" class="fms-link">Sign in</a> to access your portal.
        </p>
    @endauth
</div>
@endsection
