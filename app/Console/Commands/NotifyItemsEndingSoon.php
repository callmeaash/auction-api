<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Item;
use App\Services\NotificationService;

class NotifyItemsEndingSoon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auctions:notify-ending-soon';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify users watching items that are ending within 1 hour';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Define the target time window (e.g., between 59 and 61 minutes from now)
        // This ensures we catch items exactly as they enter the 1-hour window.
        $startTime = now()->addMinutes(59);
        $endTime = now()->addMinutes(61);

        $items = Item::where('is_active', true)
            ->whereBetween('end_date', [$startTime, $endTime])
            ->with('wishlistedBy')
            ->get();

        if ($items->isEmpty()) {
            $this->info('No items ending soon found in this window.');
            return;
        }

        foreach ($items as $item) {
            $watchers = $item->wishlistedBy;
            
            foreach ($watchers as $user) {
                NotificationService::notifyItemEndingSoon($user->id, $item);
                $this->info("Notified user {$user->id} about item '{$item->title}'");
            }
        }

        $this->info('Finished sending ending soon notifications.');
    }
}
