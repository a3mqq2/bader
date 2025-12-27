<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_out',
        'status',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * الموظف
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * المسجل (المشرف)
     */
    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * حساب ساعات العمل
     */
    public function getWorkHoursAttribute()
    {
        if (!$this->check_in || !$this->check_out) {
            return null;
        }

        $checkIn = Carbon::parse($this->check_in);
        $checkOut = Carbon::parse($this->check_out);

        return $checkOut->diffInMinutes($checkIn) / 60;
    }

    /**
     * تنسيق ساعات العمل
     */
    public function getFormattedWorkHoursAttribute()
    {
        $hours = $this->work_hours;
        if (!$hours) {
            return '-';
        }

        $h = floor($hours);
        $m = round(($hours - $h) * 60);

        return "{$h} س {$m} د";
    }

    /**
     * نص الحالة
     */
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'present' => 'حاضر',
            'absent' => 'غائب',
            'late' => 'متأخر',
            'early_leave' => 'خروج مبكر',
            default => $this->status,
        };
    }

    /**
     * لون الحالة
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'present' => 'success',
            'absent' => 'danger',
            'late' => 'warning',
            'early_leave' => 'info',
            default => 'secondary',
        };
    }

    /**
     * Scope: حضور اليوم
     */
    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    /**
     * Scope: حسب التاريخ
     */
    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }
}
