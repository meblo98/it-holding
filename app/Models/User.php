<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Rôles disponibles
    const ROLES = [
        'admin'       => 'Administrateur',
        'dg'          => 'Directeur Général',
        'commercial'  => 'Commercial',
        'comptable'   => 'Comptable',
        'magasinier'  => 'Magasinier',
        'technicien'  => 'Technicien',
        'livreur'     => 'Livreur',
        'client'      => 'Client',
    ];

    protected $fillable = [
        'name', 'email', 'password', 'photo', 'is_admin', 'role',
        'username', 'phone', 'country', 'state', 'zip_code', 'address',
        'billing_first_name', 'billing_last_name', 'billing_address',
        'billing_city', 'billing_zip',
        'shipping_first_name', 'shipping_last_name', 'shipping_address',
        'shipping_city', 'shipping_zip',
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
            'is_admin'          => 'boolean',
        ];
    }

    // ── Role helpers ────────────────────────────────────────────────────────
    public function hasRole(string ...$roles): bool
    {
        if ($this->is_admin || $this->role === 'admin') return true;
        return in_array($this->role, $roles);
    }

    public function isAdmin(): bool       { return $this->is_admin || $this->role === 'admin'; }
    public function isDG(): bool          { return $this->hasRole('dg'); }
    public function isCommercial(): bool  { return $this->hasRole('commercial'); }
    public function isComptable(): bool   { return $this->hasRole('comptable'); }
    public function isMagasinier(): bool  { return $this->hasRole('magasinier'); }
    public function isTechnicien(): bool  { return $this->hasRole('technicien'); }
    public function isLivreur(): bool     { return $this->hasRole('livreur'); }

    public function isStaff(): bool
    {
        return $this->is_admin || in_array($this->role, ['admin', 'dg', 'commercial', 'comptable', 'magasinier', 'technicien', 'livreur']);
    }

    public function getRoleLabelAttribute(): string
    {
        return self::ROLES[$this->role] ?? ucfirst($this->role);
    }

    // ── Relations ────────────────────────────────────────────────────────────
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // ── Photo accessor ───────────────────────────────────────────────────────
    protected function photoUrl(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function ($value) {
                if ($this->photo) {
                    return asset('storage/' . $this->photo);
                }
                return "https://i.pravatar.cc/150?u=" . $this->id;
            }
        );
    }
}
