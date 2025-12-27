<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollItem extends Model
{
    protected $fillable = [
        'payroll_id',
        'user_id',
        'base_salary',
        'bonus',
        'deduction',
        'net_salary',
        'work_days',
        'work_hours',
        'notes',
        'is_processed',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'bonus' => 'decimal:2',
        'deduction' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'work_hours' => 'decimal:2',
        'work_days' => 'integer',
        'is_processed' => 'boolean',
    ];

    // العلاقات
    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // حساب صافي الراتب تلقائياً
    public function calculateNetSalary(): float
    {
        return $this->base_salary + $this->bonus - $this->deduction;
    }

    // Boot method لحساب صافي الراتب تلقائياً
    protected static function booted()
    {
        static::saving(function ($item) {
            $item->net_salary = $item->base_salary + $item->bonus - $item->deduction;
        });
    }
}
