<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_schedule_id',
        'member_id',
        'booking_date',
        'status',
    ];

    protected $casts = [
        'booking_date' => 'date',
    ];

    // Relationships
    public function schedule()
    {
        return $this->belongsTo(ClassSchedule::class, 'class_schedule_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('booking_date', '>=', today())
            ->where('status', 'booked');
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('booking_date', $date);
    }

    public function scopeByMember($query, $memberId)
    {
        return $query->where('member_id', $memberId);
    }

    // Helpers
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'booked' => 'Terdaftar',
            'attended' => 'Hadir',
            'cancelled' => 'Dibatalkan',
            'no_show' => 'Tidak Hadir',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'booked' => 'blue',
            'attended' => 'green',
            'cancelled' => 'gray',
            'no_show' => 'red',
            default => 'gray',
        };
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    public function markAttended(): void
    {
        $this->update(['status' => 'attended']);
    }

    public function markNoShow(): void
    {
        $this->update(['status' => 'no_show']);
    }
}
