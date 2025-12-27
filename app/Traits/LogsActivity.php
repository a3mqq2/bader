<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    protected static function bootLogsActivity(): void
    {
        // تسجيل عند الإنشاء
        static::created(function ($model) {
            if (method_exists($model, 'getActivityLogDescription')) {
                $description = $model->getActivityLogDescription('created');
            } else {
                $description = 'تم إنشاء ' . static::getModelLabel();
            }

            ActivityLog::log($description, $model, 'create');
        });

        // تسجيل عند التحديث
        static::updated(function ($model) {
            if (method_exists($model, 'getActivityLogDescription')) {
                $description = $model->getActivityLogDescription('updated');
            } else {
                $description = 'تم تحديث ' . static::getModelLabel();
            }

            ActivityLog::log($description, $model, 'update', [
                'changes' => $model->getChanges(),
            ]);
        });

        // تسجيل عند الحذف
        static::deleted(function ($model) {
            if (method_exists($model, 'getActivityLogDescription')) {
                $description = $model->getActivityLogDescription('deleted');
            } else {
                $description = 'تم حذف ' . static::getModelLabel();
            }

            ActivityLog::log($description, $model, 'delete');
        });
    }

    // اسم الموديل للعرض
    protected static function getModelLabel(): string
    {
        return property_exists(static::class, 'activityLogLabel')
            ? static::$activityLogLabel
            : class_basename(static::class);
    }

    // جلب سجلات النشاط لهذا الموديل
    public function activityLogs()
    {
        return ActivityLog::where('model_type', get_class($this))
            ->where('model_id', $this->id)
            ->latest()
            ->get();
    }
}
