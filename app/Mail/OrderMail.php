<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Order;
use App\OrderDetail;

class OrderMail extends Mailable
{
    use Queueable, SerializesModels;
    protected $order;
    protected $orderDetail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Order $order, OrderDetail $orderDetail)
    {
        $this->order = $order;
        $this->orderDetail = $orderDetail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if (auth()->guard('seller')->check()) {
            return $this->subject('Pesanan Anda Dikirim ' . strtoupper($this->orderDetail->tracking_number))
                ->view('ecommerce.emails.order')
                ->with([
                    'order' => $this->order,
                    'orderDetail' => $this->orderDetail
                ]);
        } else {
            // Handle the case where the seller is not authenticated, if necessary
            return $this->subject('Pesanan Anda Dikirim ' . strtoupper($this->orderDetail->tracking_number))
                ->view('ecommerce.emails.order')
                ->with([
                    'order' => $this->order,
                    'orderDetail' => $this->orderDetail
                ]);
        }
    }
}
