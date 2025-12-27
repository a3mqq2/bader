<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'code',
        'name',
        'birth_date',
        'gender',
        'guardian_name',
        'phone',
        'phone_alt',
        'address',
        'notes',
        'status',
        'created_by',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    // توليد كود الطالب تلقائياً
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($student) {
            if (empty($student->code)) {
                $student->code = self::generateCode();
            }
        });
    }

    // توليد كود فريد للطالب
    public static function generateCode(): string
    {
        $year = date('Y');
        $lastStudent = self::where('code', 'like', $year . '%')
            ->orderByRaw('CAST(code AS UNSIGNED) DESC')
            ->first();

        if ($lastStudent) {
            $lastNumber = (int) substr($lastStudent->code, 4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $year . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    // حساب العمر
    public function getAgeAttribute(): string
    {
        $birthDate = Carbon::parse($this->birth_date);
        $now = Carbon::now();

        $diff = $birthDate->diff($now);
        $years = $diff->y;
        $months = $diff->m;

        if ($years > 0) {
            return $years . ' سنة' . ($months > 0 ? ' و ' . $months . ' شهر' : '');
        }
        return $months . ' شهر';
    }

    // علاقة مع المستخدم الذي أنشأ الطالب
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // علاقة مع دراسات الحالة
    public function cases()
    {
        return $this->hasMany(StudentCase::class);
    }

    // علاقة مع الفواتير
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    // علاقة مع باقات الجلسات
    public function sessionPackages()
    {
        return $this->hasMany(SessionPackage::class);
    }

    // علاقة مع الجلسات الفردية
    public function sessions()
    {
        return $this->hasMany(StudentSession::class);
    }

    // علاقة مع اشتراكات الرعاية النهارية
    public function daycareSubscriptions()
    {
        return $this->hasMany(DaycareSubscription::class);
    }

    // علاقة مع الغياب بإذن
    public function excusedAbsences()
    {
        return $this->hasMany(ExcusedAbsence::class);
    }

    // الحصول على الغياب بإذن النشط حالياً
    public function activeExcusedAbsences()
    {
        $today = now()->toDateString();
        return $this->hasMany(ExcusedAbsence::class)
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today);
    }

    // الحصول على دراسة الحالة الحالية
    public function currentCase()
    {
        return $this->hasOne(StudentCase::class)->latest();
    }

    // الحصول على نص الجنس
    public function getGenderTextAttribute(): string
    {
        return $this->gender === 'male' ? 'ذكر' : 'أنثى';
    }

    // الحصول على نص الحالة
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'new' => 'جديد',
            'under_assessment' => 'تحت التقييم',
            'active' => 'نشط',
            default => 'غير محدد',
        };
    }

    // الحصول على لون الحالة
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'new' => 'warning',
            'under_assessment' => 'info',
            'active' => 'success',
            default => 'secondary',
        };
    }

    /**
     * حساب عدد أيام الغياب المتتالية بدون إذن
     * يحسب أطول فترة غياب متتالية للجلسات أو الرعاية النهارية
     */
    public function getConsecutiveUnexcusedAbsenceDaysAttribute(): int
    {
        $maxDays = 0;

        // حساب للجلسات
        $sessionAbsences = $this->sessions()
            ->where('status', 'absent')
            ->where('is_excused', false)
            ->orderBy('session_date', 'asc')
            ->pluck('session_date')
            ->toArray();

        $maxDays = max($maxDays, $this->calculateMaxConsecutiveDays($sessionAbsences));

        // حساب للرعاية النهارية
        $daycareAbsences = DaycareAttendance::whereHas('subscription', function ($query) {
                $query->where('student_id', $this->id);
            })
            ->where('status', 'absent')
            ->where('is_excused', false)
            ->orderBy('date', 'asc')
            ->pluck('date')
            ->toArray();

        $maxDays = max($maxDays, $this->calculateMaxConsecutiveDays($daycareAbsences));

        return $maxDays;
    }

    /**
     * حساب أطول فترة متتالية من التواريخ
     */
    private function calculateMaxConsecutiveDays(array $dates): int
    {
        if (empty($dates)) {
            return 0;
        }

        $maxConsecutive = 1;
        $currentConsecutive = 1;

        for ($i = 1; $i < count($dates); $i++) {
            $prevDate = Carbon::parse($dates[$i - 1]);
            $currDate = Carbon::parse($dates[$i]);

            // نعتبر يومين متتاليين إذا الفرق يوم واحد أو أقل (للتعامل مع عطلات نهاية الأسبوع)
            $diff = $prevDate->diffInDays($currDate);

            if ($diff <= 3) { // نسمح بفجوة 3 أيام للعطل
                $currentConsecutive++;
            } else {
                $currentConsecutive = 1;
            }

            $maxConsecutive = max($maxConsecutive, $currentConsecutive);
        }

        return $maxConsecutive;
    }

    /**
     * هل الطالب في حالة خطر؟
     * خطر = غياب أكثر من 7 أيام متتالية بدون إذن
     */
    public function getIsAtRiskAttribute(): bool
    {
        return $this->consecutive_unexcused_absence_days >= 7;
    }

    /**
     * الحصول على آخر تاريخ حضور
     */
    public function getLastAttendanceDateAttribute(): ?Carbon
    {
        // آخر جلسة حضرها
        $lastSession = $this->sessions()
            ->where('status', 'completed')
            ->orderBy('session_date', 'desc')
            ->first();

        // آخر يوم حضره في الرعاية
        $lastDaycare = DaycareAttendance::whereHas('subscription', function ($query) {
                $query->where('student_id', $this->id);
            })
            ->where('status', 'present')
            ->orderBy('date', 'desc')
            ->first();

        $dates = [];
        if ($lastSession) {
            $dates[] = $lastSession->session_date;
        }
        if ($lastDaycare) {
            $dates[] = $lastDaycare->date;
        }

        if (empty($dates)) {
            return null;
        }

        return max($dates);
    }

    /**
     * الحصول على عدد أيام منذ آخر حضور
     */
    public function getDaysSinceLastAttendanceAttribute(): ?int
    {
        $lastDate = $this->last_attendance_date;
        if (!$lastDate) {
            return null;
        }

        return $lastDate->diffInDays(Carbon::now());
    }
}
