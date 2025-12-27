<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExcusedAbsence extends Model
{
    protected $fillable = [
        'student_id',
        'type',
        'start_date',
        'end_date',
        'reason',
        'reason_details',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // أنواع الغياب
    const TYPE_SESSIONS = 'sessions';
    const TYPE_DAYCARE = 'daycare';

    // أسباب الغياب
    const REASON_ILLNESS = 'illness';
    const REASON_TRAVEL = 'travel';
    const REASON_FAMILY = 'family';
    const REASON_OTHER = 'other';

    public static function getTypes(): array
    {
        return [
            self::TYPE_SESSIONS => 'جلسات',
            self::TYPE_DAYCARE => 'رعاية نهارية',
        ];
    }

    public static function getReasons(): array
    {
        return [
            self::REASON_ILLNESS => 'مرض',
            self::REASON_TRAVEL => 'سفر',
            self::REASON_FAMILY => 'ظرف عائلي',
            self::REASON_OTHER => 'سبب آخر',
        ];
    }

    // العلاقات
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function studentSessions(): HasMany
    {
        return $this->hasMany(StudentSession::class);
    }

    public function daycareAttendances(): HasMany
    {
        return $this->hasMany(DaycareAttendance::class);
    }

    // Accessors
    public function getTypeTextAttribute(): string
    {
        return self::getTypes()[$this->type] ?? $this->type;
    }

    public function getReasonTextAttribute(): string
    {
        return self::getReasons()[$this->reason] ?? $this->reason;
    }

    public function getPeriodTextAttribute(): string
    {
        return $this->start_date->format('Y-m-d') . ' إلى ' . $this->end_date->format('Y-m-d');
    }

    public function getDaysCountAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    /**
     * هل الغياب نشط (التاريخ الحالي ضمن الفترة)؟
     */
    public function getIsActiveAttribute(): bool
    {
        $today = now()->startOfDay();
        return $today->between($this->start_date, $this->end_date);
    }

    /**
     * تطبيق الغياب بإذن على الجلسات أو الحضور
     */
    public function applyToRecords(): void
    {
        if ($this->type === self::TYPE_SESSIONS) {
            // تحديث جلسات الطالب في الفترة المحددة
            StudentSession::where('student_id', $this->student_id)
                ->where('status', 'absent')
                ->whereBetween('session_date', [$this->start_date, $this->end_date])
                ->update([
                    'is_excused' => true,
                    'excused_absence_id' => $this->id,
                ]);
        } else {
            // تحديث حضور الرعاية النهارية في الفترة المحددة
            DaycareAttendance::whereHas('subscription', function ($query) {
                    $query->where('student_id', $this->student_id);
                })
                ->where('status', 'absent')
                ->whereBetween('date', [$this->start_date, $this->end_date])
                ->update([
                    'is_excused' => true,
                    'excused_absence_id' => $this->id,
                ]);
        }
    }

    /**
     * إزالة تأثير الغياب بإذن
     */
    public function removeFromRecords(): void
    {
        if ($this->type === self::TYPE_SESSIONS) {
            StudentSession::where('excused_absence_id', $this->id)
                ->update([
                    'is_excused' => false,
                    'excused_absence_id' => null,
                ]);
        } else {
            DaycareAttendance::where('excused_absence_id', $this->id)
                ->update([
                    'is_excused' => false,
                    'excused_absence_id' => null,
                ]);
        }
    }
}
