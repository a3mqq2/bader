<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'treasury_id',
        'transaction_id',
        'receipt_number',
        'amount',
        'payment_method',
        'bank_name',
        'account_number',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->receipt_number)) {
                $payment->receipt_number = self::generateReceiptNumber();
            }
        });

        // تحديث المبلغ المدفوع في الفاتورة بعد إنشاء الدفعة
        static::created(function ($payment) {
            $payment->updateInvoicePaidAmount();
        });

        // تحديث المبلغ المدفوع عند حذف الدفعة
        static::deleted(function ($payment) {
            $payment->updateInvoicePaidAmount();
        });
    }

    public static function generateReceiptNumber(): string
    {
        $year = date('Y');
        $lastPayment = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($lastPayment && preg_match('/REC' . $year . '(\d+)/', $lastPayment->receipt_number, $matches)) {
            $sequence = intval($matches[1]) + 1;
        }

        return 'REC' . $year . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function updateInvoicePaidAmount(): void
    {
        $totalPaid = self::where('invoice_id', $this->invoice_id)->sum('amount');
        $this->invoice->update(['paid_amount' => $totalPaid]);
        $this->invoice->updateStatus();
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function treasury()
    {
        return $this->belongsTo(Treasury::class);
    }

    public function transaction()
    {
        return $this->belongsTo(FinancialTransaction::class, 'transaction_id');
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2) . ' د.ل';
    }

    public function getPaymentMethodTextAttribute(): string
    {
        return match($this->payment_method) {
            'cash' => 'نقدي',
            'bank_transfer' => 'تحويل بنكي',
            'card' => 'بطاقة',
            default => 'غير محدد',
        };
    }

    public function getPaymentMethodIconAttribute(): string
    {
        return match($this->payment_method) {
            'cash' => 'fa-money-bill-wave',
            'bank_transfer' => 'fa-university',
            'card' => 'fa-credit-card',
            default => 'fa-question',
        };
    }
}
