<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PtSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'trainer_id',
        'branch_id',
        'session_date',
        'start_time',
        'end_time',
        'notes',
        'status',
    ];

    protected $casts = [
        'session_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    // Relationships
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // Scopes
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('session_date', '>=', today())
            ->where('status', 'scheduled');
    }

    public function scopeByTrainer($query, $trainerId)
    {
        return $query->where('trainer_id', $trainerId);
    }

    public function scopeByMember($query, $memberId)
    {
        return $query->where('member_id', $memberId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('session_date', $date);
    }

    // Helpers
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'scheduled' => 'Terjadwal',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            'no_show' => 'Tidak Hadir',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'scheduled' => 'blue',
            'completed' => 'green',
            'cancelled' => 'gray',
            'no_show' => 'red',
            default => 'gray',
        };
    }

    public function getTimeRangeAttribute()
    {
        $start = is_string($this->start_time) ? $this->start_time : $this->start_time->format('H:i');
        $end = is_string($this->end_time) ? $this->end_time : $this->end_time->format('H:i');
        return "{$start} - {$end}";
    }

    public function getDurationMinutesAttribute()
    {
        $start = is_string($this->start_time) ? \Carbon\Carbon::parse($this->start_time) : $this->start_time;
        $end = is_string($this->end_time) ? \Carbon\Carbon::parse($this->end_time) : $this->end_time;
        return $start->diffInMinutes($end);
    }

    public function complete(): void
    {
        $this->update(['status' => 'completed']);
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }
}
