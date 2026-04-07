<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Notifications;

class NotificationsPolicy
{
   public function read(User $user, Notifications $notification): bool
   {
    return $user->id === $notification->user_id;
   }

   public function delete(User $user, Notifications $notification): bool
   {
    return $user->id === $notification->user_id;
   }
}
