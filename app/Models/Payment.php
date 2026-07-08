<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'license_id',
        'order_code',
        'provider',
        'amount',
        'status',
        'transaction_code',
        'paid_at',
        'raw_data'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'raw_data' => 'array'
    ];

    public function license()
    {
        return $this->belongsTo(License::class);
    }
}
