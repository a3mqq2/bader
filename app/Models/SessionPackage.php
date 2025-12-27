<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionPackage extends Model
{
    protected $fillable = [
        'student_id',
        'therapy_session_id',
        'specialist_id',
        'start_date',
        'end_date',
        'session_time',
        'session_duration',
        'days',
        'total_price',
        'sessions_count',
        'notes',
        'created_by',
        'invoice_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'days' => 'array',
        'total_price' => 'decimal:2',
    ];

    public static $dayNames = [
        'saturday' => 'السبت',
        'sunday' => 'الأحد',
        'monday' => 'الإثنين',
        'tuesday' => 'الثلاثاء',
        'wednesday' => 'الأربعاء',
        'thursday' => 'الخميس',
        'friday' => 'الجمعة',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function therapySession()
    {
        return $this->belongsTo(TherapySession::class);
    }

    public function specialist()
    {
        return $this->belongsTo(User::class, 'specialist_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sessions()
    {
        return $this->hasMany(StudentSession::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function getDaysTextAttribute(): string
    {
        if (empty($this->days)) return '-';

        $names = [];
        foreach ($this->days as $day) {
            $names[] = self::$dayNames[$day] ?? $day;
        }
        return implode(', ', $names);
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->total_price, 2) . ' د.ل';
    }

    public function getFormattedTimeAttribute(): string
    {
        return date('h:i A', strtotime($this->session_time));
    }
}
