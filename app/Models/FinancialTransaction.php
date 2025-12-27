<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'treasury_id',
        'category_id',
        'type',
        'amount',
        'description',
        'payment_method',
        'bank_name',
        'account_number',
        'document_number',
        'recipient_name',
        'balance_after',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($transaction) {
            // تحديث رصيد الخزينة بعد إنشاء الحركة
            $transaction->treasury->updateBalance();
        });

        static::deleted(function ($transaction) {
            // تحديث رصيد الخزينة بعد حذف الحركة
            $transaction->treasury->updateBalance();
        });
    }

    /**
     * الخزينة
     */
    public function treasury()
    {
        return $this->belongsTo(Treasury::class);
    }

    /**
     * التصنيف
     */
    public function category()
    {
        return $this->belongsTo(TransactionCategory::class, 'category_id');
    }

    /**
     * منشئ الحركة
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * نص النوع
     */
    public function getTypeTextAttribute()
    {
        return match($this->type) {
            'income' => 'إيراد',
            'expense' => 'مصروف',
            default => $this->type,
        };
    }

    /**
     * لون النوع
     */
    public function getTypeColorAttribute()
    {
        return match($this->type) {
            'income' => 'success',
            'expense' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * نص طريقة الدفع
     */
    public function getPaymentMethodTextAttribute()
    {
        return match($this->payment_method) {
            'cash' => 'نقدي',
            'bank_transfer' => 'تحويل بنكي',
            default => $this->payment_method,
        };
    }
}
