<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentSession extends Model
{
    protected $fillable = [
        'session_package_id',
        'student_id',
        'specialist_id',
        'session_date',
        'session_time',
        'duration',
        'status',
        'is_excused',
        'excused_absence_id',
        'notes',
    ];

    protected $casts = [
        'session_date' => 'date',
        'is_excused' => 'boolean',
    ];

    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_COMPLETED = 'completed';
    const STATUS_ABSENT = 'absent';
    const STATUS_POSTPONED = 'postponed';
    const STATUS_CANCELLED = 'cancelled';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_SCHEDULED => 'مجدولة',
            self::STATUS_COMPLETED => 'تمت',
            self::STATUS_ABSENT => 'غائب',
            self::STATUS_POSTPONED => 'مؤجلة',
            self::STATUS_CANCELLED => 'ملغاة',
        ];
    }

    public static function getStatusColors(): array
    {
        return [
            self::STATUS_SCHEDULED => 'primary',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_ABSENT => 'danger',
            self::STATUS_POSTPONED => 'warning',
            self::STATUS_CANCELLED => 'secondary',
        ];
    }

    public function package()
    {
        return $this->belongsTo(SessionPackage::class, 'session_package_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function specialist()
    {
        return $this->belongsTo(User::class, 'specialist_id');
    }

    public function excusedAbsence()
    {
        return $this->belongsTo(ExcusedAbsence::class);
    }

    public function getStatusTextAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? 'غير محدد';
    }

    public function getStatusColorAttribute(): string
    {
        return self::getStatusColors()[$this->status] ?? 'secondary';
    }

    public function getFormattedTimeAttribute(): string
    {
        return date('h:i A', strtotime($this->session_time));
    }

    public function getDayNameAttribute(): string
    {
        $dayOfWeek = strtolower($this->session_date->format('l'));
        return SessionPackage::$dayNames[$dayOfWeek] ?? $dayOfWeek;
    }
}
