<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Campos que se pueden llenar con create() / updateOrCreate()
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    // Campos que nunca se muestran si el usuario se convierte a JSON
    protected $hidden = [
        'password',
        'remember_token',
        'verification_code',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'verification_expires_at' => 'datetime',
        ];
    }

    public function esAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
