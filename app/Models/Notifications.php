<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Item;
use App\NotificationType;

class Notifications extends Model
{
    /** @use HasFactory<\Database\Factories\NotificationsFactory> */
    use HasFactory;

    protected $table = 'auction_notifications';

    protected $fillable = [
        'user_id',
        'item_id',
        'type',
        'title',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'type' => NotificationType::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
