<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'is_active', 'must_change_password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    public const array CMS_ROLES = [
        'super_admin' => 'Super Administrator',
        'administrator' => 'Administrator',
        'editor' => 'Editor',
        'content_manager' => 'Content Manager',
    ];

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
            'is_active' => 'boolean',
            'must_change_password' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function hasCmsAccess(): bool
    {
        return $this->is_active && array_key_exists($this->role, self::CMS_ROLES);
    }

    public function canManageCms(string $module): bool
    {
        $permissions = match ($this->role) {
            'super_admin' => ['*'],
            'administrator' => ['dashboard', 'pages', 'services', 'content', 'media', 'submissions', 'settings', 'activity'],
            'editor' => ['dashboard', 'pages', 'services', 'content', 'media'],
            'content_manager' => ['dashboard', 'pages', 'services', 'content', 'media'],
            default => [],
        };

        return $this->hasCmsAccess() && (in_array('*', $permissions, true) || in_array($module, $permissions, true));
    }
}
