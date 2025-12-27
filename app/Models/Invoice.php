<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'description',
        'student_id',
        'student_case_id',
        'invoice_type_id',
        'total_amount',
        'paid_amount',
        'discount',
        'status',
        'notes',
        'created_by',
    ];

    public function invoiceType()
    {
        return $this->belongsTo(InvoiceType::class);
    }

    public function getTypeTextAttribute(): string
    {
        return $this->invoiceType?->name ?? 'غير محدد';
    }

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'discount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = self::generateInvoiceNumber();
            }
        });
    }

    public static function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $lastInvoice = self::where('invoice_number', 'like', 'INV' . $year . '%')
            ->orderByRaw('CAST(SUBSTRING(invoice_number, 8) AS UNSIGNED) DESC')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, 7);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'INV' . $year . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function studentCase()
    {
        return $this->belongsTo(StudentCase::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function updateStatus(): void
    {
        $balance = $this->balance;

        if ($balance <= 0) {
            $this->update(['status' => 'paid']);
        } elseif ($this->paid_amount > 0) {
            $this->update(['status' => 'partial']);
        } else {
            $this->update(['status' => 'pending']);
        }
    }

    public function getBalanceAttribute(): float
    {
        return $this->total_amount - $this->discount - $this->paid_amount;
    }

    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'pending' => 'غير مدفوعة',
            'partial' => 'مدفوعة جزئياً',
            'paid' => 'مدفوعة',
            default => 'غير محدد',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'danger',
            'partial' => 'warning',
            'paid' => 'success',
            default => 'secondary',
        };
    }

    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total_amount, 2) . ' د.ل';
    }

    public function getFormattedPaidAttribute(): string
    {
        return number_format($this->paid_amount, 2) . ' د.ل';
    }

    public function getFormattedBalanceAttribute(): string
    {
        return number_format($this->balance, 2) . ' د.ل';
    }
}
