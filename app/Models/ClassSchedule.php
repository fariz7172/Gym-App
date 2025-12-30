<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'gym_class_id',
        'trainer_id',
        'day_of_week',
        'start_time',
        'end_time',
        'room',
        'is_active',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function gymClass()
    {
        return $this->belongsTo(GymClass::class);
    }

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    public function bookings()
    {
        return $this->hasMany(ClassBooking::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForDay($query, $day)
    {
        return $query->where('day_of_week', strtolower($day));
    }

    public function scopeToday($query)
    {
        return $query->where('day_of_week', strtolower(now()->format('l')));
    }

    // Helpers
    public function getDayLabelAttribute()
    {
        return match($this->day_of_week) {
            'monday' => 'Senin',
            'tuesday' => 'Selasa',
            'wednesday' => 'Rabu',
            'thursday' => 'Kamis',
            'friday' => 'Jumat',
            'saturday' => 'Sabtu',
            'sunday' => 'Minggu',
            default => $this->day_of_week,
        };
    }

    public function getTimeRangeAttribute()
    {
        $start = is_string($this->start_time) ? $this->start_time : $this->start_time->format('H:i');
        $end = is_string($this->end_time) ? $this->end_time : $this->end_time->format('H:i');
        return "{$start} - {$end}";
    }

    public function getBookingsCountForDate($date)
    {
        return $this->bookings()
            ->where('booking_date', $date)
            ->where('status', '!=', 'cancelled')
            ->count();
    }

    public function getAvailableSlotsForDate($date)
    {
        $booked = $this->getBookingsCountForDate($date);
        return max(0, $this->gymClass->max_capacity - $booked);
    }

    public function isFull($date): bool
    {
        return $this->getAvailableSlotsForDate($date) <= 0;
    }
}
