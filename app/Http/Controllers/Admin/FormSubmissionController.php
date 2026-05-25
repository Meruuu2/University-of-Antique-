<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FormSubmission;
use App\Models\Facility;
use App\Models\User;

use App\Services\BookingService;
use App\Services\NotificationService;
use Illuminate\Http\Request;


class FormSubmissionController extends Controller
{ 
    public function __construct(
        protected BookingService $bookingService,
        protected NotificationService $notifications
    ) {}

    /**
     * List facilities utilization form submissions.
     */
    public function index()
    {
        $submissions = FormSubmission::with('requester')
            ->where('type', 'facilities_utilization')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.forms.facilities_index', compact('submissions'));
    }

    /**
     * Show a single submission.
     */
    public function show(FormSubmission $submission)
    {
        if ($submission->type !== 'facilities_utilization') {
            abort(404);
        }

        $payload = $submission->payload ?? [];
        $facility = null;

        if (!empty($payload['facility_id'])) {
            $facility = Facility::find($payload['facility_id']);
        }

        return view('admin.forms.facilities_show', compact('submission', 'payload', 'facility'));
    }

    /**
     * Approve a submission (GSU side).
     */
    public function approve(FormSubmission $submission)
    {
        if ($submission->type !== 'facilities_utilization') {
            abort(404);
        }

        $submission->status = 'approved';
        $payload = $submission->payload ?? [];

        // Generate control number if it doesn't exist.
        if (empty($payload['control_no'])) {
            $timestamp = now()->format('YmdHis');
            $randomDigit = rand(0, 9);
            $payload['control_no'] = "BKG-{$timestamp}-{$randomDigit}";
        }

        $payload['approved_datetime'] = now()->format('M d, Y h:i A');
        $submission->payload = $payload;
        $submission->save();

        if ($submission->requester) {
            $this->notifications->notifyFormApproved($submission->requester_id, $submission->id);
        }


        return redirect()->route('admin.forms.facilities.index')
            ->with('status', 'Request approved. Requester must proceed to GSU office to sign and finalize the form.');
    }

    /**
     * Disapprove a submission.
     */
    public function disapprove(FormSubmission $submission)
    {
        if ($submission->type !== 'facilities_utilization') {
            abort(404);
        }

        $submission->status = 'disapproved';
        $submission->save();

        if ($submission->requester) {
            $this->notifications->notifyFormDisapproved($submission->requester_id, $submission->id);
        }


        return redirect()->route('admin.forms.facilities.index')
            ->with('status', 'Request disapproved.');
    }

    /**
     * Convert an approved facilities form into a Booking.
     *
     * This enforces:
     * - Only approved submissions are allowed.
     * - Facility must not be unavailable/maintenance.
     * - No overlapping approved bookings for the same facility & time.
     */
    public function setBooking(FormSubmission $submission)
    {
        if ($submission->type !== 'facilities_utilization') {
            abort(404);
        }

        if ($submission->status !== 'approved') {
            return back()->withErrors(['status' => 'Only approved requests can be converted to bookings.']);
        }

        $result = $this->bookingService->createFromSubmission($submission);

        if (is_string($result)) {
            return back()->withErrors(['status' => $result]);
        }

        $booking = $result;

        return redirect()->route('admin.forms.facilities.index')
            ->with('status', "Booking created (Code: {$booking->booking_code}) and request marked as booked.");
    }

}