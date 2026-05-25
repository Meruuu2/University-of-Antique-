@extends('layouts.college')

@section('college-content')
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
            <h1 class="fms-page-title">Booking Calendar</h1>
            <p class="text-xs text-neutral-600">Read-only view of your bookings and bookings for {{ $collegeName }} facilities.</p>
        </div>
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('college.calendar', ['month' => $prevMonth]) }}" class="fms-link">← Prev</a>
            <span class="text-neutral-600">{{ $monthLabel }}</span>
            <a href="{{ route('college.calendar', ['month' => $nextMonth]) }}" class="fms-link">Next →</a>
        </div>
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
                            @php
                                $displayDay = $cellCount >= $firstWeekday && $dayCounter <= $daysInMonth;
                                $dayBookings = [];
                                $hasBookings = false;

                                if ($displayDay) {
                                    $dateObj = $currentMonth->copy()->day($dayCounter);
                                    $dateKey = $dateObj->toDateString();
                                    $dayBookings = $days[$dateKey] ?? [];
                                    $hasBookings = count($dayBookings) > 0;
                                }
                            @endphp
                            <td class="align-top border-r border-black last:border-r-0 p-1 h-24 w-[110px] {{ $hasBookings ? 'bg-neutral-100' : '' }}">
                                @if ($displayDay)
                                    <div class="flex flex-col items-center justify-center h-full">
                                        <span class="text-[11px] font-semibold mb-0.5">{{ $dayCounter }}</span>
                                        @if ($hasBookings)
                                            <a
                                                href="{{ route('admin.calendar', ['month' => $currentMonth->format('Y-m'), 'day' => $dayCounter]) }}"
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
        <h2 class="mb-2 text-sm font-semibold uppercase tracking-widest text-neutral-500">
            Bookings on {{ $dateLabel }}
        </h2>
        <div class="fms-table-wrap mb-6">
            <table class="fms-table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Facilities</th>
                        <th>Requester</th>
                        <th>Purpose</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($selectedDateBookings as $booking)
                        @php
                            $facilityNames = $booking->facilities->pluck('name')->join(', ');
                        @endphp
                        <tr>
                            <td>
                                {{ $booking->start_time->format('H:i') }} – {{ $booking->end_time->format('H:i') }}
                            </td>
                            <td>{{ $facilityNames !== '' ? $facilityNames : 'Unknown facility' }}</td>
                            <td>
                                {{ optional($booking->requester)->name ?? 'Unknown' }}<br>
                                <span class="text-xs text-neutral-500">
                                    {{ $booking->requester_unit ?? ucfirst($booking->requester_type) }}
                                </span>
                            </td>
                            <td>{{ \Illuminate\Support\Str::limit($booking->purpose ?? '-', 80) }}</td>
                            <td><span class="fms-badge">{{ ucfirst($booking->status) }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <h2 class="mb-2 text-sm font-semibold uppercase tracking-widest text-neutral-500">Facility booking overview ({{ $monthLabel }})</h2>

    <div class="fms-table-wrap">
        <table class="fms-table">
            <thead>
                <tr>
                    <th>Facility</th>
                    <th>Availability</th>
                    <th>Bookings this month</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($facilityCounts as $item)
                    @php
                        $facility = $item['facility'];
                        $count = $item['count'];
                        $status = $facility->availability_status ?? 'available';
                    @endphp
                    <tr>
                        <td>{{ $facility->name }}</td>
                        <td><span class="fms-badge">{{ ucfirst($status) }}</span></td>
                        <td>{{ $count }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
