<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'license_id',
        'device_id',
        'computer_name',
        'cpu',
        'gpu',
        'os',
        'ip',
        'app_version',
        'first_login',
        'last_online',
        'is_online'
    ];

    protected $casts = [
        'first_login' => 'datetime',
        'last_online' => 'datetime',
        'is_online' => 'boolean',
    ];

    public function license()
    {
        return $this->belongsTo(License::class);
    }
}
