<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'member_code',
        'name',
        'email',
        'phone',
        'gender',
        'date_of_birth',
        'address',
        'photo',
        'emergency_contact',
        'health_notes',
        'qr_code',
        'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($member) {
            if (empty($member->member_code)) {
                $member->member_code = self::generateMemberCode($member->branch_id);
            }
        });
    }

    // Generate unique member code
    public static function generateMemberCode($branchId): string
    {
        $branch = Branch::find($branchId);
        $prefix = $branch ? strtoupper(substr($branch->code, 0, 3)) : 'GYM';
        $date = now()->format('ym');
        $lastMember = self::where('member_code', 'LIKE', "{$prefix}{$date}%")
            ->orderBy('member_code', 'desc')
            ->first();

        if ($lastMember) {
            $lastNumber = (int) substr($lastMember->member_code, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "{$prefix}{$date}{$newNumber}";
    }

    // Relationships
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }

    public function activeMembership()
    {
        return $this->hasOne(Membership::class)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->latest();
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function classBookings()
    {
        return $this->hasMany(ClassBooking::class);
    }

    public function ptSessions()
    {
        return $this->hasMany(PtSession::class);
    }

    public function bodyMeasurements()
    {
        return $this->hasMany(BodyMeasurement::class);
    }

    public function workoutLogs()
    {
        return $this->hasMany(WorkoutLog::class);
    }

    public function goals()
    {
        return $this->hasMany(Goal::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    // Helpers
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->activeMembership !== null;
    }

    public function getAgeAttribute()
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    public function getMembershipExpiryAttribute()
    {
        return $this->activeMembership?->end_date;
    }

    public function getDaysUntilExpiryAttribute()
    {
        if ($this->membership_expiry) {
            return now()->diffInDays($this->membership_expiry, false);
        }
        return null;
    }

    // Check if member is currently checked in
    public function isCheckedIn(): bool
    {
        return $this->attendances()
            ->whereDate('check_in', today())
            ->whereNull('check_out')
            ->exists();
    }

    public function getLatestAttendance()
    {
        return $this->attendances()
            ->whereNull('check_out')
            ->latest('check_in')
            ->first();
    }
}
