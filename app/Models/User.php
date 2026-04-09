<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role_id',
        'is_active',
        'paginas_permitidas',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'paginas_permitidas',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'paginas_permitidas' => 'array',
            'last_login_at' => 'datetime',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function hasRole(string|array $roles): bool
    {
        $slugs = is_array($roles) ? $roles : [$roles];

        return in_array((string) $this->role?->slug, $slugs, true);
    }

    public function isOwner(): bool
    {
        return $this->hasRole('dueno');
    }

    public function getInitialsAttribute(): string
    {
        return collect(explode(' ', trim($this->name)))
            ->filter()
            ->take(2)
            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
            ->implode('');
    }
}
