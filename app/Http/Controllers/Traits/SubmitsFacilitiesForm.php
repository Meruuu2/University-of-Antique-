<?php

namespace App\Http\Controllers\Traits;

use App\Http\Requests\StoreFacilitiesUtilizationRequest;
use App\Models\Booking;
use App\Models\Facility;
use App\Models\Facility;
use App\Models\FormSubmission;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;

trait SubmitsFacilitiesForm
{
    abstract protected function requesterType(): string;

    abstract protected function requesterUnit(): string;

    abstract protected function submittedRedirectRoute(): string;

    protected function handleStoreFacilities(StoreFacilitiesUtilizationRequest $request, int $facilityId): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();
        $validated = $request->validated();

        $startDateTime = Carbon::parse($validated['date_activity'] . ' ' . $validated['start_time']);
        $endDateTime   = Carbon::parse($validated['date_activity'] . ' ' . $validated['end_time']);

        $facility = Facility::findOrFail($facilityId);

        $payload = [
            'control_no'      => null,
            'date_request'    => now()->toDateString(),
            'requester_name'  => $user->name,
            'requester_unit'  => $this->requesterUnit(),
            'date_activity'   => $validated['date_activity'],
            'time_range'      => [
                'start' => $validated['start_time'],
                'end'   => $validated['end_time'],
            ],
            'facility_id'     => $facility->id,
            'facility_name'   => $facility->name,
            'purpose'         => $validated['purpose'],
            'equipment'       => [
                'monobloc_chair' => (int) $request->input('qty_monobloc', 0),
                'table'          => (int) $request->input('qty_table', 0),
                'electric_fan'   => (int) $request->input('qty_fan', 0),
                'rostrum'        => (int) $request->input('qty_rostrum', 0),
                'flag'           => (int) $request->input('qty_flag', 0),
                'sound'          => (int) $request->input('qty_sound', 0),
                'led'            => (int) $request->input('qty_led', 0),
            ],
        ];

        $submission = FormSubmission::create([
            'type'           => 'facilities_utilization',
            'requester_id'   => $user->id,
            'requester_type' => $this->requesterType(),
            'requester_unit' => $this->requesterUnit(),
            'status'         => 'pending',
            'payload'        => $payload,
        ]);

        $notifications = app(NotificationService::class);
        $notifications->notifyFormSubmitted($user->id, $submission->id);
        $notifications->notifyAdmins(
            'form_pending_admin',
            'New facilities request from ' . $this->requesterType(),
            $this->requesterUnit() . ' submitted a new utilization request.',
            ['submission_id' => $submission->id]
        );

        return redirect()->route($this->submittedRedirectRoute())
            ->with('status', 'Facilities utilization request submitted to GSU.');
    }

    protected function handleIndexFacilities()
    {
        $user = Auth::user();

        return FormSubmission::with('requester')
            ->where('type', 'facilities_utilization')
            ->where('requester_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(10);
    }
}