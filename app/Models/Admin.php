<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Admin extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
        ];
    }

    public function hasRole(string|array $roles): bool
    {
        $roles = collect(is_array($roles) ? $roles : [$roles])
            ->flatMap(fn (string $role) => explode('|', $role))
            ->map(fn (string $role) => strtolower(trim($role)))
            ->filter()
            ->values();

        if ($roles->isEmpty()) {
            return false;
        }

        if ($roles->contains('admin')) {
            return true;
        }

        return $roles->contains('super admin') && $this->isSuperAdmin();
    }

    public function hasAnyPermission(array|string $permissions): bool
    {
        return true;
    }

    public function getRoleNames(): Collection
    {
        return collect([$this->isSuperAdmin() ? 'Super Admin' : 'Admin']);
    }

    protected function isSuperAdmin(): bool
    {
        return Str::lower((string) $this->email) === 'admin@gmail.com'
            || Str::lower((string) $this->name) === 'super admin';
    }
}
