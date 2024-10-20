<?php

namespace App\Jobs;

use App\Order;
use App\OrderDetail;
use App\Mail\OrderMail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class ProcessShippingOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $orderId;
    protected $productId;
    protected $sellerId;
    protected $trackingNumber;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($orderId, $productId, $sellerId, $trackingNumber)
    {
        $this->orderId = $orderId;
        $this->productId = $productId;
        $this->sellerId = $sellerId;
        $this->trackingNumber = $trackingNumber;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $order = Order::with(['customer'])->find($this->orderId);

        if ($order) {
            $orderDetailUpdated = OrderDetail::where('order_id', $order->id)
                ->where('product_id', $this->productId)
                ->where('seller_id', $this->sellerId)
                ->where('tracking_number', $this->trackingNumber)
                ->update([
                    'status' => 4,
                    'shippin_date' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s')
                ]);

            if ($orderDetailUpdated) {
                $orderDetail = OrderDetail::where('order_id', $this->orderId)
                    ->where('product_id', $this->productId)
                    ->first();

                Mail::to($order->customer->email)->send(new OrderMail($order, $orderDetail));

                \App\Jobs\UpdateOrderStatusToArrivedJob::dispatch($order->id, $this->productId, $this->sellerId, $this->trackingNumber)
                ->delay(now()->addMinutes(1)); // delay 1 minute before arriving (status: 5)
            }
        }
    }
}
