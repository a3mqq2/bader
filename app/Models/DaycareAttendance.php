<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DaycareAttendance extends Model
{
    protected $fillable = [
        'subscription_id',
        'date',
        'status',
        'is_excused',
        'excused_absence_id',
        'notes',
        'updated_by',
    ];

    protected $casts = [
        'date' => 'date',
        'is_excused' => 'boolean',
    ];

    // العلاقات
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(DaycareSubscription::class, 'subscription_id');
    }

    // Accessors
    public function getStatusTextAttribute(): string
    {
        return match ($this->status) {
            'present' => 'حاضر',
            'absent' => 'غائب',
            'pending' => 'قيد الانتظار',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'present' => 'success',
            'absent' => 'danger',
            'pending' => 'warning',
            default => 'secondary',
        };
    }

    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            'present' => 'check-circle',
            'absent' => 'times-circle',
            'pending' => 'clock',
            default => 'question-circle',
        };
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->date->format('Y-m-d');
    }

    public function getDayNameAttribute(): string
    {
        $days = [
            'Sunday' => 'الأحد',
            'Monday' => 'الاثنين',
            'Tuesday' => 'الثلاثاء',
            'Wednesday' => 'الأربعاء',
            'Thursday' => 'الخميس',
            'Friday' => 'الجمعة',
            'Saturday' => 'السبت',
        ];
        return $days[$this->date->format('l')] ?? $this->date->format('l');
    }

    // العلاقة مع المستخدم الذي قام بالتحديث
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // العلاقة مع الغياب بإذن
    public function excusedAbsence(): BelongsTo
    {
        return $this->belongsTo(ExcusedAbsence::class);
    }
}
