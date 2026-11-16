<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'department_id',
        //'username',         // <-- Ditambahkan agar simpan username berhasil
        'identity_number',
        'name',
        'email',
        'whatsapp_number',
        'department_name',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /**
     * Relasi ke OPD/Department (jika user ASN/Admin).
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Relasi ke Tiket yang dibuat oleh user ini.
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'user_id');
    }

    /**
     * Relasi ke Tiket yang ditangani (ditugaskan) ke Admin ini.
     */
    public function managedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assigned_agent_id');
    }

    /**
     * Helper check role Admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}