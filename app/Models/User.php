<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Item;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Bid;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Comment;
use App\Models\Report;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'username',
        'email',
        'password',
        'fullname',
        'avatar',
        'country',
        'phone',
        'bio',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_active' => 'boolean',
        ];
    }


    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function wonItems(): HasMany
    {
        return $this->hasMany(Item::class, 'winner_id');
    }

    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    public function wishlists(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'wishlists');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }
}
