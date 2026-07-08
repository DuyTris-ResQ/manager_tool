<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'type',
        'message',
        'details'
    ];

    protected $casts = [
        'details' => 'array'
    ];
}
