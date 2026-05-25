<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Booking;
use App\Models\Facility;
use Carbon\Carbon;

class StoreFacilitiesUtilizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date_activity' => 'required|date|after_or_equal:' . now()->addDays(7)->toDateString(),
            'start_time'    => 'required|date_format:H:i',
            'end_time'      => 'required|date_format:H:i|after:start_time',
            'facility_id'   => [
                'required',
                'exists:facilities,id',
                function ($attribute, $value, $fail) {
                    $date = $this->input('date_activity');
                    $startTime = $this->input('start_time');
                    $endTime = $this->input('end_time');

                    if (!$date || !$startTime || !$endTime) {
                        return; // Other validation rules will handle this
                    }

                    $startDateTime = Carbon::parse($date . ' ' . $startTime);
                    $endDateTime = Carbon::parse($date . ' ' . $endTime);

                    $isOverlapping = Booking::whereHas('facilities', function ($query) use ($value) {
                        $query->where('facility_id', $value);
                    })
                    ->where(function ($query) use ($startDateTime, $endDateTime) {
                        $query->where(function ($q) use ($startDateTime, $endDateTime) {
                            $q->where('start_time', '<', $endDateTime)
                              ->where('end_time', '>', $startDateTime);
                        });
                    })
                    ->where('status', 'booked')
                    ->exists();

                    if ($isOverlapping) {
                        $fail('The selected facility is already booked for the chosen date and time.');
                    }
                }
            ],
            'purpose'       => 'required|string|max:500',
            'qty_monobloc'  => 'nullable|integer|min:0',
            'qty_table'     => 'nullable|integer|min:0',
            'qty_fan'       => 'nullable|integer|min:0',
            'qty_rostrum'   => 'nullable|integer|min:0',
            'qty_flag'      => 'nullable|integer|min:0',
            'qty_sound'     => 'nullable|integer|min:0',
            'qty_led'       => 'nullable|integer|min:0',
            'venue_others'  => 'nullable|string|max:255',

            // Noted by (signatory)
            'noted_signatory_id' => 'nullable|string',
            'noted_signatory_custom' => 'nullable|string|max:255',
        ];
    }
}