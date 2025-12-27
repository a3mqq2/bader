<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class DaycareSubscription extends Model
{
    protected $fillable = [
        'student_id',
        'daycare_type_id',
        'supervisor_id',
        'start_date',
        'end_date',
        'price',
        'invoice_id',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'price' => 'decimal:2',
    ];

    // العلاقات
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function daycareType(): BelongsTo
    {
        return $this->belongsTo(DaycareType::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(DaycareAttendance::class, 'subscription_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    // Accessors
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2) . ' د.ل';
    }

    public function getStatusTextAttribute(): string
    {
        return match ($this->status) {
            'active' => 'نشط',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active' => 'success',
            'completed' => 'secondary',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }

    public function getDurationDaysAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function getPresentCountAttribute(): int
    {
        return $this->attendances()->where('status', 'present')->count();
    }

    public function getAbsentCountAttribute(): int
    {
        return $this->attendances()->where('status', 'absent')->count();
    }

    public function getPendingCountAttribute(): int
    {
        return $this->attendances()->where('status', 'pending')->count();
    }

    // توليد أيام الحضور تلقائياً
    public function generateAttendances(): void
    {
        $startDate = $this->start_date->copy();
        $endDate = $this->end_date->copy();

        while ($startDate <= $endDate) {
            // تخطي الجمعة والسبت (عطلة نهاية الأسبوع)
            if (!in_array($startDate->dayOfWeek, [Carbon::FRIDAY, Carbon::SATURDAY])) {
                DaycareAttendance::firstOrCreate([
                    'subscription_id' => $this->id,
                    'date' => $startDate->format('Y-m-d'),
                ], [
                    'status' => 'pending', // افتراضي قيد الانتظار
                ]);
            }
            $startDate->addDay();
        }
    }

    // علاقة مع الفاتورة
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
