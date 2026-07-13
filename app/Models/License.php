<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    use HasFactory;

    protected $fillable = [
        'license_key',
        'status',
        'trial_start',
        'expire_at',
        'max_devices',
        'user_id',
        'product_name'
    ];

    protected $casts = [
        'trial_start' => 'datetime',
        'expire_at' => 'datetime',
        'max_devices' => 'integer',
        'user_id' => 'integer',
        'product_name' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Resolve a setting, prioritizing the owner user's settings, falling back to global settings.
     */
    public function getSetting(string $key, $default = null)
    {
        if ($this->user_id) {
            $user = $this->user;
            if ($user) {
                $val = $user->getSetting($key);
                if ($val !== null && $val !== '') {
                    return $val;
                }
            }
        }
        return Setting::get($key, $default);
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Check if the license is expired.
     */
    public function isExpired(): bool
    {
        if ($this->status === 'expired') {
            return true;
        }

        if (in_array($this->status, ['active', 'trial']) && $this->expire_at && $this->expire_at->isPast()) {
            // Auto update status in db if expired
            $this->update(['status' => 'expired']);
            return true;
        }

        return false;
    }

    /**
     * Check if the license is currently valid (not expired, disabled, or banned).
     */
    public function isValid(): bool
    {
        if (in_array($this->status, ['disabled', 'banned', 'expired'])) {
            return false;
        }

        return !$this->isExpired();
    }
}
