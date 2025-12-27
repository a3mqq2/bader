<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'description',
        'model_type',
        'model_id',
        'action',
        'properties',
        'ip_address',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    // العلاقات
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // تسجيل نشاط جديد
    public static function log(string $description, $model = null, string $action = null, array $properties = []): self
    {
        return static::create([
            'user_id' => auth()->id(),
            'description' => $description,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->id,
            'action' => $action,
            'properties' => $properties ?: null,
            'ip_address' => request()->ip(),
        ]);
    }

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // اسم الموديل المختصر
    public function getModelNameAttribute(): ?string
    {
        if (!$this->model_type) {
            return null;
        }

        $names = [
            'App\Models\Student' => 'طالب',
            'App\Models\User' => 'مستخدم',
            'App\Models\Invoice' => 'فاتورة',
            'App\Models\Payment' => 'دفعة',
            'App\Models\Session' => 'جلسة',
            'App\Models\DaycareSubscription' => 'اشتراك رعاية',
            'App\Models\DaycareAttendance' => 'حضور رعاية',
            'App\Models\Assessment' => 'مقياس',
            'App\Models\TherapySession' => 'نوع جلسة',
            'App\Models\DaycareType' => 'نوع رعاية',
        ];

        return $names[$this->model_type] ?? class_basename($this->model_type);
    }
}
