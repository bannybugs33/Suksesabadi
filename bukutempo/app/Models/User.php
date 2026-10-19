<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    protected $fillable = ['name', 'username', 'email', 'role', 'store_name', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function getFirstNameAttribute(): string
    {
        return Str::before($this->name, ' ');
    }

    public function getInitialsAttribute(): string
    {
        $initials = Str::of($this->name)
            ->explode(' ')
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->take(2)
            ->implode('');

        return Str::upper($initials);
    }
}
