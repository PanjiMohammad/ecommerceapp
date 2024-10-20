<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Order;
use App\OrderDetail;
use App\OrderCancelled;
use App\OrderCancelledDetail;
use App\OrderReturn;
use App\Payment;
use App\Product;
use App\Customer;

use App\Jobs\ProcessShippingOrderJob;
use App\Jobs\UpdateOrderStatusToArrivedJob;

// Seller, District, City, Province
use App\Seller;
use App\District;
use App\City;
use App\Province;

use App\Mail\OrderMail;
use Mail;
use Carbon\Carbon;
use PDF;
use DataTables;
use App\Exports\OrdersReturnExport;
use App\Exports\OrdersReportExport;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    public function index() 
    {
        return view('seller.orders.index');   
    }

    public function datatables(Request $request)
    {
        $ordersQuery = Order::with([
            'customer.district.city.province',
            'details' => function($q) {
                $q->where('seller_id', auth()->guard('seller')->user()->id);
            },
        ])->withCount('return')->orderBy('created_at', 'DESC');

        $orders = $ordersQuery->get();
        $orderIds = $orders->pluck('id')->toArray();

        $detailOrder = OrderDetail::whereIn('order_id', $orderIds)
            ->where('seller_id', auth()->guard('seller')->user()->id)
            ->whereIn('status', [0,1,2,3,4,5])
            ->orderBy('created_at', 'DESC')
            ->get();

        $groups = $detailOrder->groupBy('order_id'); 

        $filteredOrders = $orders->filter(function($order) use ($groups) {
            return isset($groups[$order->id]);
        });

        $returns = OrderReturn::whereIn('order_id', $filteredOrders->pluck('id'))->get();
        $orderPayments = Payment::whereIn('order_id', $filteredOrders->pluck('id'))->get();

        $returnsGrouped = $returns->groupBy('order_id')->map(function ($group) {
            return $group->keyBy('product_id');
        });

        $orderPaymentGrouped = $orderPayments->groupBy('order_id')->map(function ($group) {
            return $group->keyBy('product_id');
        });

        // karena detail orders tidak ada relasi dengan payment, maka logic ini memaksa eloquent payment dibuat relasi dengan detail-order
        $details = $groups->map(function ($detailGroup) use ($returnsGrouped, $orderPaymentGrouped) {
            return $detailGroup->map(function ($detail) use ($returnsGrouped, $orderPaymentGrouped) {
                $statusReturn = null;
                $statusLabel = null;
                $statusPayment = null;
                $formattedDate = null;

                // return order
                if (isset($returnsGrouped[$detail->order_id]) && isset($returnsGrouped[$detail->order_id][$detail->product_id])) {
                    $return = $returnsGrouped[$detail->order_id][$detail->product_id];
                    $statusReturn = $return->status;
                    $statusLabel = $return->status_label;
                }

                // payment order
                if (isset($orderPaymentGrouped[$detail->order_id]) && isset($orderPaymentGrouped[$detail->order_id][$detail->product_id])) {
                    $payment = $orderPaymentGrouped[$detail->order_id][$detail->product_id];
                    $statusPayment = $payment->status;
                    $paymentArray = $payment->toArray();
                    unset($paymentArray['order_id']); 
                    $detail->payment = $paymentArray;

                    $carbonDate = Carbon::parse($payment->transfer_date);
                    $formattedDate = $carbonDate->translatedFormat('l, d F Y');
                    $translations = [
                        'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
                        'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
                        'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April',
                        'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
                        'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
                    ];

                    $formattedDate = str_replace(array_keys($translations), array_values($translations), $formattedDate);
                    $detail->formatted_transfer_date = $formattedDate; 
                }

                $detail->status_return = $statusReturn;
                $detail->status_label_return = $statusLabel;
                $detail->status_payment = $statusPayment;

                return $detail;
            });
        });

        $subtotals = [];
        $shippingCosts = [];

        foreach ($groups as $detail) {
            foreach($detail as $row){
                $orderId = $row->order_id;
                $subtotal = $row->qty * $row->price;
                $shippingCost = $row->shipping_cost;

                if (!isset($subtotals[$orderId])) {
                    $subtotals[$orderId] = 0;
                }
                if (!isset($shippingCosts[$orderId])) {
                    $shippingCosts[$orderId] = 0;
                }

                $subtotals[$orderId] += $subtotal;
                $shippingCosts[$orderId] += $shippingCost;
            }
        }

        $totalOmsets = [];
        foreach ($subtotals as $orderId => $subtotal) {
            $tax = $subtotal * 0.10;
            $shippingCost = $shippingCosts[$orderId];
            $totalOmset = $subtotal + $tax + $shippingCost;
            $totalOmsets[$orderId] = $totalOmset;
        }

        $ordersArray = [];
        foreach ($filteredOrders as $order) {
            $order->details = $details[$order->id] ?? collect();
            $order->total_omset = $totalOmsets[$order->id] ?? 0;
            $ordersArray[] = $order;
        }

        return DataTables::of($ordersArray)
                ->addColumn('details', function ($order) {
                    $detailsCount = $order->details->count();
                    if ($order->details->isEmpty()) { 
                        $detailsHtml .= '<li>No details available</li>';
                    } else {
                        if ($detailsCount > 1) {
                            $detailsHtml = '<ul>';
                            foreach ($order->details as $detail) {
                                $detailsHtml .= '<li>' . $detail->product->name . '</li>';
                            }
                            $detailsHtml .= '</ul>';
                        } else {
                            if ($order->details->isEmpty()) {
                                $detailsHtml = '-';
                            } else {
                                $detail = $order->details->first();
                                $detailsHtml = $detail->product->name;
                            }
                        }
                    }

                    return $detailsHtml;
                })
                ->addColumn('statusProduct', function ($order) {
                    $detailsCount = $order->details->count();
                    if ($detailsCount > 1) {
                        $statusProductHtml = '<ul>';
                        foreach ($order->details as $detail) {
                            if ($detail->status_return != null) {
                                $statusProductHtml .= '<li><span class="font-weight-bold"> Return : ' . $detail->status_label_return . '</span></li>';
                            } else {
                                $statusProductHtml .= '<li>' . $detail->status_label . '</li>';
                            }
                        }
                        $statusProductHtml .= '</ul>';
                    } else {
                        if ($order->details->isEmpty()) {
                            $statusProductHtml = '-';
                        } else {
                            $detail = $order->details->first();
                            $statusProductHtml = $detail->status_return != null ? '<span class="font-weight-bold">Return : ' . $detail->status_label_return . '</span>' : $detail->status_label;
                        }
                    }
                    return $statusProductHtml;
                })
                ->addColumn('totalProduct', function ($order) {
                    if ($order->details->isEmpty()) {
                        return '<span>Rp 0</span>';
                    }
                    
                    $totalAmount = 0;
                    $shippingCost = 0;

                    $serviceCost = $order->service_cost;
                    $packagingCost = $order->details->groupBy('seller_id')->count() * 1000;
                
                    foreach ($order->details as $detail) {
                        $subtotal = $detail->price * $detail->qty;
                        // $totalAmount += $subtotal + $subtotal * 0.10; // 0.10 is tax
                        $totalAmount += $subtotal; // 0.10 is tax
                    }
                
                    // // Add shipping cost to the total amount
                    // $totalAmount += $shippingCost;
                    $totalAmount += $packagingCost + 1000;
                
                    return '<span>Rp ' . number_format($totalAmount, 0, ',', '.') . '</span>';
                })
                ->addColumn('formattedDate', function ($order) {
                    // $detailsCount = $order->details->count();
                    // if ($detailsCount > 1) {
                    //     $formattedDateHtml = '<ul>';
                    //     foreach ($order->details as $detail) {
                    //         $formattedDateHtml .= '<li>' . (isset($detail->formatted_transfer_date) ? $detail->formatted_transfer_date : '-') . '</li>';
                    //     }
                    //     $formattedDateHtml .= '</ul>';
                    // } else {
                        
                    // }
                    // $formattedDateHtml = $order->details->isEmpty() || !$order->details->first()->formatted_transfer_date ? '-' : $order->details->first()->formatted_transfer_date;
                    $formattedDateHtml = Carbon::parse($order->created_at)->locale('id')->translatedFormat('d M Y');
                    return $formattedDateHtml;
                })
                ->addColumn('action', function ($order) use (&$index) {
                    static $index = 0;
                    $index++;
                    return '
                        <a href="javascript:void(0);" class="btn btn-sm btn-primary view-order" data-index="'.$index.'" data-invoice="' . $order->invoice . '" title="Detail Invoice '. $order->invoice . '">
                            <span class="fa fa-eye"></span>
                        </a>
                        <button type="button" class="btn btn-sm btn-danger ml-1 delete-order" title="Hapus Invoice '. $order->invoice . '" data-order-id="' . $order->id . '"><span class="fa fa-trash"></span></button>

                        <form id="deleteForm' . $order->id . '" action="' . route('orders.newDestroy', $order->id) . '" method="post" class="d-none">
                            ' . method_field('DELETE') . csrf_field() . '
                        </form>
                    ';
                })
                ->rawColumns(['details', 'action', 'totalProduct', 'formattedDate', 'statusProduct'])
                ->make(true);
    }

    // order finish
    public function orderFinishIndex(){
        return view('seller.orders.orderfinish');
    }

    public function orderFinishDatatables(Request $request)
    {
        $ordersQuery = Order::with([
            'customer.district.city.province',
            'details' => function($q) {
                $q->where('seller_id', auth()->guard('seller')->user()->id);
            },
        ])->withCount('return')->orderBy('created_at', 'DESC');

        $orders = $ordersQuery->get();
        $orderIds = $orders->pluck('id')->toArray();

        $detailOrder = OrderDetail::whereIn('order_id', $orderIds)
            ->where('seller_id', auth()->guard('seller')->user()->id)
            ->where('status', 6)
            ->orderBy('created_at', 'DESC')
            ->get();

        $groups = $detailOrder->groupBy('order_id'); 

        $filteredOrders = $orders->filter(function($order) use ($groups) {
            return isset($groups[$order->id]);
        });

        $returns = OrderReturn::whereIn('order_id', $filteredOrders->pluck('id'))->where('status', 1)->get();
        $orderPayments = Payment::whereIn('order_id', $filteredOrders->pluck('id'))->where('status', 1)->get();

        $returnsGrouped = $returns->groupBy('order_id')->map(function ($group) {
            return $group->keyBy('product_id');
        });

        $orderPaymentGrouped = $orderPayments->groupBy('order_id')->map(function ($group) {
            return $group->keyBy('product_id');
        });

        // karena detail orders tidak ada relasi dengan payment, maka logic ini memaksa eloquent payment dibuat relasi dengan detail-order
        $details = $groups->map(function ($detailGroup) use ($returnsGrouped, $orderPaymentGrouped) {
            return $detailGroup->map(function ($detail) use ($returnsGrouped, $orderPaymentGrouped) {
                $statusReturn = null;
                $statusLabel = null;
                $statusPayment = null;
                $formattedDate = null;

                // return order
                if (isset($returnsGrouped[$detail->order_id]) && isset($returnsGrouped[$detail->order_id][$detail->product_id])) {
                    $return = $returnsGrouped[$detail->order_id][$detail->product_id];
                    $statusReturn = $return->status;
                    $statusLabel = $return->status_label;
                }

                // payment order
                if (isset($orderPaymentGrouped[$detail->order_id]) && isset($orderPaymentGrouped[$detail->order_id][$detail->product_id])) {
                    $payment = $orderPaymentGrouped[$detail->order_id][$detail->product_id];
                    $statusPayment = $payment->status;
                    $paymentArray = $payment->toArray();
                    unset($paymentArray['order_id']); 
                    $detail->payment = $paymentArray;

                    $carbonDate = Carbon::parse($payment->transfer_date);
                    $formattedDate = $carbonDate->translatedFormat('l, d F Y');
                    $translations = [
                        'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
                        'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
                        'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April',
                        'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
                        'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
                    ];

                    $formattedDate = str_replace(array_keys($translations), array_values($translations), $formattedDate);
                    $detail->formatted_transfer_date = $formattedDate; 
                }

                $detail->status_return = $statusReturn;
                $detail->status_label_return = $statusLabel;
                $detail->status_payment = $statusPayment;

                return $detail;
            });
        });

        $subtotals = [];
        $shippingCosts = [];

        foreach ($groups as $detail) {
            foreach($detail as $row){
                $orderId = $row->order_id;
                $subtotal = $row->qty * $row->price;
                $shippingCost = $row->shipping_cost;

                if (!isset($subtotals[$orderId])) {
                    $subtotals[$orderId] = 0;
                }
                if (!isset($shippingCosts[$orderId])) {
                    $shippingCosts[$orderId] = 0;
                }

                $subtotals[$orderId] += $subtotal;
                $shippingCosts[$orderId] += $shippingCost;
            }
        }

        $totalOmsets = [];
        foreach ($subtotals as $orderId => $subtotal) {
            $tax = $subtotal * 0.10;
            $shippingCost = $shippingCosts[$orderId];
            $totalOmset = $subtotal + $tax + $shippingCost;
            $totalOmsets[$orderId] = $totalOmset;
        }

        $ordersArray = [];
        foreach ($filteredOrders as $order) {
            $order->details = $details[$order->id] ?? collect();
            $order->total_omset = $totalOmsets[$order->id] ?? 0;
            $ordersArray[] = $order;
        }

        return DataTables::of($ordersArray)
                ->addColumn('details', function ($order) {
                    $detailsCount = $order->details->count();
                    if ($order->details->isEmpty()) { 
                        $detailsHtml .= '<li>No details available</li>';
                    } else {
                        if ($detailsCount > 1) {
                            $detailsHtml = '<ul>';
                            foreach ($order->details as $detail) {
                                $detailsHtml .= '<li>' . $detail->product->name . '</li>';
                            }
                            $detailsHtml .= '</ul>';
                        } else {
                            if ($order->details->isEmpty()) {
                                $detailsHtml = '-';
                            } else {
                                $detail = $order->details->first();
                                $detailsHtml = $detail->product->name;
                            }
                        }
                    }

                    return $detailsHtml;
                })
                ->addColumn('statusProduct', function ($order) {
                    $detailsCount = $order->details->count();
                    if ($detailsCount > 1) {
                        $statusProductHtml = '<ul>';
                        foreach ($order->details as $detail) {
                            if ($detail->status_return != null) {
                                $statusProductHtml .= '<li><span class="font-weight-bold"> Return : ' . $detail->status_label_return . '</span></li>';
                            } else {
                                $statusProductHtml .= '<li>' . $detail->status_label . '</li>';
                            }
                        }
                        $statusProductHtml .= '</ul>';
                    } else {
                        if ($order->details->isEmpty()) {
                            $statusProductHtml = '-';
                        } else {
                            $detail = $order->details->first();
                            $statusProductHtml = $detail->status_return != null ? '<span class="font-weight-bold">Return : ' . $detail->status_label_return . '</span>' : $detail->status_label;
                        }
                    }
                    return $statusProductHtml;
                })
                ->addColumn('totalProduct', function ($order) {
                    if ($order->details->isEmpty()) {
                        return '<span>Rp 0</span>';
                    }
                    
                    $totalAmount = 0;
                    $shippingCost = 0;

                    $serviceCost = $order->service_cost;
                    $packagingCost = $order->details->groupBy('seller_id')->count() * 1000;
                
                    foreach ($order->details as $detail) {
                        $subtotal = $detail->price * $detail->qty;
                        // $totalAmount += $subtotal + $subtotal * 0.10; // 0.10 is tax
                        $totalAmount += $subtotal; // 0.10 is tax
                    }
                
                    // // Add shipping cost to the total amount
                    // $totalAmount += $shippingCost;
                    $totalAmount += $packagingCost + 1000;
                
                    return '<span>Rp ' . number_format($totalAmount, 0, ',', '.') . '</span>';
                })
                ->addColumn('formattedDate', function ($order) {
                    // $detailsCount = $order->details->count();
                    // if ($detailsCount > 1) {
                    //     $formattedDateHtml = '<ul>';
                    //     foreach ($order->details as $detail) {
                    //         $formattedDateHtml .= '<li>' . (isset($detail->formatted_transfer_date) ? $detail->formatted_transfer_date : '-') . '</li>';
                    //     }
                    //     $formattedDateHtml .= '</ul>';
                    // } else {
                        
                    // }
                    // $formattedDateHtml = $order->details->isEmpty() || !$order->details->first()->formatted_transfer_date ? '-' : $order->details->first()->formatted_transfer_date;
                    $formattedDateHtml = Carbon::parse($order->created_at)->locale('id')->translatedFormat('d M Y');
                    return $formattedDateHtml;
                })
                ->addColumn('action', function ($order) use (&$index) {
                    static $index = 0;
                    $index++;
                    return '
                        <a href="javascript:void(0);" class="btn btn-sm btn-primary view-order" data-index="'.$index.'" data-invoice="' . $order->invoice . '" title="Detail Invoice '. $order->invoice . '">
                            <span class="fa fa-eye"></span>
                        </a>
                        <button type="button" class="btn btn-sm btn-danger ml-1 delete-order" title="Hapus Invoice '. $order->invoice . '" data-order-id="' . $order->id . '"><span class="fa fa-trash"></span></button>

                        <form id="deleteForm' . $order->id . '" action="' . route('orders.newDestroy', $order->id) . '" method="post" class="d-none">
                            ' . method_field('DELETE') . csrf_field() . '
                        </form>
                    ';
                })
                ->rawColumns(['details', 'action', 'totalProduct', 'formattedDate', 'statusProduct'])
                ->make(true);
    }

    public function view($invoice) 
    {
        if (Order::where('invoice', $invoice)->exists()){

            $order = Order::with([
                'customer.district.city.province', 
                'payment', 
                'details' => function($q) {
                    $q->where('seller_id', auth()->guard('seller')->user()->id);
                }
            ])->withCount('return')->where('invoice', $invoice)->first();
            $details = $order->details->where('seller_id', auth()->guard('seller')->user()->id);

            $orderIds = $details->pluck('order_id')->toArray();
            $orderProductIds = $details->pluck('product_id')->toArray();

            $returns = OrderReturn::whereIn('order_id', $orderIds)->whereIn('product_id', $orderProductIds)->get();

            $orderPayments = Payment::whereIn('order_id', $orderIds)->get();

            $returnsGrouped = $returns->groupBy('order_id')->map(function ($group) {
                return $group->keyBy('product_id');
            });

            $orderPaymentGrouped = $orderPayments->groupBy('order_id')->map(function ($group) {
                return $group->keyBy('product_id');
            });

            $details = $details->map(function ($detail) use ($returnsGrouped, $orderPaymentGrouped) {
                $statusReturn = null;
                $statusLabel = null;
                $statusPayment = null;
                $formattedDate = null;

                if (isset($returnsGrouped[$detail->order_id]) && isset($returnsGrouped[$detail->order_id][$detail->product_id])) {
                    $return = $returnsGrouped[$detail->order_id][$detail->product_id];
                    $statusReturn = $return->status;
                    $statusLabel = $return->status_label;
                    $returnArray = $return->toArray();
                    unset($returnArray['order_id']); 
                    $detail->return = $returnArray;
                }

                if (isset($orderPaymentGrouped[$detail->order_id]) && isset($orderPaymentGrouped[$detail->order_id][$detail->product_id])) {
                    $payment = $orderPaymentGrouped[$detail->order_id][$detail->product_id];
                    $statusPayment = $payment->status;
                    $paymentArray = $payment->toArray();
                    unset($paymentArray['order_id']); 
                    $detail->payment = $paymentArray;

                    // formatDate
                    $detail->formatted_transfer_date = Carbon::parse($payment->transfer_date)->locale('id')->translatedFormat('d F Y'); 
                }

                // assign status to detail
                $detail->status_return = $statusReturn;
                $detail->status_label_return = $statusLabel;
                $detail->status_payment = $statusPayment;

                return $detail;
            });
            
            // map value jika harga ongkir di setiap produknya sama pada satu order id
            $newCost = $details->pluck('shipping_cost')->first();

            $shippingCostSums = [];
            foreach ($details as $detail) {
                $sellerId = $detail['seller_id'];
                if (!isset($shippingCostSums[$sellerId])) {
                    $shippingCostSums[$sellerId] = 0;
                }
                $shippingCostSums[$sellerId] += $detail['shipping_cost'];
            }

            $shippingCost = array_sum($shippingCostSums);
            
            $subtotal = $details->sum(function($q){
                return $q['qty'] * $q['price'];
            });

            $tax = 0.10;
            $pajak = $subtotal * $tax;

            $total = collect([$subtotal, $pajak, $shippingCost])->pipe(function($q){
                return $q[0] + $q[1] + $q[2];
            });

            $formattedDate = null;

            if($order->payment){
                $formattedDate = Carbon::parse($order->payment->transfer_date)->locale('id')->translatedFormat('l, d F Y H:i:s');
            }

            return view('seller.orders.view', compact('order', 'details', 'subtotal', 'pajak', 'total', 'shippingCost', 'formattedDate', 'orderPayments'));
        }else {
            return redirect(route('orders.newIndex'))->with('error', 'Pesanan ini sudah tidak bisa diakses');
        }    
    }

    public function orderCancelIndex(){
        return view('seller.orders.ordercancel');
    }

    public function orderCancelDatatables(Request $request)
    {
        // seller id
        $sellerId = auth()->guard('seller')->user()->id;

        // Get cancelled orders with details filtered by seller ID
        $ordersCancelled = OrderCancelled::whereHas('details', function ($query) use ($sellerId) {
            $query->where('seller_id', $sellerId);
        })
        ->with(['details' => function ($query) use ($sellerId) {
            $query->where('seller_id', $sellerId);
        }])
        ->orderBy('created_at', 'DESC')
        ->get();

        return DataTables::of($ordersCancelled)
                ->addColumn('total', function($order){
                    if ($order->details->isEmpty()) {
                        return '<span>Rp 0</span>';
                    }
                    
                    $totalAmount = 0;
                    $shippingCost = 0;

                    $serviceCost = $order->service_cost;
                    $packagingCost = $order->details->groupBy('seller_id')->count() * 1000;
                
                    foreach ($order->details as $detail) {
                        $subtotal = $detail->price * $detail->qty;
                        // $totalAmount += $subtotal + $subtotal * 0.10; // 0.10 is tax
                        $totalAmount += $subtotal; // 0.10 is tax
                    }
                
                    // // Add shipping cost to the total amount
                    // $totalAmount += $shippingCost;
                    $totalAmount += $packagingCost + 1000;
                
                    return '<span>Rp ' . number_format($totalAmount, 0, ',', '.') . '</span>';
                })
                ->addColumn('details', function ($order) {
                    $detailsHtml = ''; 
                    $detailsCount = $order->details->count();
                    if ($order->details->isEmpty()) { 
                        $detailsHtml .= '<li>No details available</li>';
                    } else {
                        if ($detailsCount > 1) {
                            $detailsHtml = '<ul>';
                            foreach ($order->details as $detail) {
                                $detailsHtml .= '<li>' . $detail->product->name . '</li>';
                            }
                            $detailsHtml .= '</ul>';
                        } else {
                            if ($order->details->isEmpty()) {
                                $detailsHtml = '-';
                            } else {
                                $detail = $order->details->first();
                                $detailsHtml = $detail->product->name;
                            }
                        }
                    }

                    return $detailsHtml;
                })
                ->addColumn('status', function($order){
                    $detailsHtml = '';
                    $detailsCount = $order->details->count();
                    if ($order->details->isEmpty()) { 
                        $detailsHtml .= '<li>No details available</li>';
                    } else {
                        if ($detailsCount > 1) {
                            $detailsHtml = '<ul>';
                            foreach ($order->details as $detail) {
                                $detailsHtml .= '<li><span class="badge badge-danger">' . ($detail->status == 'cancel' ? 'Batal' : '') . '</span></li>';
                            }
                            $detailsHtml .= '</ul>';
                        } else {
                            if ($order->details->isEmpty()) {
                                $detailsHtml = '-';
                            } else {
                                $detail = $order->details->first();
                                $detailsHtml = '<span class="badge badge-danger">' . ($detail->status == 'cancel' ? 'Batal' : '') . '</span>';
                            }
                        }
                    }

                    return $detailsHtml;
                })
                ->addColumn('action', function ($order) use (&$index) {
                    static $index = 0;
                    $index++;
                    return '
                        <button type="button" class="btn btn-sm btn-danger ml-1 delete-order" title="Hapus Invoice '. $order->invoice . '" data-order-id="' . $order->id . '" data-invoice="' . $order->invoice . '"><span class="fa fa-trash"></span></button>

                        <form id="deleteForm' . $order->id . '" action="' . route('orders.cancelDelete', $order->id) . '" method="post" class="d-none">
                            ' . method_field('DELETE') . csrf_field() . '
                        </form>
                    ';
                })
                ->addColumn('formattedDate', function($order) {
                    return Carbon::parse($order->created_at)->locale('id')->translatedFormat('d M Y');
                })
                ->rawColumns(['total', 'details', 'status', 'formattedDate', 'action'])
                ->make(true);
    }

    public function acceptPayment($invoice, $product_id)
    {
        DB::beginTransaction();
        try {
            $order = Order::where('invoice', $invoice)->first();

            if (!$order) {
                return response()->json(['error' => 'Pesanan tidak ditemukan'], 404);
            }

            $payment = Payment::where('order_id', $order->id)
                ->where('product_id', $product_id)
                ->update(['status' => 1]);

            $orderDetailUpdated = OrderDetail::where('order_id', $order->id)
                ->where('product_id', $product_id)
                ->update([
                    'status' => 2,
                    'confirm_payment_date' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'),
                ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Pembayaran berhasil diterima'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function processOrder(Request $request)
    {
        $order = Order::with(['customer'])->find($request->order_id);

        if (!$order) {
            return response()->json(['error' => 'Pesanan tidak ditemukan'], 404);
        }

        // generate unik id untuk tracking number
        $uuid = (string) Str::uuid();
        $trackingNumber = 'TRX-' . strtoupper(substr($uuid, 0, 8));

        $orderDetail = OrderDetail::where('order_id', $order->id)
                    ->where('product_id', $request->product_id)
                    ->where('seller_id', auth()->guard('seller')->user()->id)
                    ->where('status', 2)
                    ->update([
                        'status' => 3,
                        'tracking_number' => $trackingNumber,
                        'process_date' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'),
                    ]);

        if ($orderDetail) {
            return response()->json(['success' => 'Berhasil diproses'], 200);
        } else {
            return response()->json(['error' => 'Pesanan tidak ditemukan atau sudah diproses'], 400);
        }
    }

    public function shippingOrder(Request $request)
    {
        $trackingNumber = $request->tracking_number;

        if (!$trackingNumber) {
            return response()->json(['error' => 'Nomor Faktur Diperlukan'], 400);
        }

        $order = Order::with(['customer'])->find($request->order_id);

        if (!$order) {
            return response()->json(['error' => 'Pesanan tidak ditemukan'], 404);
        }

        $productId = $request->product_id;
        $sellerId = auth()->guard('seller')->user()->id;

        $track = OrderDetail::where('tracking_number', $trackingNumber)->first();

        // if($trackingNumber && ($track->status == 4)){
        //     return response()->json(['error' => 'Nomor Resi ini sedang dalam pengiriman'], 404);
        // }

        if($track){
            ProcessShippingOrderJob::dispatchNow($order->id, $productId, $sellerId, $trackingNumber);

            return response()->json(['success' => 'Berhasil mengirim produk'], 200);
        } else {
            return response()->json(['error' => 'Nomor resi tidak ditemukan.'], 404);
        }
    }

    public function return($invoice, $product_id) 
    {
        if (Order::where('invoice', $invoice)->exists()){
            $order = Order::with(['return', 'customer'])->where('invoice', $invoice)->first();

            $detailOrder = OrderDetail::with(['product'])->where('order_id', $order->id)->where('product_id', $product_id)->first();
            $returns = OrderReturn::where('order_id', $order->id)->where('product_id', $product_id)->first();

            return view('seller.orders.return', compact('order', 'detailOrder', 'product_id', 'returns'));
        }else {
            return redirect(route('orders.newIndex'))->with('error', 'Pesanan ini sudah tidak bisa diakses kembali');
        }
    }

    public function approveReturn(Request $request)
    {
        try {
            $order = Order::find($request->order_id);

            $orderReturn = OrderReturn::where('order_id', $order->id)->where('product_id', $request->product_id)
                                    ->update(['status' => 1]);

            $orderDetail = OrderDetail::where('order_id', $order->id)
                                    ->where('product_id', $request->product_id)
                                    ->where('seller_id', auth()->guard('seller')->user()->id)
                                    ->update(['status' => 6]);

            $product = Product::where('id', $request->product_id)->increment('stock', $request->qty);

            return response()->json(['success' => 'Berhasil terima return'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function orderReport()
    {
        return view('seller.report.index');
    }

    // datatables for report order
    public function datatablesReport(Request $request)
    {
        $start = $request->query('start_date') ? Carbon::parse($request->query('start_date'))->startOfDay() : Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
        $end = $request->query('end_date') ? Carbon::parse($request->query('end_date'))->endOfDay() : Carbon::now()->endOfMonth()->format('Y-m-d H:i:s');

        $sellerId = auth()->guard('seller')->user()->id;

        $orders = Order::with([
            'customer.district',
            'details' => function($q) use ($sellerId, $start, $end) {
                $q->where('seller_id', $sellerId)
                ->where('status', 6)
                ->whereBetween('created_at', [$start, $end]);
            },
        ])
        ->whereHas('details', function($q) use ($sellerId, $start, $end) {
            $q->where('seller_id', $sellerId)
            ->where('status', 6)
            ->whereBetween('created_at', [$start, $end]);
        })
        ->whereBetween('created_at', [$start, $end])
        ->orderBy('created_at', 'DESC')
        ->get();

        $orderIds = $orders->pluck('id')->toArray();
        $detailOrder = OrderDetail::whereIn('order_id', $orderIds)
            ->where('seller_id', $sellerId)
            ->where('status', 6)
            ->orderBy('created_at', 'DESC')
            ->get();

        $groups = $detailOrder->groupBy('order_id'); 

        $filteredOrders = $orders->filter(function($order) use ($groups) {
            return isset($groups[$order->id]);
        });

        $returns = OrderReturn::whereIn('order_id', $filteredOrders->pluck('id'))->get();
        $orderPayments = Payment::whereIn('order_id', $filteredOrders->pluck('id'))->get();

        $returnsGrouped = $returns->groupBy('order_id')->map(function ($group) {
            return $group->keyBy('product_id');
        });

        $orderPaymentGrouped = $orderPayments->groupBy('order_id')->map(function ($group) {
            return $group->keyBy('product_id');
        });

        // karena detail orders tidak ada relasi dengan payment, maka logic ini memaksa eloquent payment dibuat relasi dengan detail-order
        $details = $groups->map(function ($detailGroup) use ($returnsGrouped, $orderPaymentGrouped) {
            return $detailGroup->map(function ($detail) use ($returnsGrouped, $orderPaymentGrouped) {
                $statusReturn = null;
                $statusLabel = null;
                $statusPayment = null;
                $formattedDate = null;

                // return order
                if (isset($returnsGrouped[$detail->order_id]) && isset($returnsGrouped[$detail->order_id][$detail->product_id])) {
                    $return = $returnsGrouped[$detail->order_id][$detail->product_id];
                    $statusReturn = $return->status;
                    $statusLabel = $return->status_label;
                }

                // payment order
                if (isset($orderPaymentGrouped[$detail->order_id]) && isset($orderPaymentGrouped[$detail->order_id][$detail->product_id])) {
                    $payment = $orderPaymentGrouped[$detail->order_id][$detail->product_id];
                    $statusPayment = $payment->status;
                    $paymentArray = $payment->toArray();
                    unset($paymentArray['order_id']); 
                    $detail->payment = $paymentArray;

                    $carbonDate = Carbon::parse($payment->transfer_date);
                    $formattedDate = $carbonDate->translatedFormat('l, d F Y');
                    $translations = [
                        'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
                        'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
                        'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April',
                        'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
                        'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
                    ];

                    $formattedDate = str_replace(array_keys($translations), array_values($translations), $formattedDate);
                    $detail->formatted_transfer_date = $formattedDate; 
                }

                $detail->status_return = $statusReturn;
                $detail->status_label_return = $statusLabel;
                $detail->status_payment = $statusPayment;

                return $detail;
            });
        });

        $subtotals = [];
        $shippingCosts = [];

        foreach ($groups as $detail) {
            foreach($detail as $row){
                $orderId = $row->order_id;
                $subtotal = $row->qty * $row->price;
                $shippingCost = $row->shipping_cost;

                if (!isset($subtotals[$orderId])) {
                    $subtotals[$orderId] = 0;
                }
                if (!isset($shippingCosts[$orderId])) {
                    $shippingCosts[$orderId] = 0;
                }

                $subtotals[$orderId] += $subtotal;
                $shippingCosts[$orderId] += $shippingCost;
            }
        }

        $totalOmsets = [];
        foreach ($subtotals as $orderId => $subtotal) {
            $tax = $subtotal * 0.10;
            $shippingCost = $shippingCosts[$orderId];
            $totalOmset = $subtotal + $tax + $shippingCost;
            $totalOmsets[$orderId] = $totalOmset;
        }

        $ordersArray = [];
        foreach ($filteredOrders as $order) {
            $order->details = $details[$order->id] ?? collect();
            $order->total_omset = $totalOmsets[$order->id] ?? 0;
            $ordersArray[] = $order;
        }

        return DataTables::of($orders)
            ->addColumn('details', function ($order) {
                $detailsCount = $order->details->count();
                if ($detailsCount > 1) {
                    $detailsHtml = '<ul>';
                    foreach ($order->details as $detail) {
                        $detailsHtml .= '<li>' . $detail->product->name . '</li>';
                    }
                    $detailsHtml .= '</ul>';
                } else {
                    $detailsHtml = $order->details->isEmpty() ? '-' : $order->details->first()->product->name;
                }
                return $detailsHtml;
            })
            ->addColumn('totalProduct', function ($order) {
                // $detailsCount = $order->details->count();
                // if ($detailsCount > 1) {
                //     $detailsProductHtml = '<ul>';
                //     foreach ($order->details as $detail) {
                //         // $amount = isset($detail->payment['amount']) ? $detail->payment['amount'] : 0;
                //         $amount = ($detail->qty * $detail->price + $detail->qty * $detail->price * 0.10 + $detail->shipping_cost) ?? 0;
                //         $detailsProductHtml .= '<li>Rp ' . number_format($amount, 0, ',', '.') . '</li>';
                //     }
                //     $detailsProductHtml .= '</ul>';
                // } else {
                //     // $amount = $order->details->isEmpty() ? 0 : (isset($order->details->first()->payment['amount']) ? $order->details->first()->payment['amount'] : 0);
                //     $amount = $order->details->first()->qty * $order->details->first()->price + $order->details->first()->qty * $order->details->first()->price + 0.10 + $order->details->first()->shipping_cost;
                //     $detailsProductHtml = 'Rp ' . number_format($amount, 0, ',', '.');
                // }
                // return $detailsProductHtml;

                if ($order->details->isEmpty()) {
                    return '<span>Rp 0</span>';
                }
                
                $totalAmount = 0;
                $shippingCost = 0;
            
                foreach ($order->details as $detail) {
                    $subtotal = $detail->price * $detail->qty;
                    // $totalAmount += $subtotal + ($subtotal * 0.10);
                    $totalAmount += $subtotal;
            
                    // Map shipping cost only once
                    if ($shippingCost == 0) {
                        $shippingCost = $detail->shipping_cost;
                    }
                }
            
                // Add shipping cost to the total amount
                // $totalAmount += $shippingCost;
            
                return '<span>Rp ' . number_format($totalAmount + 1000, 0, ',', '.') . '</span>';
            })
            ->addColumn('formattedDate', function ($order) {
                // $formattedDateHtml = $order->details->isEmpty() || !$order->details->first()->formatted_transfer_date ? '-' : $order->details->first()->formatted_transfer_date;
                // Get the first detail that has a formatted transfer date
                // $detail = $order->details->firstWhere('formatted_transfer_date', '!=', null);

                // Return the formatted transfer date if it exists, otherwise return a dash
                // return $detail ? $detail->formatted_transfer_date : '-';

                return Carbon::parse($order->created_at)->locale('id')->translatedFormat('d M Y');
            })
            ->addColumn('statusProduct', function ($order) {
                $detailsCount = $order->details->count();
                if ($detailsCount > 1) {
                    $statusProductHtml = '<ul>';
                    foreach ($order->details as $detail) {
                        if ($detail->status_return != null && $detail->status_return != 0) {
                            $statusProductHtml .= '<li><span class="font-weight-bold"> Return : ' . $detail->status_label_return . '</span></li>';
                        } else {
                            $statusProductHtml .= '<li>' . $detail->status_label . '</li>';
                        }
                    }
                    $statusProductHtml .= '</ul>';
                } else {
                    if ($order->details->isEmpty()) {
                        $statusProductHtml = '-';
                    } else {
                        $detail = $order->details->first();
                        $statusProductHtml = $detail->status_return != null && $detail->status_return != 0 ? '<span class="font-weight-bold"> Return : ' . $detail->status_label_return . '</span>' : $detail->status_label;
                    }
                }
                return $statusProductHtml;
            })
            ->rawColumns(['details', 'totalProduct', 'formattedDate', 'statusProduct']) // Ensure HTML is not escaped in action column
            ->make(true);
    }

    public function orderReportPdf($daterange)
    {
        try {
            $date = explode(' - ', $daterange);

            $start = Carbon::parse($date[0])->startOfDay()->format('Y-m-d H:i:s');
            $end = Carbon::parse($date[1])->endOfDay()->format('Y-m-d H:i:s');

            $sellerId = auth()->guard('seller')->user()->id;

            $orders = Order::with([
                'customer.district',
                'payment',
                'details' => function($q) use ($sellerId, $start, $end) {
                    $q->where('seller_id', $sellerId)
                    ->where('status', 6)
                    ->whereBetween('created_at', [$start, $end]);
                },
                'return' => function($q) use ($sellerId, $start, $end) {
                    $q->where('seller_id', $sellerId)
                    ->where('status', 1);
                }])
                ->whereHas('details', function($q) use ($sellerId, $start, $end) {
                    $q->where('seller_id', $sellerId)
                    ->where('status', 6)
                    ->whereBetween('created_at', [$start, $end]);
                })
                ->orderBy('created_at', 'DESC')
                ->get();

            $sellers = Seller::with(['district'])->where('id', auth()->guard('seller')->user()->id)->first();

            $startFormattedDate = Carbon::parse($start)->locale('id')->translatedFormat('l, d F Y');
            $endFormattedDate = Carbon::parse($end)->locale('id')->translatedFormat('l, d F Y');
            $formattedDate = $startFormattedDate . ' - ' . $endFormattedDate;

            $pdf = PDF::loadView('seller.report.orderpdf', compact('orders', 'formattedDate', 'sellers'));

            $startpdf = Carbon::parse($date[0])->locale('id')->translatedFormat('l, d F Y');
            $endpdf = Carbon::parse($date[1])->locale('id')->translatedFormat('l, d F Y');

            $fileName = 'Laporan Order Periode ' . $startpdf . ' sampai ' . $endpdf . '.pdf';
            $filePath = storage_path('app/public/reports/' . $fileName);

            // Save the PDF temporarily
            $pdf->save($filePath);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'PDF berhasil diunduh',
                    'file_url' => asset('storage/reports/' . $fileName)
                ], 200);
            }

            // Download the PDF directly
            return $pdf->download($fileName);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to generate PDF.', 'error' => $e->getMessage()], 500);
        }
    }

    public function exportExcelReport($daterange)
    {
        try {
            // Split the date range
            list($startDate, $endDate) = explode('+', $daterange);

            // Format dates for the file name
            $start = Carbon::parse($startDate)->locale('id')->translatedFormat('l, d F Y');
            $end = Carbon::parse($endDate)->locale('id')->translatedFormat('l, d F Y');

            // passing order total from here 
            $orders = Order::with([
                'customer',
                'details' => function($q) use ($startDate, $endDate) {
                    $q->where('seller_id', auth()->guard('seller')->user()->id)
                        ->where('status', 6)
                        ->whereBetween('created_at', [$startDate, $endDate]);
                },
                'return' => function($q) {
                    $q->where('seller_id', auth()->guard('seller')->user()->id)
                        ->where('status', 1);
                }
            ])
            ->whereHas('details', function($query) use ($startDate, $endDate) {
                $query->where('seller_id', auth()->guard('seller')->user()->id)
                    ->where('status', 6)
                    ->whereBetween('created_at', [$startDate, $endDate]); 
            })
            ->orderBy('created_at', 'DESC')
            ->get();

            $total = 0;
            foreach($orders as $order){
                $subtotal = 0;
                foreach($order->details as $detail){
                    $productReturn = $order->return->first(function ($return) use ($detail) {
                        return $return->order_id === $detail->order_id && $return->product_id === $detail->product_id;
                    });

                    $items = $detail->qty * $detail->price;

                    if($productReturn){
                        $subtotal += $items - $productReturn->refund_transfer;
                    } else {
                        $subtotal += $items;
                    }
                }

                $total += $subtotal;
            }

            $grandTotal = 'Rp ' . number_format($total, 0, ',', '.');

            // File name for the report
            $fileName = 'Laporan Pesanan Periode ' . $start . ' sampai ' . $end . '.xlsx';

            // Path to store the Excel file in storage/app/public/reports/excel
            $filePath = 'reports/excel/' . $fileName;

            // Generate the Excel report
            $export = new OrdersReportExport($startDate, $endDate, $grandTotal);

            // Check if the request is Ajax
            if (request()->ajax()) {
                // Store the file in the 'public/reports/excel' directory
                Excel::store($export, 'public/' . $filePath);

                // Return a JSON response for Ajax requests with the file URL
                return response()->json([
                    'success' => true,
                    'message' => 'Excel berhasil diunduh',
                    'file_url' => asset('storage/' . $filePath), // Public URL to access the file
                ], 200);
            }

            // For non-Ajax requests, return the file directly for download
            return Excel::download($export, $fileName);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to generate file.', 'error' => $e->getMessage()], 500);
        }
    }


    // return detail
    public function newReturnDetails($invoice, $productId)
    {
        // $invoice = $request->input('invoice');
        // $productId = $request->input('product_id');
        $sellerId = auth()->guard('seller')->user()->id;

        $order = Order::with(['customer'])->where('invoice', $invoice)->first();
        $detail = OrderDetail::with(['product'])->where('seller_id', $sellerId)->where('order_id', $order->id)->where('product_id', $productId)->first();
        $return = OrderReturn::with(['product'])->where('order_id', $order->id)->where('product_id', $productId)->where('seller_id', $sellerId)->first();

        return response()->json([
            'return_product' => $return->product->name,
            'return_tracking_number' => '#' . $return->tracking_number,
            'return_date' => Carbon::parse($return->created_at)->locale('id')->translatedFormat('l, d F Y'),
            'customer_name' => $order->customer_name,
            'customer_phone' => $order->customer_phone,
            'customer_email' => $order->customer->email,
            'customer_address' => $order->customer->address . ', Kecamatan ' . $order->customer->district->name . ', Kota ' . $order->customer->district->city->name . ', ' . $order->customer->district->city->province->name,
            'return' => $return,
            'refund_amount' => 'Rp ' . number_format($return->refund_transfer, 0, ',', '.'),
            'status_label' => $return->status_label,
            'order_id' => $order->id,
            'product_id' => $productId,
            'product_photo' => asset('/products/' . $return->product->image),
            'product_qty' => $return->qty . ' item',
            'return_photo' => asset('/proof/return/' . $return->photo),
        ]);
    }

    public function returnReport()
    {
        return view('seller.report.return');
    }

    public function datatablesReportReturn(Request $request)
    {
        // $start = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
        // $end = Carbon::now()->endOfMonth()->format('Y-m-d H:i:s');
        $start = $request->query('start_date') ? Carbon::parse($request->query('start_date'))->startOfDay() : Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
        $end = $request->query('end_date') ? Carbon::parse($request->query('end_date'))->endOfDay() : Carbon::now()->endOfMonth()->format('Y-m-d H:i:s');

        $orders = Order::with([
            'customer.district',
            'return' => function($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end])
                  ->where('seller_id', auth()->guard('seller')->user()->id)
                  ->where('status', 1);
            }    
        ])
        ->whereHas('return', function($query) use ($start, $end) {
            $query->whereBetween('created_at', [$start, $end])
                  ->where('seller_id', auth()->guard('seller')->user()->id)
                  ->where('status', 1);
        })
        ->orderBy('created_at', 'DESC')
        ->get(); 

        return DataTables::of($orders)
            ->addColumn('invoice', function ($order) {
                return $order->invoice;
            })

            ->addColumn('product', function ($order) {
                $returnCount = $order->return->count();
                if ($returnCount > 1) {
                    $returnHtml = '<ul>';
                    foreach ($order->return as $return) {
                        $returnHtml .= '<li>' . $return->product->name . '</li>';
                    }
                    $returnHtml .= '</ul>';
                } else {
                    $returnHtml = $order->return->isEmpty() ? '-' : $order->return->first()->product->name;
                }
                return $returnHtml;
            })

            ->addColumn('reason', function ($order) {
                $returnCount = $order->return->count();
                if ($returnCount > 1) {
                    $reasonsHtml = '<ul>';
                    foreach ($order->return as $return) {
                        $reasonsHtml .= '<li>' . ($return->reason ?? '-') . '</li>';
                    }
                    $reasonsHtml .= '</ul>';
                } else {
                    $reasonsHtml = $order->return->isEmpty() ? '-' : $order->return->first()->reason;
                }
                return $reasonsHtml;
            })
            
            // ->addColumn('statusProduct', function ($order) {
            //     $statusCount = $order->return->count();
            //     if ($statusCount > 1) {
            //         $statusHtml = '<ul>';
            //         foreach ($order->return as $return) {
            //             $statusHtml .= '<li>' . ($return->status_label ?? '-') . '</li>';
            //         }
            //         $statusHtml .= '</ul>';
            //     } else {
            //         $statusHtml = $order->return->isEmpty() ? '-' : $order->return->first()->status_label;
            //     }
            //     return $statusHtml;
            // })

            ->addColumn('totalRefund', function ($order) {
                $refundCount = $order->return->count();
                if ($refundCount > 1) {
                    $totalRefund = '<ul>';
                    foreach ($order->return as $return) {
                        $amount = $return->refund_transfer ?? 0;
                        $totalRefund .= '<li> Rp ' . number_format($amount, 0, ',', '.') . '</li>';
                    }
                    $totalRefund .= '</ul>';
                } else {
                    $amount = $order->return->isEmpty() ? 0 : $order->return->first()->refund_transfer;
                    $totalRefund = 'Rp ' . number_format($amount, 0, ',', '.');
                }

                return $totalRefund;
            })

            ->addColumn('formattedReturnDate', function ($order) {
                $dateCount = $order->return->count();
                if ($dateCount > 1) {
                    $datesHtml = '<ul>';
                    foreach ($order->return as $return) {
                        $carbonDate = Carbon::parse($return->created_at);
                        $formattedDate = $carbonDate->translatedFormat('d M Y');
                        $translations = [
                            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
                            'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
                            'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April',
                            'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
                            'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
                        ];
                        $formattedDate = str_replace(array_keys($translations), array_values($translations), $formattedDate);
                        $datesHtml .= '<li>' . $formattedDate . '</li>';
                    }
                    $datesHtml .= '</ul>';
                } else {
                    $datesHtml = $order->return->isEmpty() ? '-' : Carbon::parse($order->return->first()->created_at)
                        ->locale('id')
                        ->translatedFormat('d M Y');
                }
                return $datesHtml;
            })

            ->rawColumns(['reason', 'statusProduct', 'formattedReturnDate', 'totalRefund', 'invoice', 'product'])
            ->make(true);
    }

    public function returnReportPdf($daterange)
    {
        try {
            $date = explode('+', $daterange);
            $start = Carbon::parse($date[0])->format('Y-m-d') . ' 00:00:01';
            $end = Carbon::parse($date[1])->format('Y-m-d') . ' 23:59:59';

            $orders = Order::with([
                'customer',
                'return' => function($q) use ($start, $end) {
                        $q->whereBetween('created_at', [$start, $end])
                        ->where('seller_id', auth()->guard('seller')->user()->id)
                        ->where('status', 1);
                }
            ])
            ->whereHas('return', function($query) use ($start, $end) {
                $query->whereBetween('created_at', [$start, $end])
                    ->where('seller_id', auth()->guard('seller')->user()->id)
                    ->where('status', 1);
            })
            ->orderBy('created_at', 'DESC')
            ->get();

            $returns = OrderReturn::with(['product'])
                    ->where('seller_id', auth()->guard('seller')->user()->id)
                    ->where('status', 1)
                    ->whereBetween('created_at', [$start, $end])
                    ->orderBy('created_at', 'DESC')
                    ->get();

            $orderIds = $returns->keys();
            $productIds = $returns->flatten()->pluck('product_id')->unique();

            $details = OrderDetail::with(['product'])
                    ->whereIn('order_id', $orderIds)
                    ->whereIn('product_id', $productIds)
                    ->get()
                    ->groupBy('order_id');

            $orders = $orders->map(function ($order) use ($returns, $details) {
                $order->returns = $returns->get($order->id, collect());
                $order->details = $details->get($order->id, collect());
                return $order;
            });

            $dates = collect($date)->map(function ($item, $key) {
                return date("D, d F Y", strtotime($item));
            })->all();

            $dayMapping = [
                'Mon' => 'Senin',
                'Tue' => 'Selasa',
                'Wed' => 'Rabu',
                'Thu' => 'Kamis',
                'Fri' => 'Jumat',
                'Sat' => 'Sabtu',
                'Sun' => 'Minggu'
            ];

            $monthMapping = [
                'January' => 'Januari',
                'February' => 'Februari',
                'March' => 'Maret',
                'April' => 'April',
                'May' => 'Mei',
                'June' => 'Juni',
                'July' => 'Juli',
                'August' => 'Agustus',
                'September' => 'September',
                'October' => 'Oktober',
                'November' => 'November',
                'December' => 'Desember'
            ];

            function translateDate($date, $dayMapping, $monthMapping) {
                $parts = explode(' ', $date);
                $day = str_replace(array_keys($dayMapping), array_values($dayMapping), rtrim($parts[0], ','));
                $month = str_replace(array_keys($monthMapping), array_values($monthMapping), $parts[2]);
                
                return "$day, $parts[1] $month $parts[3]";
            }

            $formattedDate = array_map(function($date) use ($dayMapping, $monthMapping) {
                return translateDate($date, $dayMapping, $monthMapping);
            }, $dates);

            // Seller > get address, district, city, province
            $sellers = auth()->guard('seller')->user()->load('district');
            $districts = District::where('id', $sellers->district->id)->first();
            $cities = City::where('id', $sellers->district->city_id)->first();
            $provinces = Province::where('id', $sellers->district->province_id)->first();

            // Customer > get address, district, city, province
            $customerIds = $orders->pluck('customer_id')->toArray();

            $customers = Customer::whereIn('id', $customerIds)->get();
            $customerPhones = $customers->pluck('phone_number', 'id')->toArray();
            $customerNames = $customers->pluck('name', 'id')->toArray();
            $customerAddress = $customers->pluck('address', 'id')->toArray();
            $custDistrictIds = $customers->pluck('district_id')->toArray();

            $custDistricts = District::whereIn('id', $custDistrictIds)->get();
            $custDistrictNames = $districts->pluck('name', 'id')->toArray();
            $custCityIds = $districts->pluck('city_id')->toArray();

            $custCities = City::whereIn('id', $custCityIds)->get();
            $custCityNames = $cities->pluck('name', 'id')->toArray();
            $custProvinceIds = $cities->pluck('province_id')->toArray();;

            $custProvinces = Province::whereIn('id', $custProvinceIds)->get();
            $custProvinceNames = $cities->pluck('name', 'id')->toArray();

            $combinedInfo = [];
            foreach ($customers as $customer) {
                $district = $districts->where('id', $customer->district_id)->first();
                $city = $cities->where('id', $district->city_id)->first();
                $province = $provinces->where('id', $city->province_id)->first();

                $combinedInfo[] = [
                    'name' => $customer->name,
                    'phone' => $customer->phone_number,
                    'address' => $customer->address,
                    'district' => $district->name,
                    'city' => $city->name,
                    'province' => $province->name,
                ];
            }

            // transform created_at
            $orders->transform(function($order) {
                // $order->formatted_date = Carbon::parse($order->created_at)->translatedFormat('l, d F Y');
                $carbonDate = Carbon::parse($order->created_at);
                $formattedDate = $carbonDate->translatedFormat('l, d F Y');
                $translations = [
                    'Sunday' => 'Minggu',
                    'Monday' => 'Senin',
                    'Tuesday' => 'Selasa',
                    'Wednesday' => 'Rabu',
                    'Thursday' => 'Kamis',
                    'Friday' => 'Jumat',
                    'Saturday' => 'Sabtu',
                    'January' => 'Januari',
                    'February' => 'Februari',
                    'March' => 'Maret',
                    'April' => 'April',
                    'May' => 'Mei',
                    'June' => 'Juni',
                    'July' => 'Juli',
                    'August' => 'Agustus',
                    'September' => 'September',
                    'October' => 'Oktober',
                    'November' => 'November',
                    'December' => 'Desember'
                ];

                $formattedDate = str_replace(array_keys($translations), array_values($translations), $formattedDate);

                // assign
                $order->formatted_date = $formattedDate;

                return $order;
            });

            // Calculate total refund transfer
            $resultTotal = OrderReturn::with(['product'])
                ->where('seller_id', auth()->guard('seller')->user()->id)
                ->where('status', 1)
                ->whereBetween('created_at', [$start, $end])
                ->sum('refund_transfer');

            $pdf = PDF::loadView('seller.report.returnpdf', compact('orders', 'resultTotal', 'combinedInfo', 'formattedDate', 'sellers', 'districts', 'cities', 'provinces'));
            
            $startpdf = Carbon::parse($date[0])->locale('id')->translatedFormat('l, d F Y');
            $endpdf = Carbon::parse($date[1])->locale('id')->translatedFormat('l, d F Y');

            $fileName = 'Laporan Order Return Periode ' . $startpdf . ' sampai ' . $endpdf . '.pdf';
            $filePath = storage_path('app/public/reportsreturn/' . $fileName);

            // Save the PDF temporarily
            $pdf->save($filePath);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'PDF berhasil diunduh',
                    'file_url' => asset('storage/reportsreturn/' . $fileName)
                ], 200);
            }

            // Download the PDF directly
            return $pdf->download($fileName);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to generate PDF.', 'error' => $e->getMessage()], 500);
        }
    }

    public function exportExcelReportReturn($daterange)
    {
        // list($startDate, $endDate) = explode('+', $daterange);

        // $start = Carbon::parse($startDate)->locale('id')->translatedFormat('l, d F Y');
        // $end = Carbon::parse($endDate)->locale('id')->translatedFormat('l, d F Y');

        // return Excel::download(new ($startDate, $endDate), ' ' . $start . ' sampai ' . $end . '.xlsx');

        try {
            list($startDate, $endDate) = explode('+', $daterange);

            $start = Carbon::parse($startDate)->locale('id')->translatedFormat('l, d F Y');
            $end = Carbon::parse($endDate)->locale('id')->translatedFormat('l, d F Y');

            $orders = Order::with([
                'customer',
                'return' => function($q) use ($startDate, $endDate) {
                    $q->where('seller_id', auth()->guard('seller')->user()->id)
                        ->where('status', 1)
                        ->whereBetween('created_at', [$startDate, $endDate]);
                }
            ])
            ->whereHas('return', function($query) use ($startDate, $endDate) {
                $query->where('seller_id', auth()->guard('seller')->user()->id)
                    ->where('status', 1)
                    ->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->get();

            $total = 0;
            foreach($orders as $order){
                foreach($order->return as $return){
                    $total += $return->refund_transfer;
                }
            }

            $totalSum = 'Rp ' . number_format($total, 0, ',', '.');

            $fileName = 'Laporan Pengembalian Pesanan Periode ' . $start . ' sampai ' . $end . '.xlsx';

            $filePath = 'reportsreturn/excel/' . $fileName;
            $export = new OrdersReturnExport($startDate, $endDate, $totalSum);

            if (request()->ajax()) {
                Excel::store($export, 'public/' . $filePath);

                return response()->json([
                    'success' => true,
                    'message' => 'Excel berhasil diunduh',
                    'file_url' => asset('storage/' . $filePath),
                ], 200);
            }

            return Excel::download($export, $fileName);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to generate file.', 'error' => $e->getMessage()], 500);
        }
    }

    // delete order
    public function destroy($id)
    {
        $order = Order::find($id);

        if ($order) {
            $details = OrderDetail::where('order_id', $id)->delete();
            $returns = OrderReturn::where('order_id', $id)->delete();
            $order->delete();
        }

        if (!$order) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Pesanan Tidak Ditemukan'], 404);
            }
        }

        if (request()->ajax()) {
            return response()->json(['success' => 'Pesanan berhasil dihapus'], 200);
        } else {
            return response()->json(['error' => 'Pesanan Gagal Dihapus']);
        }

        // return redirect(route('orders.newIndex'))->with(['success' => 'Order Sudah Dihapus']);
    }

    // delete order-cancel
    public function destroyOrderCancel($id)
    {
        $order = OrderCancelled::find($id);

        if ($order) {
            $details = OrderCancelledDetail::where('order_id', $id)->delete();
            $order->delete();
        }

        if (!$order) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Pesanan Tidak Ditemukan'], 404);
            }
        }

        if (request()->ajax()) {
            return response()->json(['success' => 'Pesanan berhasil dihapus'], 200);
        } else {
            return response()->json(['error' => 'Pesanan Gagal Dihapus'], 500);
        }

        // return redirect(route('orders.newIndex'))->with(['success' => 'Order Sudah Dihapus']);
    }

    public function incomeIndex()
    {   
        // seller id
        $sellerId = auth()->guard('seller')->user()->id;

        // kalkulasi pendapatan
        $ongoing = OrderDetail::where('seller_id', $sellerId)->whereIn('status', [2,3,4,5])->orderBy('created_at', 'DESC')->sum(DB::raw('qty * price'));
        $finish = OrderDetail::where('seller_id', $sellerId)->where('status', 6)->orderBy('created_at', 'DESC')->sum(DB::raw('qty * price'));
        $cancel = OrderCancelledDetail::where('seller_id', $sellerId)->orderBy('created_at', 'DESC')->sum(DB::raw('qty * price'));

        // count pesanan
        $ongoingCount = OrderDetail::where('seller_id', $sellerId)->whereIn('status', [2,3,4,5])->count();
        $finishCount = OrderDetail::where('seller_id', $sellerId)->where('status', 6)->count();
        $cancelCount = OrderCancelledDetail::where('seller_id', $sellerId)->count();

        return view('seller.orders.income', compact('ongoing', 'finish', 'cancel', 'ongoingCount', 'finishCount', 'cancelCount'));
    }

    public function incomeOnGoing(Request $request)
    {
        $sellerId = auth()->guard('seller')->user()->id;
        $income = OrderDetail::where('seller_id', $sellerId)->whereIn('status', [2,3,4,5])->orderBy('created_at', 'DESC')->get();

        return DataTables::of($income)
            ->editColumn('productName', function($income) {
                return $income->product->name;
            })
            ->editColumn('status', function($income) {
                return $income->status_label;
            })
            ->editColumn('qty', function($income) {
                return $income->qty . ' item';
            })
            ->editColumn('price', function($income) {
                return 'Rp ' . number_format($income->price, 0, ',', '.');
            })
            ->addColumn('subtotal', function($income){
                return 'Rp ' . number_format($income->price * $income->qty, 0, ',', '.');
            })
            ->editColumn('tracking_number', function($income) {
                $trackingNumber = $income->tracking_number != null ? '<span class="font-weight-bold">#' . $income->tracking_number . '</span>' : '';
                return $trackingNumber;
            })
            ->editColumn('shipping_service', function($income) {
                return $income->shipping_service ?? '-';
            })
            ->rawColumns(['productName', 'status', 'qty', 'price', 'subtotal', 'tracking_number', 'shipping_service'])
            ->make(true);
    }

    public function incomeFinish(Request $request)
    {
        $sellerId = auth()->guard('seller')->user()->id;
        $income = OrderDetail::where('seller_id', $sellerId)->where('status', 6)->orderBy('created_at', 'DESC')->get();

        return DataTables::of($income)
            ->editColumn('productName', function($income) {
                return $income->product->name;
            })
            ->editColumn('status', function($income) {
                return $income->status_label;
            })
            ->editColumn('qty', function($income) {
                return $income->qty . ' item';
            })
            ->editColumn('price', function($income) {
                return 'Rp ' . number_format($income->price, 0, ',', '.');
            })
            ->addColumn('subtotal', function($income){
                return 'Rp ' . number_format($income->price * $income->qty, 0, ',', '.');
            })
            ->editColumn('tracking_number', function($income) {
                return '<span class="font-weight-bold">#' . $income->tracking_number . '</span>';
            })
            ->editColumn('shipping_service', function($income) {
                return $income->shipping_service ?? '-';
            })
            ->rawColumns(['productName', 'status', 'qty', 'price', 'subtotal', 'tracking_number', 'shipping_service'])
            ->make(true);
    }

    public function incomeReturn(Request $request)
    {
        $sellerId = auth()->guard('seller')->user()->id;
        $return = OrderReturn::where('seller_id', $sellerId)->orderBy('created_at', 'DESC')->get();

        return DataTables::of($return)
            ->editColumn('productName', function($return) {
                return $return->product->name;
            })
            ->editColumn('status', function($return) {
                return $return->status_label;
            })
            ->editColumn('qty', function($return) {
                return $return->qty . ' item';
            })
            ->editColumn('reason', function($return) {
                return $return->reason;
            })
            ->editColumn('refund_transfer', function($return) {
                return 'Rp ' . number_format($return->refund_transfer, 0, ',', '.');
            })
            ->editColumn('tracking_number', function($return){
                return '<span class="font-weight-bold">#' . $return->tracking_number . '</span>';
            })
            ->rawColumns(['productName', 'status', 'qty', 'refund_transfer', 'tracking_number'])
            ->make(true);
    }

    public function incomeCancel(Request $request)
    {
        $sellerId = auth()->guard('seller')->user()->id;
        $income = OrderCancelledDetail::where('seller_id', $sellerId)->orderBy('created_at', 'DESC')->get();

        return DataTables::of($income)
            ->editColumn('productName', function($income) {
                return $income->product->name;
            })
            ->editColumn('status', function($income) {
                $status = '<span class="badge badge-danger">' . ($income->status == 'cancel' ? 'Batal' : '') .'</span>';
                return $status;
            })
            ->editColumn('qty', function($income) {
                return $income->qty . ' item';
            })
            ->editColumn('price', function($income) {
                return 'Rp ' . number_format($income->price, 0, ',', '.');
            })
            ->addColumn('subtotal', function($income){
                return 'Rp ' . number_format($income->price * $income->qty, 0, ',', '.');
            })
            ->addColumn('shipping_service', function($income){
                return $income->shipping_service;
            })
            ->rawColumns(['productName', 'status', 'qty', 'price', 'subtotal', 'shipping_service'])
            ->make(true);
    }
}
