<?php

namespace App\Jobs;

use App\OrderDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Carbon\Carbon;

class UpdateOrderStatusToArrivedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $orderId;
    protected $productId;
    protected $sellerId;
    protected $trackingNumber;

    /**
     * Create a new job instance.
     *
     * @param int $orderId
     * @param int $productId
     * @param string $trackingNumber
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
        try {
            $orderDetail = OrderDetail::where('order_id', $this->orderId)
                ->where('product_id', $this->productId)
                ->where('seller_id', $this->sellerId)
                ->where('tracking_number', $this->trackingNumber)
                ->first();

            if ($orderDetail) {
                $orderDetail->update([
                    'status' => 5, // Update status to "arrived"
                    'arrived_date' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'),
                ]);
            }
        } catch (\Exception $e) {
            report($e);
            throw $e;
        }
    }
}
