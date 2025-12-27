<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Treasury extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'opening_balance',
        'current_balance',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($treasury) {
            // إنشاء حركة الرصيد الافتتاحي تلقائياً
            if ($treasury->opening_balance > 0) {
                $openingCategory = TransactionCategory::firstOrCreate(
                    ['name' => 'رصيد افتتاحي', 'for_system' => true],
                    ['type' => 'income', 'is_active' => true]
                );

                FinancialTransaction::create([
                    'treasury_id' => $treasury->id,
                    'category_id' => $openingCategory->id,
                    'type' => 'income',
                    'amount' => $treasury->opening_balance,
                    'description' => 'رصيد افتتاحي للخزينة',
                    'payment_method' => 'cash',
                    'balance_after' => $treasury->opening_balance,
                    'created_by' => $treasury->created_by,
                ]);
            }
        });
    }

    /**
     * المستخدمين المخولين
     */
    public function authorizedUsers()
    {
        return $this->belongsToMany(User::class, 'treasury_user')->withTimestamps();
    }

    /**
     * الحركات المالية
     */
    public function transactions()
    {
        return $this->hasMany(FinancialTransaction::class);
    }

    /**
     * منشئ الخزينة
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * تحديث الرصيد الحالي
     */
    public function updateBalance()
    {
        $income = $this->transactions()->where('type', 'income')->sum('amount');
        $expense = $this->transactions()->where('type', 'expense')->sum('amount');
        $this->current_balance = $income - $expense;
        $this->saveQuietly();
    }

    /**
     * Scope للخزائن النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
