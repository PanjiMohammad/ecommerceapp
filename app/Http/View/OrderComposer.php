<?php

namespace App\Http\View;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Order;
use App\OrderReturn;

class OrderComposer
{
    private function getNewOrdersCount()
    {
        if (Auth::guard('customer')->check()) {
            // Count orders with status 0 (new) for the authenticated user
            // return Order::with('details')->where('customer_id', Auth::guard('customer')->user()->id)->where('status', 0)->count();
            return Order::where('customer_id', Auth::guard('customer')->user()->id)->whereHas('details', function($query) {
                $query->where('status', 0);
            })->count();
        }
        
        return 0;
    }

    private function getProductStatusAnnouncements()
    {
        try {
            // Check if the user is authenticated
            $user = Auth::guard('customer')->user();

            // If the user is not authenticated, return an empty array
            if (!$user) {
                return [];
            }

            $userId = $user->id;

            // Define status descriptions
            $statusDescriptions = [
                1 => 'Pembayaran Berhasil & Menunggu Konfirmasi',
                2 => 'Sudah Dikonfirmasi',
                3 => 'Sedang Diproses',
                4 => 'Dikirim',
                5 => 'Sampai',
            ];

            // Define return status descriptions
            $statusReturnDescriptions = [
                0 => 'Produk ini sedang mengajukan return',
                1 => 'Return Sudah Dikonfirmasi',
            ];

            // Fetch orders and product details with eager loading
            $orders = Order::with([
                'details' => function ($query) {
                    $query->select('order_id', 'product_id', 'qty', 'price', 'status')
                        ->whereIn('status', [1, 2, 3, 4, 5])
                        ->orderBy('created_at', 'DESC'); // Only include specific status
                }, 
                'details.product' => function ($query) {
                    $query->select('id', 'name', 'image')
                    ->orderBy('created_at', 'DESC'); // Select only necessary fields
                }
            ])
            ->where('customer_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->get(['id', 'invoice', 'created_at']);

            // Map the announcements for each order and order detail
            $announcements = $orders->flatMap(function ($order) use ($statusDescriptions, $statusReturnDescriptions) {
                return $order->details->map(function ($detail) use ($order, $statusDescriptions, $statusReturnDescriptions) {

                    // Check if there's a return for this product in the current order
                    $productReturn = $order->return->first(function ($return) use ($detail) {
                        return $return->order_id === $detail->order_id && $return->product_id === $detail->product_id;
                    });

                    // Return the details for each product
                    return [
                        'product_name' => $detail->product->name,
                        'image' => $detail->product->image,
                        'type' => $detail->product->type,
                        'qty' => $detail->qty,
                        'price' => $detail->price,
                        'status' => $statusDescriptions[$detail->status] ?? 'Status tidak diketahui', // Handle null status gracefully
                        'invoice' => $order->invoice,
                        'return_status' => $productReturn && isset($statusReturnDescriptions[$productReturn->status]) 
                            ? $statusReturnDescriptions[$productReturn->status] 
                            : null, // Only show return status if applicable
                    ];
                });
            });

            return $announcements;

        } catch (\Exception $e) {
            // Log the error and return an empty array if an exception occurs
            Log::error('Error fetching product status announcements: ' . $e->getMessage());

            return [];
        }
    }

    private function getOrdersWithSpecificStatusesCount()
    {
        if (Auth::guard('customer')->check()) {
            return Order::where('customer_id', Auth::guard('customer')->user()->id)
                ->whereHas('details', function($query) {
                    $query->whereIn('status', [1, 2, 3, 4, 5]);
                })->count();
        }

        return 0;
    }

    public function compose(View $view)
    {
        $newOrdersCount = $this->getNewOrdersCount();
        $productStatusAnnouncements = $this->getProductStatusAnnouncements();
        $ordersWithSpecificStatusesCount = $this->getOrdersWithSpecificStatusesCount();

        $view->with('newOrdersCount', $newOrdersCount)
             ->with('productStatusAnnouncements', $productStatusAnnouncements)
             ->with('ordersWithSpecificStatusesCount', $ordersWithSpecificStatusesCount);
    }
}
