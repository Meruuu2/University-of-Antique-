<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\GroupsBookingsByDay;
use App\Models\Booking;
use App\Models\Facility;
use Illuminate\Http\Request;

class PublicCalendarController extends Controller
{
    use GroupsBookingsByDay;

    public function index(Request $request)
    {
        $current = $this->resolveMonth($request);
        $start = $current->copy()->startOfMonth();
        $end = $current->copy()->endOfMonth();

       $bookings = Booking::with('facilities')
            ->whereBetween('start_time', [$start, $end])
            ->whereIn('status', ['booked', 'rescheduled'])
            ->orderBy('start_time')
            ->get();

        $days = $this->groupByDay($bookings);

        $selectedDate = null;
        $selectedDateBookings = collect();
        if ($request->filled('day')) {
            $dayInt = (int) $request->query('day');
            if ($dayInt >= 1 && $dayInt <= $current->daysInMonth) {
                $selectedDate = $current->copy()->day($dayInt);
                $key = $selectedDate->toDateString();
                $selectedDateBookings = collect($days[$key] ?? [])->sortBy('start_time');
            }
        }

        $unavailableFacilities = Facility::whereIn('availability_status', ['unavailable', 'maintenance'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

 return view('public.calendar', [
            'currentMonth'          => $current,
            'days'                  => $days,
            'unavailableFacilities' => $unavailableFacilities,
            'selectedDate'          => $selectedDate,
            'selectedDateBookings'  => $selectedDateBookings,
        ]);
    }
}
