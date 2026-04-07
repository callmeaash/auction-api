<?php

namespace App\Services;

use App\Models\Notifications;
use App\NotificationType;
use App\Events\NotificationSent;

class NotificationService
{
    /**
     * Create a notification.
     *
     * @param int $userId The recipient's user ID.
     * @param NotificationType $type The notification type enum.
     * @param string $title The notification title.
     * @param string $message The notification message.
     * @param int|null $itemId The item ID related to the notification (optional).
     * @return Notifications
     */
    public static function createNotification(int $userId, NotificationType $type, string $title, string $message, ?int $itemId = null): Notifications
    {
        $notification = Notifications::create([
            'user_id' => $userId,
            'item_id' => $itemId,
            'type'    => $type,
            'title'   => $title,
            'message' => $message,
            'is_read' => false,
        ]);

        broadcast(new NotificationSent($notification));

        return $notification;
    }

    /**
     * Send an 'outbid' notification to a user.
     * 
     * @param int $userId
     * @param mixed $item The Item model instance
     * @param float $newBidAmount
     */
    public static function notifyOutbid(int $userId, $item, float $newBidAmount): Notifications
    {
        return self::createNotification(
            $userId,
            NotificationType::BID_OUTBID,
            'You have been outbid',
            "Your bid on '{$item->title}' was outbid with a new high bid of \${$newBidAmount}.",
            $item->id
        );
    }

    /**
     * Notify an item owner that their item has been commented on.
     * 
     * @param mixed $item
     * @param string $commenterName
     */
    public static function notifyNewComment($item, string $commenterName): Notifications
    {
        return self::createNotification(
            $item->user_id,
            NotificationType::ITEM_COMMENTED,
            'New comment on your item',
            "{$commenterName} left a comment on '{$item->title}'.",
            $item->id
        );
    }

    /**
     * Notify the winner of an auction.
     * 
     * @param int $winnerId
     * @param mixed $item
     * @param float $winningBid
     */
    public static function notifyAuctionWon(int $winnerId, $item, float $winningBid): Notifications
    {
        return self::createNotification(
            $winnerId,
            NotificationType::AUCTION_WON,
            'You won the auction!',
            "Congratulations! You won the auction for '{$item->title}' with a bid of \${$winningBid}.",
            $item->id
        );
    }

    /**
     * Notify the owner about the outcome of their auction.
     * 
     * @param mixed $item
     * @param float|null $finalPrice Required if item is sold.
     */
    public static function notifyItemSold($item, ?float $finalPrice = null): Notifications
    {
        if ($item->getTotalBids() === 0) {
            return self::createNotification(
                $item->user_id,
                NotificationType::ITEM_UNSOLD,
                'Your item was not sold',
                "Your item '{$item->title}' ended with no bids and remains unsold.",
                $item->id
            );
        }

        return self::createNotification(
            $item->user_id,
            NotificationType::ITEM_SOLD,
            'Your item has been sold',
            "Your item '{$item->title}' was sold for \${$finalPrice}.",
            $item->id
        );
    }

    /**
     * Notify a user that an item in their wishlist is ending within 1 hour.
     * 
     * @param int $userId
     * @param mixed $item
     */
    public static function notifyItemEndingSoon(int $userId, $item): Notifications
    {
        return self::createNotification(
            $userId,
            NotificationType::ITEM_ENDING_SOON,
            'Auction ending soon!',
            "An item in your wishlist '{$item->title}' is ending in less than 1 hour. Place your bid now!",
            $item->id
        );
    }
}
