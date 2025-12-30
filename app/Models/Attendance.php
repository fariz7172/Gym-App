<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'branch_id',
        'check_in',
        'check_out',
        'duration_minutes',
    ];

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
    ];

    // Relationships
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('check_in', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('check_in', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('check_in', now()->month)
            ->whereYear('check_in', now()->year);
    }

    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeCheckedIn($query)
    {
        return $query->whereNull('check_out');
    }

    // Helpers
    public function checkOut(): void
    {
        $this->check_out = now();
        $this->duration_minutes = $this->check_in->diffInMinutes($this->check_out);
        $this->save();
    }

    public function getDurationTextAttribute()
    {
        if ($this->duration_minutes) {
            $hours = floor($this->duration_minutes / 60);
            $minutes = $this->duration_minutes % 60;
            return $hours > 0 ? "{$hours}j {$minutes}m" : "{$minutes}m";
        }
        return '-';
    }

    public function isCheckedIn(): bool
    {
        return $this->check_out === null;
    }

    // Static helpers
    public static function checkInMember(Member $member, $branchId = null): self
    {
        return self::create([
            'member_id' => $member->id,
            'branch_id' => $branchId ?? $member->branch_id,
            'check_in' => now(),
        ]);
    }
}
