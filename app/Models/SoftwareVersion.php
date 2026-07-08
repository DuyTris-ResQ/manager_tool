<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoftwareVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'version',
        'download_url',
        'force_update',
        'release_note'
    ];

    protected $casts = [
        'force_update' => 'boolean'
    ];
}
