<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'requester_id',
        'start_time',
        'end_time',
        'requester_type',
        'requester_unit',
        'status',
        'request_method',
        'purpose',
        'additional_details',
        'requested_at',
        'approved_at',
        'booking_code',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'additional_details' => 'array',
    ];

    public function markRescheduled(array $equipment, string $reason, string $rescheduledBy): void
    {
        $details = $this->additional_details ?? [];

        $details['equipment'] = $equipment;
        $details['reschedule_note'] = $reason;
        $details['rescheduled_at'] = now()->toDateTimeString();
        $details['rescheduled_by'] = $rescheduledBy;

        $this->status = 'rescheduled';
        $this->additional_details = $details;
    }

    public function markCancelled(string $reason, string $cancelledBy): void
    {
        $details = $this->additional_details ?? [];

        $details['cancel_reason'] = $reason;
        $details['cancelled_by'] = $cancelledBy;
        $details['cancelled_at'] = now()->toDateTimeString();

        $this->status = 'cancelled';
        $this->additional_details = $details;
    }

    public function scopeOverlapping($query, array $facilityIds, $startTime, $endTime)
    {
        return $query->whereHas('facilities', function ($q) use ($facilityIds) {
            $q->whereIn('facility_id', $facilityIds);
        })
            ->where('status', 'approved')
            ->where(function ($q) use ($startTime, $endTime) {
                $q->whereBetween('start_time', [$startTime, $endTime])
                  ->orWhereBetween('end_time', [$startTime, $endTime])
                  ->orWhere(function ($q2) use ($startTime, $endTime) {
                      $q2->where('start_time', '<=', $startTime)
                         ->where('end_time', '>=', $endTime);
                  });
            });
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function facilities()
    {
        return $this->belongsToMany(Facility::class, 'booking_facility');
    }

    public function maintenanceTickets()
    {
        return $this->hasMany(MaintenanceTicket::class);
    }
}