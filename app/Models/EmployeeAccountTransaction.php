<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeAccountTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'treasury_id',
        'financial_transaction_id',
        'type',
        'transaction_type', // advance, bonus, deduction, salary
        'amount',
        'balance_after',
        'description',
        'payment_method', // cash, bank_transfer
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    // العلاقات
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function treasury()
    {
        return $this->belongsTo(Treasury::class);
    }

    public function financialTransaction()
    {
        return $this->belongsTo(FinancialTransaction::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors
    public function getTypeTextAttribute(): string
    {
        return match($this->type) {
            'credit' => 'له',
            'debit' => 'عليه',
            default => $this->type,
        };
    }

    public function getTransactionTypeTextAttribute(): string
    {
        return match($this->transaction_type) {
            'advance' => 'سلفة',
            'bonus' => 'مكافأة',
            'deduction' => 'خصم',
            'salary' => 'راتب',
            'session_incentive' => 'حافز جلسة',
            'daycare_incentive' => 'حافز رعاية',
            default => $this->transaction_type ?? '-',
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'credit' => 'success',
            'debit' => 'danger',
            default => 'secondary',
        };
    }

    public function getTransactionTypeColorAttribute(): string
    {
        return match($this->transaction_type) {
            'advance' => 'warning',
            'bonus' => 'success',
            'deduction' => 'danger',
            'salary' => 'primary',
            'session_incentive' => 'info',
            'daycare_incentive' => 'info',
            default => 'secondary',
        };
    }

    public function getTypeIconAttribute(): string
    {
        return match($this->transaction_type) {
            'advance' => 'ti-cash',
            'bonus' => 'ti-gift',
            'deduction' => 'ti-minus',
            'salary' => 'ti-wallet',
            'session_incentive' => 'ti-stethoscope',
            'daycare_incentive' => 'ti-heart-handshake',
            default => 'ti-exchange',
        };
    }

    public function getAffectsTreasuryAttribute(): bool
    {
        return $this->payment_method === 'cash' && in_array($this->transaction_type, ['advance']);
    }

    public function getPaymentMethodTextAttribute(): string
    {
        return match($this->payment_method) {
            'cash' => 'نقدي',
            'bank_transfer' => 'تحويل بنكي',
            default => '-',
        };
    }

    public function getPaymentMethodIconAttribute(): string
    {
        return match($this->payment_method) {
            'cash' => 'ti-cash',
            'bank_transfer' => 'ti-building-bank',
            default => 'ti-coin',
        };
    }

    // Boot method لتحديث رصيد الموظف
    protected static function booted()
    {
        static::created(function ($transaction) {
            $user = $transaction->user;

            if ($transaction->type === 'credit') {
                $user->increment('account_balance', $transaction->amount);
            } else {
                $user->decrement('account_balance', $transaction->amount);
            }
        });
    }
}
