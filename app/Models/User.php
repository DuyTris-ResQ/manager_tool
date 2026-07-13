<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'settings', 'permissions', 'is_active', 'max_licenses'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'settings' => 'array',
            'permissions' => 'array',
            'is_active' => 'boolean',
            'max_licenses' => 'integer',
        ];
    }

    /**
     * Get a user-specific setting.
     */
    public function getSetting(string $key, $default = null)
    {
        $settings = $this->settings ?? [];
        return $settings[$key] ?? $default;
    }

    /**
     * Set a user-specific setting.
     */
    public function setSetting(string $key, $value)
    {
        $settings = $this->settings ?? [];
        $settings[$key] = $value;
        $this->settings = $settings;
        $this->save();
    }

    /**
     * Check if the user has a specific permission.
     */
    public function hasPermission(string $permission, bool $default = true): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        $permissions = $this->permissions ?? [];
        return isset($permissions[$permission]) ? (bool) $permissions[$permission] : $default;
    }

    /**
     * Check if user is super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Get the licenses owned by this user.
     */
    public function licenses()
    {
        return $this->hasMany(License::class);
    }
}
