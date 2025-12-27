<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'assessment_id',
        'assessment_name',
        'price',
        'quantity',
        'total',
        'assessment_status',
        'assessment_result',
        'assessment_notes',
        'assessed_by',
        'assessed_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'total' => 'decimal:2',
        'assessed_at' => 'datetime',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function assessor()
    {
        return $this->belongsTo(User::class, 'assessed_by');
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2) . ' د.ل';
    }

    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total, 2) . ' د.ل';
    }

    public function getAssessmentStatusTextAttribute(): string
    {
        return match($this->assessment_status) {
            'pending' => 'في الانتظار',
            'completed' => 'مكتمل',
            default => 'غير محدد',
        };
    }

    public function getAssessmentStatusColorAttribute(): string
    {
        return match($this->assessment_status) {
            'pending' => 'warning',
            'completed' => 'success',
            default => 'secondary',
        };
    }

    public function isCompleted(): bool
    {
        return $this->assessment_status === 'completed';
    }
}
