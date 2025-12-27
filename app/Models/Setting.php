<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
    ];

    /**
     * الحصول على قيمة إعداد معين
     */
    public static function get(string $key, $default = null)
    {
        $setting = Cache::remember("setting.{$key}", 3600, function () use ($key) {
            return self::where('key', $key)->first();
        });

        if (!$setting) {
            return $default;
        }

        return self::castValue($setting->value, $setting->type);
    }

    /**
     * تعيين قيمة إعداد
     */
    public static function set(string $key, $value): bool
    {
        $setting = self::where('key', $key)->first();

        if (!$setting) {
            return false;
        }

        $setting->update(['value' => $value]);
        Cache::forget("setting.{$key}");

        return true;
    }

    /**
     * تحويل القيمة حسب النوع
     */
    protected static function castValue($value, $type)
    {
        return match ($type) {
            'integer' => (int) $value,
            'decimal' => (float) $value,
            'boolean' => (bool) $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * الحصول على إعدادات مجموعة معينة
     */
    public static function getGroup(string $group): array
    {
        $settings = self::where('group', $group)->get();

        return $settings->mapWithKeys(function ($setting) {
            return [$setting->key => self::castValue($setting->value, $setting->type)];
        })->toArray();
    }

    /**
     * تحديث مجموعة إعدادات
     */
    public static function updateGroup(string $group, array $values): void
    {
        foreach ($values as $key => $value) {
            self::set($key, $value);
        }
    }

    /**
     * الحصول على وقت بداية الدوام
     */
    public static function getWorkStartTime(): string
    {
        return self::get('work_start_time', '08:30');
    }

    /**
     * الحصول على وقت نهاية الدوام
     */
    public static function getWorkEndTime(): string
    {
        return self::get('work_end_time', '12:30');
    }

    /**
     * الحصول على قيمة حافز الجلسة
     */
    public static function getSessionIncentive(): float
    {
        return (float) self::get('session_incentive_amount', 10.00);
    }

    /**
     * الحصول على قيمة حافز الرعاية النهارية
     */
    public static function getDaycareIncentive(): float
    {
        return (float) self::get('daycare_incentive_amount', 5.00);
    }

    /**
     * هل نظام الحوافز مفعل؟
     */
    public static function isIncentivesEnabled(): bool
    {
        return (bool) self::get('incentives_enabled', true);
    }
}
