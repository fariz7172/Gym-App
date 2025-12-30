<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'title',
        'description',
        'goal_type',
        'target_value',
        'current_value',
        'unit',
        'target_date',
        'status',
    ];

    protected $casts = [
        'target_value' => 'decimal:2',
        'current_value' => 'decimal:2',
        'target_date' => 'date',
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

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeAchieved($query)
    {
        return $query->where('status', 'achieved');
    }

    // Helpers
    public function getGoalTypeLabelAttribute()
    {
        return match($this->goal_type) {
            'weight_loss' => 'Penurunan Berat',
            'muscle_gain' => 'Peningkatan Massa Otot',
            'endurance' => 'Ketahanan',
            'strength' => 'Kekuatan',
            'flexibility' => 'Fleksibilitas',
            'body_fat' => 'Body Fat',
            default => $this->goal_type,
        };
    }

    public function getProgressPercentageAttribute()
    {
        if (!$this->target_value || $this->target_value == 0) return 0;

        // For goals like weight loss, reverse the calculation
        if (in_array($this->goal_type, ['weight_loss', 'body_fat'])) {
            $start = $this->current_value + $this->target_value; // Assuming target is the amount to lose
            $progress = (($start - $this->current_value) / $this->target_value) * 100;
        } else {
            $progress = ($this->current_value / $this->target_value) * 100;
        }

        return min(100, max(0, round($progress)));
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'in_progress' => 'Berlangsung',
            'achieved' => 'Tercapai',
            'abandoned' => 'Dibatalkan',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'in_progress' => 'blue',
            'achieved' => 'green',
            'abandoned' => 'gray',
            default => 'gray',
        };
    }

    public function getDaysRemainingAttribute()
    {
        if ($this->target_date) {
            return max(0, now()->diffInDays($this->target_date, false));
        }
        return null;
    }

    public function updateProgress($newValue): void
    {
        $this->current_value = $newValue;

        // Auto-mark as achieved if target reached
        if ($this->progress_percentage >= 100) {
            $this->status = 'achieved';
        }

        $this->save();
    }

    public function markAchieved(): void
    {
        $this->update(['status' => 'achieved']);
    }

    public function abandon(): void
    {
        $this->update(['status' => 'abandoned']);
    }
}
