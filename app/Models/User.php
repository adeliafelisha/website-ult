<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'is_admin'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['email_verified_at' => 'datetime', 'password' => 'hashed', 'is_admin' => 'boolean'];

    public function canAccessPanel(Panel $panel): bool
    {
        $allowedDomains = array_filter(array_map('trim', explode(',', (string) config('auth.admin_domains'))));
        $domainAllowed = $allowedDomains === [] || collect($allowedDomains)->contains(fn (string $domain) => str_ends_with(strtolower($this->email), '@'.strtolower($domain)));

        return $panel->getId() === 'admin' && $this->is_admin && $this->email_verified_at !== null && $domainAllowed;
    }
}
