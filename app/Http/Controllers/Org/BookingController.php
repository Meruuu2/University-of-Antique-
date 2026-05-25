<?php

namespace App\Http\Controllers\Org;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\GroupsBookingsByDay;
use App\Models\Booking;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    use GroupsBookingsByDay;

    public function calendar(Request $request)
    {
        $user = Auth::user();

        $current = $this->resolveMonth($request);
        $start = $current->copy()->startOfMonth();
        $end = $current->copy()->endOfMonth();

        $bookings = Booking::with('facilities')
            ->whereBetween('start_time', [$start, $end])
            ->where('requester_id', $user->id)
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

                $selectedDateBookings = collect($days[$key] ?? [])
                    ->sortBy('start_time')
                    ->values();
            }
        }

        return view('org.bookings.calendar', [
            'currentMonth' => $current,
            'days' => $days,
            'selectedDate' => $selectedDate,
            'selectedDateBookings' => $selectedDateBookings,
        ]);
    }
}