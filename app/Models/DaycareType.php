<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DaycareType extends Model
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

    /**
     * Scope للأنواع المفعّلة فقط
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * الحصول على السعر بالتنسيق
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2) . ' د.ل';
    }

    /**
     * الحصول على نص الحالة
     */
    public function getStatusTextAttribute(): string
    {
        return $this->is_active ? 'مفعّل' : 'غير مفعّل';
    }

    /**
     * الحصول على لون الحالة
     */
    public function getStatusColorAttribute(): string
    {
        return $this->is_active ? 'success' : 'secondary';
    }
}
