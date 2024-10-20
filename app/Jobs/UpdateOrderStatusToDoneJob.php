<?php

namespace App\Jobs;

use App\OrderDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class UpdateOrderStatusToDoneJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $orderId;
    protected $productId;
    protected $trackingNumber;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($orderId, $productId, $trackingNumber)
    {
        $this->orderId = $orderId;
        $this->productId = $productId;
        $this->trackingNumber = $trackingNumber;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $orderDetail = OrderDetail::where('order_id', $this->orderId)
                ->where('product_id', $this->productId)
                ->where('tracking_number', $this->trackingNumber)
                ->first();

            if ($orderDetail) {
                $orderDetail->update(['status' => 5]); // status 5 for arrived
                Log::info('Order status updated to arrived', [
                    'orderId' => $this->orderId,
                    'productId' => $this->productId,
                ]);
            } else {
                Log::warning('OrderDetail not found', [
                    'orderId' => $this->orderId,
                    'productId' => $this->productId,
                    'trackingNumber' => $this->trackingNumber,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to update order status to arrived', [
                'message' => $e->getMessage(),
                'orderId' => $this->orderId,
                'productId' => $this->productId,
                'trackingNumber' => $this->trackingNumber,
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}