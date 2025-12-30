<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'duration_days',
        'price',
        'description',
        'features',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helpers
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getDurationTextAttribute()
    {
        if ($this->duration_days >= 365) {
            $years = floor($this->duration_days / 365);
            return $years . ' Tahun';
        } elseif ($this->duration_days >= 30) {
            $months = floor($this->duration_days / 30);
            return $months . ' Bulan';
        }
        return $this->duration_days . ' Hari';
    }
}
