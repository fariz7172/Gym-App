<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkoutLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'workout_date',
        'exercises',
        'duration_minutes',
        'calories_burned',
        'notes',
    ];

    protected $casts = [
        'workout_date' => 'date',
        'exercises' => 'array',
    ];

    // Relationships
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    // Scopes
    public function scopeByMember($query, $memberId)
    {
        return $query->where('member_id', $memberId);
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('workout_date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('workout_date', now()->month)
            ->whereYear('workout_date', now()->year);
    }

    // Helpers
    public function getDurationTextAttribute()
    {
        if ($this->duration_minutes) {
            $hours = floor($this->duration_minutes / 60);
            $minutes = $this->duration_minutes % 60;
            return $hours > 0 ? "{$hours}j {$minutes}m" : "{$minutes} menit";
        }
        return '-';
    }

    public function getExerciseCountAttribute()
    {
        return is_array($this->exercises) ? count($this->exercises) : 0;
    }

    public function getTotalSetsAttribute()
    {
        if (!is_array($this->exercises)) return 0;
        return array_sum(array_column($this->exercises, 'sets'));
    }

    public function getTotalRepsAttribute()
    {
        if (!is_array($this->exercises)) return 0;
        return array_sum(array_map(function ($exercise) {
            return ($exercise['sets'] ?? 0) * ($exercise['reps'] ?? 0);
        }, $this->exercises));
    }

    // Static helpers
    public static function getWeeklyStats($memberId)
    {
        $logs = self::byMember($memberId)->thisWeek()->get();

        return [
            'total_workouts' => $logs->count(),
            'total_duration' => $logs->sum('duration_minutes'),
            'total_calories' => $logs->sum('calories_burned'),
            'avg_duration' => $logs->avg('duration_minutes'),
        ];
    }
}
