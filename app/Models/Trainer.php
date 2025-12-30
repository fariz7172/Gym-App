<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trainer extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'name',
        'email',
        'phone',
        'gender',
        'specialization',
        'bio',
        'photo',
        'hourly_rate',
        'is_active',
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function classSchedules()
    {
        return $this->hasMany(ClassSchedule::class);
    }

    public function ptSessions()
    {
        return $this->hasMany(PtSession::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    // Helpers
    public function getFormattedRateAttribute()
    {
        return 'Rp ' . number_format($this->hourly_rate, 0, ',', '.') . '/jam';
    }

    public function getUpcomingSessionsAttribute()
    {
        return $this->ptSessions()
            ->where('session_date', '>=', today())
            ->where('status', 'scheduled')
            ->count();
    }

    public function getTotalSessionsThisMonthAttribute()
    {
        return $this->ptSessions()
            ->whereMonth('session_date', now()->month)
            ->whereYear('session_date', now()->year)
            ->count();
    }

    // Check availability
    public function isAvailable($date, $startTime, $endTime): bool
    {
        return !$this->ptSessions()
            ->where('session_date', $date)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime]);
            })
            ->exists();
    }
}
