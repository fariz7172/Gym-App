<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BodyMeasurement extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'measured_at',
        'weight',
        'height',
        'body_fat_percentage',
        'chest',
        'waist',
        'hips',
        'arms',
        'thighs',
        'notes',
        'photo_front',
        'photo_side',
        'photo_back',
    ];

    protected $casts = [
        'measured_at' => 'date',
        'weight' => 'decimal:2',
        'height' => 'decimal:2',
        'body_fat_percentage' => 'decimal:2',
        'chest' => 'decimal:2',
        'waist' => 'decimal:2',
        'hips' => 'decimal:2',
        'arms' => 'decimal:2',
        'thighs' => 'decimal:2',
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

    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('measured_at', 'desc')->limit($limit);
    }

    // Helpers
    public function getBmiAttribute()
    {
        if ($this->weight && $this->height) {
            $heightInMeters = $this->height / 100;
            return round($this->weight / ($heightInMeters * $heightInMeters), 1);
        }
        return null;
    }

    public function getBmiCategoryAttribute()
    {
        $bmi = $this->bmi;
        if (!$bmi) return null;

        return match(true) {
            $bmi < 18.5 => 'Kurus',
            $bmi < 25 => 'Normal',
            $bmi < 30 => 'Gemuk',
            default => 'Obesitas',
        };
    }

    public function getBmiColorAttribute()
    {
        $bmi = $this->bmi;
        if (!$bmi) return 'gray';

        return match(true) {
            $bmi < 18.5 => 'yellow',
            $bmi < 25 => 'green',
            $bmi < 30 => 'orange',
            default => 'red',
        };
    }

    // Get comparison with previous measurement
    public function getComparisonWithPrevious()
    {
        $previous = self::where('member_id', $this->member_id)
            ->where('measured_at', '<', $this->measured_at)
            ->orderBy('measured_at', 'desc')
            ->first();

        if (!$previous) return null;

        return [
            'weight' => $this->weight - $previous->weight,
            'body_fat_percentage' => $this->body_fat_percentage - $previous->body_fat_percentage,
            'waist' => $this->waist - $previous->waist,
        ];
    }
}
