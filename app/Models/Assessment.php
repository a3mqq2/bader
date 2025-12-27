<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $fillable = [
        'name',
        'price',
        'is_active',
        'description',
        'created_by',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusTextAttribute(): string
    {
        return $this->is_active ? 'مفعل' : 'غير مفعل';
    }

    public function getStatusColorAttribute(): string
    {
        return $this->is_active ? 'success' : 'secondary';
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2) . ' د.ل';
    }
}
