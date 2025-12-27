<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TherapySession extends Model
{
    protected $fillable = [
        'name',
        'price',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2) . ' د.ل';
    }

    public function getStatusTextAttribute(): string
    {
        return $this->is_active ? 'مفعّل' : 'معطّل';
    }

    public function getStatusColorAttribute(): string
    {
        return $this->is_active ? 'success' : 'danger';
    }
}
