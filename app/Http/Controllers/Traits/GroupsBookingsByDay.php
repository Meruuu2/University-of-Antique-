<?php

namespace App\Http\Controllers\Traits;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

trait GroupsBookingsByDay
{
    protected function resolveMonth(Request $request): Carbon
    {
        $month = $request->query('month');

        return $month
            ? Carbon::createFromFormat('Y-m', $month)->startOfMonth()
            : now()->startOfMonth();
    }

    protected function groupByDay(Collection $bookings): array
    {
        $days = [];

        foreach ($bookings as $booking) {
            $dayKey = $booking->start_time->toDateString();
            $days[$dayKey][] = $booking;
        }

        return $days;
    }
}
