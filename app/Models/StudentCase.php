<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentCase extends Model
{
    protected $fillable = [
        'student_id',
        'notes',
        'status',
        'created_by',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function assessmentResults()
    {
        return $this->hasManyThrough(
            InvoiceItem::class,
            Invoice::class,
            'student_case_id', // Foreign key on invoices table
            'invoice_id',      // Foreign key on invoice_items table
            'id',              // Local key on student_cases table
            'id'               // Local key on invoices table
        );
    }

    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'pending' => 'في الانتظار',
            'in_progress' => 'جاري التقييم',
            'completed' => 'مكتمل',
            default => 'غير محدد',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'in_progress' => 'info',
            'completed' => 'success',
            default => 'secondary',
        };
    }
}
