<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $fillable = [
        'year',
        'month',
        'status',
        'treasury_id',
        'created_by',
        'executed_by',
        'executed_at',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'executed_at' => 'datetime',
    ];

    // العلاقات
    public function items()
    {
        return $this->hasMany(PayrollItem::class);
    }

    public function treasury()
    {
        return $this->belongsTo(Treasury::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function executor()
    {
        return $this->belongsTo(User::class, 'executed_by');
    }

    // Accessors
    public function getMonthNameAttribute(): string
    {
        $months = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
        ];
        return $months[$this->month] ?? '';
    }

    public function getPeriodTextAttribute(): string
    {
        return $this->month_name . ' ' . $this->year;
    }

    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'draft' => 'مسودة',
            'executed' => 'تم التنفيذ',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'warning',
            'executed' => 'success',
            default => 'secondary',
        };
    }

    public function getTotalSalariesAttribute(): float
    {
        return $this->items->sum('base_salary');
    }

    public function getTotalBonusesAttribute(): float
    {
        return $this->items->sum('bonus');
    }

    public function getTotalDeductionsAttribute(): float
    {
        return $this->items->sum('deduction');
    }

    public function getTotalNetAttribute(): float
    {
        return $this->items->sum('net_salary');
    }
}
