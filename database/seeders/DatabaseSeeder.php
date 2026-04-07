<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Item;
use App\Models\Wishlist;
use App\Models\Bid;
use App\Models\Comment;
use App\Models\Report;
use App\Models\Notifications;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory(10)->create();
        $items = Item::factory(10)->recycle($users)->create();
        Wishlist::factory(10)->recycle($users)->recycle($items)->create();
        Bid::factory(10)->recycle($users)->recycle($items)->create();
        Comment::factory(10)->recycle($users)->recycle($items)->create();
        Report::factory(10)->recycle($users)->recycle($items)->create();
        Notifications::factory(10)->recycle($users)->recycle($items)->create();
    }
}
