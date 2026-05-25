<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',           // e.g. 'facilities_utilization', 'repair_maintenance'
        'requester_id',
        'requester_type', // 'college', 'org', 'admin'
        'requester_unit', // e.g. college_name or organization_name
        'status',         // 'pending', 'approved', 'disapproved', 'cancelled', 'booked'
        'payload',        // JSON string
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isFacilitiesUtilization(): bool
    {
        return $this->type === 'facilities_utilization';
    }

    public function markApproved(): void
    {
        $this->status = 'approved';
        $this->save();
    }

    public function markDisapproved(): void
    {
        $this->status = 'disapproved';
        $this->save();
    }

    public function markBooked(): void
    {
        $this->status = 'booked';
        $this->save();
    }

    // Backward-compat (if older code still references converted)
    public function markConverted(): void
    {
        $this->status = 'booked';
        $this->save();
    }


    public function markCancelled(): void
    {
        $this->status = 'cancelled';
        $this->save();
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }
}
 