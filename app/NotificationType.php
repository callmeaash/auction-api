<?php

namespace App;

enum NotificationType: string
{
    case BID_OUTBID = 'bid_outbid';
    case ITEM_COMMENTED = 'item_commented';
    case AUCTION_WON = 'auction_won';
    case ITEM_SOLD = 'item_sold';
    case ITEM_UNSOLD = 'item_unsold';
    case ITEM_ENDING_SOON = 'item_ending_soon';
}
