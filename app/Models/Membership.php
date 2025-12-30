<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'membership_plan_id',
        'start_date',
        'end_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Relationships
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function plan()
    {
        return $this->belongsTo(MembershipPlan::class, 'membership_plan_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('end_date', '>=', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', now())
            ->orWhere('status', 'expired');
    }

    public function scopeExpiringIn($query, $days)
    {
        return $query->where('status', 'active')
            ->whereBetween('end_date', [now(), now()->addDays($days)]);
    }

    // Helpers
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->end_date >= now();
    }

    public function getDaysRemainingAttribute()
    {
        if ($this->end_date) {
            return max(0, now()->diffInDays($this->end_date, false));
        }
        return 0;
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->start_date && $this->end_date) {
            $total = $this->start_date->diffInDays($this->end_date);
            $elapsed = $this->start_date->diffInDays(now());
            return min(100, round(($elapsed / $total) * 100));
        }
        return 0;
    }

    // Auto-update status
    public function checkAndUpdateStatus(): void
    {
        if ($this->end_date < now() && $this->status === 'active') {
            $this->update(['status' => 'expired']);
            $this->member->update(['status' => 'expired']);
        }
    }
}
