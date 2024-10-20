<?php

namespace App\Listeners;

use App\Events\ShippingCompleted;
use App\Models\OrderDetail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateOrderStatusToArrived
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\ShippingCompleted  $event
     * @return void
     */
    public function handle(ShippingCompleted $event)
    {
        OrderDetail::where('order_id', $event->orderId)
            ->where('product_id', $event->productId)
            ->update([
                'status' => 4, // Assuming 4 is the status code for 'arrived'
                'arrival_date' => now('Asia/Jakarta')->format('Y-m-d H:i:s')
            ]);
    }
}
