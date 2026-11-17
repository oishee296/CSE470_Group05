<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    public const ROLE_CITIZEN = 'citizen';
    public const ROLE_DONOR = 'donor';
    public const ROLE_LOGISTICS = 'logistics_manager';
    public const ROLE_ADMIN = 'admin';

    protected $fillable = ['name', 'email', 'password', 'role'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', // auto-hashes on save
        ];
    }

    public function familyProfile(): HasOne
    {
        return $this->hasOne(FamilyProfile::class);
    }
}