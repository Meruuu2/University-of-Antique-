<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Facility;
use App\Models\FormSubmission;
use Carbon\Carbon;

class BookingService
{
    public function __construct(
        protected NotificationService $notifications
    ) {}

    public function hasOverlap(int $facilityId, Carbon $startTime, Carbon $endTime): bool
    {
        // The 'overlapping' scope expects an array of facility IDs.
        return Booking::overlapping([$facilityId], $startTime, $endTime)->exists();
    }

    public function createFromSubmission(FormSubmission $submission): Booking|string
    {
        $payload = $submission->payload ?? null;
        if (!$payload) {
            return 'Submission payload is missing.';
        }

        $facilityId   = $payload['facility_id'] ?? null;
        $dateActivity = $payload['date_activity'] ?? null;
        $timeRange    = $payload['time_range'] ?? null;

        if (!$facilityId || !$dateActivity || !$timeRange || empty($timeRange['start']) || empty($timeRange['end'])) {
            return 'Incomplete date/time or facility information in the submission.';
        }

        if (Carbon::parse($dateActivity)->lessThan(now()->addDays(7)->startOfDay())) {
            return 'Date of activity is less than 7 days away. Please coordinate a manual exception if truly needed.';
        }

        $facility = Facility::find($facilityId);
        if (!$facility) {
            return 'Facility not found.';
        }

        if (!$facility->isAvailable()) {
            return "Facility is currently {$facility->availability_status} and cannot be booked.";
        }

        $startDateTime = Carbon::parse($dateActivity . ' ' . $timeRange['start']);
        $endDateTime   = Carbon::parse($dateActivity . ' ' . $timeRange['end']);

        if ($endDateTime->lessThanOrEqualTo($startDateTime)) {
            return 'End time must be after start time.';
        }

        if ($this->hasOverlap($facility->id, $startDateTime, $endDateTime)) {
            return 'This facility is already booked at the requested time.';
        }

        $requester = $submission->requester;
        if (!$requester) {
            return 'Requester account not found.';
        }

        $booking = Booking::create([
            'requester_id'       => $requester->id,
            'start_time'         => $startDateTime,
            'end_time'           => $endDateTime,
            'requester_type'     => $submission->requester_type,
            'requester_unit'     => $submission->requester_unit,
            'status'             => 'booked',
            'request_method'     => 'online_form',
            'purpose'            => $payload['purpose'] ?? null,
            'additional_details' => [
                'form_submission_id' => $submission->id,
                'equipment'          => $payload['equipment'] ?? [],
            ],
            'requested_at'       => $payload['date_request'] ?? now(),
            'approved_at'        => now(),
            'booking_code'       => 'BKG-' . now()->format('YmdHis') . '-' . $facility->id,
        ]);

        $booking->facilities()->attach($facility->id);

        $submission->markBooked();


        $this->notifications->notifyBookingCreated(
            $submission->requester_id,
            $submission->id,
            $booking->id,
            $booking->booking_code
        );

        return $booking;
    }

    public function createDirectBooking(array $data, int $adminId): Booking
    {
        $startDateTime = Carbon::parse($data['date_activity'] . ' ' . $data['start_time']);
        $endDateTime   = Carbon::parse($data['date_activity'] . ' ' . $data['end_time']);
        $facilityIds   = $data['facility_ids'];

        // 1. Find and update conflicting bookings
        $conflictingBookings = Booking::overlapping($facilityIds, $startDateTime, $endDateTime)->get();

        foreach ($conflictingBookings as $conflictingBooking) {
            $conflictingBooking->status = 'pending';
            $conflictingBooking->save();

            // 2. Notify the original requester that their booking was preempted
            if ($conflictingBooking->requester) {
                $this->notifications->notifyBookingPreempted(
                    $conflictingBooking->requester_id,
                    $conflictingBooking->id,
                    $conflictingBooking->booking_code
                );
            }
        }

        // 3. Create the new high-priority booking
        $booking = Booking::create([
            'requester_id'       => $adminId,
            'start_time'         => $startDateTime,
            'end_time'           => $endDateTime,
            'requester_type'     => 'admin',
            'requester_unit'     => 'GSU Office',
            'status'             => 'booked',
            'request_method'     => 'direct_admin',
            'purpose'            => $data['purpose'],
            'additional_details' => [
                'equipment' => $this->buildEquipmentArray($data),
                'requester_name' => $data['requester_name'],
            ],
            'requested_at'       => now(),
            'approved_at'        => now(),
            'booking_code'       => 'BKG-' . now()->format('YmdHis') . '-' . head($facilityIds),
        ]);

        $booking->facilities()->sync($facilityIds);

        // 4. Create a corresponding FormSubmission for record-keeping
        FormSubmission::create([
            'type'           => 'facilities_utilization',
            'requester_id'   => $adminId,
            'requester_type' => 'admin',
            'requester_unit' => 'GSU Office',
            'status'         => 'booked',
            'payload'        => [
                'control_no'      => 'BKG-' . now()->format('YmdHis') . '-' . head($facilityIds),
                'date_request'    => now()->toDateString(),
                'requester_name'  => $data['requester_name'],
                'date_activity'   => $data['date_activity'],
                'time_range'      => [
                    'start' => $data['start_time'],
                    'end'   => $data['end_time'],
                ],
                'facility_id'     => head($facilityIds), // For simplicity, log the first facility
                'purpose'         => $data['purpose'],
                'equipment'       => $this->buildEquipmentArray($data),
                'approved_datetime' => now()->format('M d, Y h:i A'),
                'is_direct_booking' => true,
            ],
        ]);

        return $booking;
    }

    public function buildEquipmentArray(array $data, array $existingEquipment = []): array
    {
        return [
            'monobloc_chair' => (int) ($data['qty_monobloc'] ?? ($existingEquipment['monobloc_chair'] ?? 0)),
            'table'          => (int) ($data['qty_table'] ?? ($existingEquipment['table'] ?? 0)),
            'electric_fan'   => (int) ($data['qty_fan'] ?? ($existingEquipment['electric_fan'] ?? 0)),
            'rostrum'        => (int) ($data['qty_rostrum'] ?? ($existingEquipment['rostrum'] ?? 0)),
            'flag'           => (int) ($data['qty_flag'] ?? ($existingEquipment['flag'] ?? 0)),
            'sound'          => (int) ($data['qty_sound'] ?? ($existingEquipment['sound'] ?? 0)),
            'led'            => (int) ($data['qty_led'] ?? ($existingEquipment['led'] ?? 0)),
        ];
    }
}