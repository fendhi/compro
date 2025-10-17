<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\LogsActivityWithIp;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, LogsActivityWithIp;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'username', 'email', 'role', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'User baru ditambahkan',
                'updated' => 'Data user diperbarui',
                'deleted' => 'User dihapus',
                default => "User {$eventName}"
            });
    }

    // Role Helper Methods
    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isKasir(): bool
    {
        return $this->role === 'kasir';
    }

    public function hasRole(...$roles): bool
    {
        return in_array($this->role, $roles);
    }

    public function canAccessFinancial(): bool
    {
        return $this->hasRole('owner', 'admin');
    }

    public function canManageUsers(): bool
    {
        return $this->isOwner();
    }

    public function canManageMasterData(): bool
    {
        return $this->hasRole('owner', 'admin');
    }

    public function canViewAllTransactions(): bool
    {
        return $this->hasRole('owner', 'admin');
    }
}
