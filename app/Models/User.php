<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone',
        'password',
        'is_active',
        'code',
        'salary',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'account_balance',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'is_active' => 'boolean',
        'salary' => 'decimal:2',
        'account_balance' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->code)) {
                $user->code = self::generateCode();
            }
        });
    }

    /**
     * توليد كود فريد مكون من 6 أرقام
     */
    public static function generateCode(): string
    {
        do {
            $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('code', $code)->exists());

        return $code;
    }

    /**
     * اسم الحقل المستخدم للمصادقة
     */
    public function username(): string
    {
        return 'phone';
    }

    /**
     * الحصول على نص الدور
     */
    public function getRoleTextAttribute(): string
    {
        $role = $this->roles->first();
        if (!$role) {
            return 'غير محدد';
        }

        return match($role->name) {
            'admin' => 'مدير النظام',
            'specialist' => 'أخصائي',
            'supervisor' => 'مشرف',
            'accountant' => 'محاسب',
            default => $role->display_name ?? $role->name,
        };
    }

    /**
     * سجلات الحضور
     */
    public function attendances()
    {
        return $this->hasMany(EmployeeAttendance::class);
    }

    /**
     * حضور اليوم
     */
    public function todayAttendance()
    {
        return $this->hasOne(EmployeeAttendance::class)->whereDate('date', today());
    }

    /**
     * حركات الحساب المالي
     */
    public function accountTransactions()
    {
        return $this->hasMany(EmployeeAccountTransaction::class);
    }

    /**
     * الخزائن المخولة
     */
    public function authorizedTreasuries()
    {
        return $this->belongsToMany(Treasury::class, 'treasury_user');
    }

    /**
     * نص حالة الرصيد
     */
    public function getBalanceStatusAttribute(): string
    {
        if ($this->account_balance > 0) {
            return 'دائن';
        } elseif ($this->account_balance < 0) {
            return 'مدين';
        }
        return 'متوازن';
    }

    /**
     * لون حالة الرصيد
     */
    public function getBalanceColorAttribute(): string
    {
        if ($this->account_balance > 0) {
            return 'success';
        } elseif ($this->account_balance < 0) {
            return 'danger';
        }
        return 'secondary';
    }

    /**
     * هل لديه حساب مصرفي محفوظ
     */
    public function getHasBankAccountAttribute(): bool
    {
        return !empty($this->bank_name) && !empty($this->bank_account_number);
    }

    /**
     * معلومات الحساب المصرفي المختصرة
     */
    public function getBankAccountInfoAttribute(): ?string
    {
        if (!$this->has_bank_account) {
            return null;
        }
        return "{$this->bank_name} - {$this->bank_account_number}";
    }
}