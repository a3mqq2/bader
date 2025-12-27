<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'for_system',
        'is_active',
    ];

    protected $casts = [
        'for_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * الحركات المالية
     */
    public function transactions()
    {
        return $this->hasMany(FinancialTransaction::class, 'category_id');
    }

    /**
     * Scope للتصنيفات النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope للتصنيفات المتاحة للمستخدمين
     */
    public function scopeForHumans($query)
    {
        return $query->where('for_system', false);
    }

    /**
     * Scope للتصنيفات النظامية
     */
    public function scopeForSystem($query)
    {
        return $query->where('for_system', true);
    }

    /**
     * نص النوع
     */
    public function getTypeTextAttribute()
    {
        return match($this->type) {
            'income' => 'إيراد',
            'expense' => 'مصروف',
            default => $this->type,
        };
    }

    /**
     * لون النوع
     */
    public function getTypeColorAttribute()
    {
        return match($this->type) {
            'income' => 'success',
            'expense' => 'danger',
            default => 'secondary',
        };
    }
}
