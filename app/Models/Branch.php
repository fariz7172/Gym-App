<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'address',
        'phone',
        'email',
        'opening_time',
        'closing_time',
        'photo',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'opening_time' => 'datetime:H:i',
        'closing_time' => 'datetime:H:i',
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function trainers()
    {
        return $this->hasMany(Trainer::class);
    }

    public function gymClasses()
    {
        return $this->hasMany(GymClass::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Statistics
    public function getTotalMembersAttribute()
    {
        return $this->members()->count();
    }

    public function getActiveMembersAttribute()
    {
        return $this->members()->where('status', 'active')->count();
    }

    public function getTodayAttendanceAttribute()
    {
        return $this->attendances()->whereDate('check_in', today())->count();
    }
}
