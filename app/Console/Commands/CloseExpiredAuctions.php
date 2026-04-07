<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Item;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class CloseExpiredAuctions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auctions:close-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close all expired auctions and notify winners and owners';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredItems = Item::where('is_active', true)
            ->where('end_date', '<=', now())
            ->whereNull('winner_id')
            ->get();

        if ($expiredItems->isEmpty()) {
            $this->info('No expired auctions to close.');
            return;
        }

        foreach ($expiredItems as $item) {
            DB::transaction(function () use ($item) {
                try {
                    $item = Item::where('id', $item->id)->lockForUpdate()->first();

                    $highestBid = $item->highestBid;

                    $winnerId = $highestBid ? $highestBid->user_id : null;
                    $finalBid = $highestBid ? $highestBid->amount : null;

                    $item->update([
                        'is_active' => false,
                        'winner_id' => $winnerId,
                    ]);
                    if ($winnerId) {
                        NotificationService::notifyAuctionWon(
                            $winnerId,
                            $item,
                            $finalBid
                        );
                    }

                    NotificationService::notifyItemSold($item, $finalBid);

                    $this->info("Successfully processed item {$item->id}: {$item->title}");

                } catch (\Exception $e) {
                    $this->error("Failed processing item {$item->id}: " . $e->getMessage());
                    throw $e;
                }
            });
        }

        $this->info('Finished closing expired auctions.');
    }
}
