<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\OrderDetail;
use App\OrderReturn;
use App\Payment;
use App\Mail\OrderMail;
use Mail;
use Carbon\Carbon;
use PDF;
use DataTables;

class OrderController extends Controller
{
    public function index() 
    {
        return view('admin.orders.index');   
    }

    public function ordersGetDatatables(Request $request)
    {
        $ordersQuery = Order::with(['customer.district.city.province'])->withCount('return')->orderBy('created_at', 'DESC');

        $orders = $ordersQuery->get();
        $orderIds = $orders->pluck('id')->toArray();

        $detailOrder = OrderDetail::whereIn('order_id', $orderIds)->orderBy('created_at', 'DESC')->get();

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
                            if ($detail->status_return != null && $detail->status_return != 0) {
                                $statusProductHtml .= '<li><span class="font-weight-bold">Return : ' . $detail->status_label_return . '<span></li>';
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
                            $statusProductHtml = $detail->status_return != null && $detail->status_return != 0 ? '<span class="font-weight-bold">Return : </span>' . $detail->status_label_return : $detail->status_label;
                        }
                    }
                    return $statusProductHtml;
                })
                ->addColumn('totalProduct', function ($order) {
                    // if ($order->details->isEmpty()) {
                    //     return '<span>Rp 0</span>';
                    // }
                
                    // $detailsProductHtml = '';
                    // $detailsCount = $order->details->count(); // Count the number of details
                
                    // foreach ($order->details as $index => $detail) {
                    //     $amount = ($detail->qty * $detail->price + $detail->qty * $detail->price * 0.10 + $detail->shipping_cost);
                        
                    //     // Conditionally add the index based on the number of details
                    //     if ($detailsCount > 1) {
                    //         $detailsProductHtml .= '<span>' . ($index + 1) . '. ' . 'Rp ' . number_format($amount, 0, ',', '.') . '</span>' . '<br>';
                    //     } else {
                    //         $detailsProductHtml .= '<span>Rp ' . number_format($amount, 0, ',', '.') . '</span>' . '<br>';
                    //     }
                    // }
                
                    // return $detailsProductHtml;

                    // new
                    if ($order->details->isEmpty()) {
                        return '<span>Rp 0</span>';
                    }
                    
                    $totalAmount = 0;
                    $shippingCost = 0;

                    $serviceCost = $order->service_cost;
                    $packagingCost = $order->details->groupBy('seller_id')->count() * 1000;
                    $shippingCost = $order->details->unique('seller_id')->sum('shipping_cost');
                
                    foreach ($order->details as $detail) {
                        $subtotal = $detail->price * $detail->qty;
                        // $totalAmount += $subtotal + $subtotal * 0.10; // 0.10 is tax
                        $totalAmount += $subtotal; // 0.10 is tax
                    }
                
                    // // Add shipping cost to the total amount
                    // $totalAmount += $shippingCost;
                    $totalAmount += $packagingCost + $serviceCost;
                
                    return '<span>Rp ' . number_format($totalAmount, 0, ',', '.') . '</span>';
                })
                ->addColumn('formattedDate', function ($order) {
                    // $formattedDate = '';
                    // foreach ($order->details as $index => $detail) {
                    //     if (isset($detail->formatted_transfer_date)) {
                    //         // Add index if there are multiple details
                    //         if ($order->details->count() > 1) {
                    //             $formattedDate .= ($index + 1) . '. ';
                    //         }
                    //         $formattedDate .= $detail->formatted_transfer_date . '<br>';
                    //     } else {
                    //         $formattedDate .= '-<br>';
                    //     }
                    // }

                    // return rtrim($formattedDate, '<br>');

                    $formattedDateHtml = Carbon::parse($order->created_at)->locale('id')->translatedFormat('d M Y');
                    return $formattedDateHtml;
                })
                ->addColumn('total_omset', function ($order) {
                    return $order->total_omset;
                })
                ->addColumn('action', function ($order) use (&$index) {
                    static $index = 0;
                    $index++;

                    return '
                        <button type="button" class="btn btn-sm btn-primary detail-order" data-index="'.$index.'" data-order-invoice="'.  $order->invoice .'" title="Detail Invoice ' .  $order->invoice . '"><i class="fa fa-eye"></i></button>
                    ';
                })
                ->rawColumns(['details', 'action', 'totalProduct', 'formattedDate', 'statusProduct']) // Ensure HTML is not escaped in action column
                ->make(true);
    }


    public function view($invoice) 
    {
        if (Order::where('invoice', $invoice)->exists()){

            $order = Order::with(['customer.district.city.province', 'payment', 'details.product'])->withCount('return')->where('invoice', $invoice)->first();

            $details = $order->details->where('order_id', $order->id);

            // Fetch returns based on the order IDs in the detail orders
            $orderIds = $details->pluck('order_id')->toArray();
            $orderProductIds = $details->pluck('product_id')->toArray();

            // Retrieve returns
            $returns = OrderReturn::whereIn('order_id', $orderIds)->whereIn('product_id', $orderProductIds)->get();

            // Retrieve payments
            $orderPayments = Payment::whereIn('order_id', $orderIds)->get();

            // Group returns by order_id and product_id
            $returnsGrouped = $returns->groupBy('order_id')->map(function ($group) {
                return $group->keyBy('product_id');
            });

            // Group payments by order_id
            $orderPaymentGrouped = $orderPayments->groupBy('order_id')->map(function ($group) {
                return $group->keyBy('product_id');
            });

            // Combine payment information with details
            $details = $details->map(function ($detail) use ($returnsGrouped, $orderPaymentGrouped) {
                $statusReturn = null;
                $statusLabel = null;
                $statusPayment = null;
                $formattedDate = null;

                // Check if return exists for this detail
                if (isset($returnsGrouped[$detail->order_id]) && isset($returnsGrouped[$detail->order_id][$detail->product_id])) {
                    $return = $returnsGrouped[$detail->order_id][$detail->product_id];
                    $statusReturn = $return->status;
                    $statusLabel = $return->status_label;
                }

                if (isset($orderPaymentGrouped[$detail->order_id]) && isset($orderPaymentGrouped[$detail->order_id][$detail->product_id])) {
                    $payment = $orderPaymentGrouped[$detail->order_id][$detail->product_id];
                    $statusPayment = $payment->status;
                    $paymentArray = $payment->toArray();
                    unset($paymentArray['order_id']); 
                    $detail->payment = $paymentArray;

                    // format tanggal
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

                // Assign statuses to detail
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
                // Create a Carbon instance
                $carbonDate = Carbon::parse($order->payment->transfer_date);

                // Format the date
                $formattedDate = $carbonDate->translatedFormat('l, d F Y');
                
                // Custom translations for day and month names
                $translations = [
                    'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
                    'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
                    'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April',
                    'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
                    'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
                ];

                $formattedDate = str_replace(array_keys($translations), array_values($translations), $formattedDate);
            }

            return view('admin.orders.view', compact('order', 'details', 'subtotal', 'pajak', 'total', 'shippingCost', 'formattedDate', 'orderPayments'));
        }else {
            return redirect()->back();
        }    
    }

    public function showOrder($invoice)
    {
        try {
            $order = Order::with(['customer', 'details.product', 'return', 'payment'])
                ->where('invoice', $invoice)
                ->first();

            if (!$order) {
                return response()->json([
                    'message' => 'Order not found'
                ], 404);
            }

            // prepare array
            $products = [];

            $subtotal = 0;
            $serviceCost = $order->service_cost;
            $packagingCost = $order->service_cost;
            $shippingCost = $order->details->unique('seller_id')->sum('shipping_cost');
            $grandTotal = 0;

            foreach($order->details as $detail){
                // convert berat
                $weight = $detail->weight;
                if (strpos($weight, '-') !== false) {
                    // If the weight is a range, split it into an array
                    $weights = explode('-', $weight);
                    $minWeight = (float) trim($weights[0]);
                    $maxWeight = (float) trim($weights[1]);

                    // Check if the weights are >= 1000 to display in Kg
                    $minWeightDisplay = $minWeight >= 1000 ? ($minWeight / 1000) : $minWeight;
                    $maxWeightDisplay = $maxWeight >= 1000 ? ($maxWeight / 1000) . ' Kg' : $maxWeight . ' gram / pack';

                    // Construct the display string
                    $weightDisplay = $minWeightDisplay . ' - ' . $maxWeightDisplay;
                } else {
                    // Single weight value
                    $weightDisplay = $weight >= 1000 ? ($weight / 1000) . ' Kg' : $weight . ' gram / pack';
                }

                // return
                $productReturn = $order->return->first(function ($return) use ($detail) {
                    return $return->order_id === $detail->order_id && $return->product_id === $detail->product_id;
                });

                // put array products
                $products[] = [
                    'image' => asset('/products/' . $detail->product->image),
                    'name' => $detail->product->name,
                    'price' => 'Rp ' . number_format($detail->price, 0, ',', '.'),
                    'qty' => $detail->qty . ' item',
                    'weight' => $weightDisplay,
                    'status' => $productReturn->status_label ?? $detail->status_label,
                    'service' => $detail->shipping_service,
                    'subtotal' => 'Rp ' . number_format($detail->price * $detail->qty, 0, ',', '.'),
                ];

                $items = $detail->qty * $detail->price;
                $subtotal += $items;
            }

            // hitung total
            $grandTotal += $shippingCost + $subtotal + $serviceCost + $packagingCost;
            
            return response()->json([
                'orders' => $order, 
                'invoice' => $order->invoice,
                'customer_name' => $order->customer->name,
                'customer_email' => $order->customer->email,
                'customer_phone' => $order->customer->phone_number,
                'customer_address' => $order->customer->address,
                'district' => $order->customer->district->name,
                'city' => $order->customer->district->city->name,
                'province' => $order->customer->district->city->province->name,
                'postal_code' => $order->customer->district->city->postal_code,
                'products' => $products,
                'subtotal' => 'Rp ' . number_format($subtotal, 0, ',', '.'),
                'service_cost' => 'Rp ' . number_format($serviceCost, 0, ',', '.'),
                'packaging_cost' => 'Rp ' . number_format($packagingCost, 0, ',', '.'),
                'shipping_cost' => 'Rp ' . number_format($shippingCost, 0, ',', '.'),
                'total' => 'Rp ' . number_format($grandTotal, 0, ',', '.'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Tidak dapat memuat detail pesanan. Coba lagi nanti', 'error' => $e->getMessage()], 500);
        }
    }

    public function acceptPayment($invoice)
    {
        $order = Order::with(['payment'])->where('invoice', $invoice)->first();

        $order->payment()->update(['status' => 1]);
        $order->update(['status' => 2]);
        return redirect(route('orders.view', $order->invoice))->with(['success' => 'Pembayaran Sudah dikonfirmasi']);
    }

    public function shippingOrder(Request $request)
    {
        $order = Order::with(['customer'])->find($request->order_id);
        $order->update(['tracking_number' => $request->tracking_number, 'status' => 3]);

        Mail::to($order->customer->email)->send(new OrderMail($order));
        return redirect()->back();
    }

    public function return($invoice) 
    {
        if (Order::where('invoice', $invoice)->exists()){
            $order = Order::with(['return', 'customer'])->where('invoice', $invoice)->first();
            return view('admin.orders.return', compact('order'));
        }else {
            return redirect()->back();
        }
    }

    public function approveReturn(Request $request)
    {
        $this->validate($request, ['status' => 'required']);

        $order = Order::find($request->order_id);
        $order->return()->update(['status' => $request->status]);
        $order->update(['status' => 4]);
        return redirect()->back();
    }

    public function orderReport()
    {
        return view('admin.report.index');
    }

    // datatables for report order
    public function getDatatablesReport(Request $request)
    {
        $start = $request->query('start_date') ? Carbon::parse($request->query('start_date'))->startOfDay() : Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
        $end = $request->query('end_date') ? Carbon::parse($request->query('end_date'))->endOfDay() : Carbon::now()->endOfMonth()->format('Y-m-d H:i:s');

        $orders = Order::with([
            'customer.district', 
            'details' => function($q){
                $q->where('status', 6)->orderBy('created_at', 'DESC');
            }
        ])
        ->whereHas('details', function($query) {
            $query->where('status', 6)->orderBy('created_at', 'DESC');
        })
        ->whereBetween('created_at', [$start, $end])
        ->orderBy('created_at', 'DESC')
        ->get();

        $orderIds = $orders->pluck('id')->toArray();
        $detailOrder = OrderDetail::whereIn('order_id', $orderIds)->orderBy('created_at', 'DESC')->get();

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
                    $formattedDate = $carbonDate->locale('id')->translatedFormat('d M Y');
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
            ->addColumn('totalProduct', function ($order) {
                // if ($order->details->isEmpty()) {
                //     return '<span>Rp 0</span>';
                // }
            
                // $detailsProductHtml = '';
                // $detailsCount = $order->details->count(); // Count the number of details
            
                // foreach ($order->details as $index => $detail) {
                //     // $amount = isset($detail->payment['amount']) ? $detail->payment['amount'] : 0;
                //     $amount = ($detail->qty * $detail->price + $detail->qty * $detail->price * 0.10 + $detail->shipping_cost) ?? 0;
                    
                //     // Conditionally add the index based on the number of details
                //     if ($detailsCount > 1) {
                //         $detailsProductHtml .= '<span>' . ($index + 1) . '. ' . 'Rp ' . number_format($amount, 0, ',', '.') . '</span>' . '<br>';
                //     } else {
                //         $detailsProductHtml .= '<span>Rp ' . number_format($amount, 0, ',', '.') . '</span>' . '<br>';
                //     }
                // }
            
                // return $detailsProductHtml;

                // new
                // new
                if ($order->details->isEmpty()) {
                    return '<span>Rp 0</span>';
                }
                
                $totalAmount = 0;
                $shippingCost = 0;

                $serviceCost = $order->service_cost;
                $packagingCost = $order->details->groupBy('seller_id')->count() * 1000;
                $shippingCost = $order->details->unique('seller_id')->sum('shipping_cost');
            
                foreach ($order->details as $detail) {
                    $subtotal = $detail->price * $detail->qty;
                    // $totalAmount += $subtotal + $subtotal * 0.10; // 0.10 is tax
                    $totalAmount += $subtotal; // 0.10 is tax
                }
            
                // // Add shipping cost to the total amount
                // $totalAmount += $shippingCost;
                $totalAmount += $packagingCost + $serviceCost;
            
                return '<span>Rp ' . number_format($totalAmount, 0, ',', '.') . '</span>';
            })
            ->addColumn('formattedDate', function ($order) {
                // $formattedDate = '';
                // if ($order->details->isEmpty() || !$order->details->first()->formatted_transfer_date) {
                //     $formattedDate = '-';
                // } else {
                //     foreach ($order->details as $index => $detail) {
                //         if (isset($detail->formatted_transfer_date)) {
                //             if ($order->details->count() > 1) {
                //                 $formattedDate .= ($index + 1) . '. ';
                //             }
                //             $formattedDate .= $detail->formatted_transfer_date . '<br>';
                //         } else {
                //             $formattedDate .= '-<br>';
                //         }
                //     }
                // }

                // return rtrim($formattedDate, '<br>');
                return Carbon::parse($order->created_at)->locale('id')->translatedFormat('d M Y ');
            })
            ->rawColumns(['details', 'totalProduct', 'formattedDate', 'statusProduct']) // Ensure HTML is not escaped in action column
            ->make(true);
    }

    public function orderReportPdf($daterange)
    {
        $date = explode('+', $daterange); 

        $start = Carbon::parse($date[0])->format('Y-m-d') . ' 00:00:01';
        $end = Carbon::parse($date[1])->format('Y-m-d') . ' 23:59:59';

        $orders = Order::with([
            'customer.district',
            'payment',
            'details' => function($q) use ($start, $end) {
                $q->where('status', 6)->whereBetween('created_at', [$start, $end]);
            },
            'return' => function($q){
                $q->where('status', 1);
            }])
            ->whereHas('details', function($q) use ($start, $end) {
                $q->where('status', 6)
                ->whereBetween('created_at', [$start, $end]);
            })
            ->orderBy('created_at', 'DESC')
            ->get();

        $pdf = PDF::loadView('admin.report.orderpdf', compact('orders', 'date'))
                    ->setPaper('A4', 'portrait')
                    ->setOptions([
                        'isHtml5ParserEnabled' => true,
                        'isRemoteEnabled' => true, 
                    ]);

        $startpdf = Carbon::parse($date[0])->format('d-F-Y');
        $endpdf = Carbon::parse($date[1])->format('d-F-Y');
        return $pdf->download('Laporan Order '.$startpdf.' sampai '.$endpdf.'.pdf');
    }

    public function returnReport()
    {
        return view('admin.report.return');
    }

    public function getDatatablesReportReturn(Request $request)
    {
        // $start = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
        // $end = Carbon::now()->endOfMonth()->format('Y-m-d H:i:s');
        $start = $request->query('start_date') ? Carbon::parse($request->query('start_date'))->startOfDay() : Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
        $end = $request->query('end_date') ? Carbon::parse($request->query('end_date'))->endOfDay() : Carbon::now()->endOfMonth()->format('Y-m-d H:i:s');

        $orders = Order::with([
            'customer.district',
            'return' => function($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end])
                  ->where('status', 1);
            }    
        ])
        ->whereHas('return', function($query) use ($start, $end) {
            $query->whereBetween('created_at', [$start, $end])
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
            
            ->addColumn('statusProduct', function ($order) {
                $statusCount = $order->return->count();
                if ($statusCount > 1) {
                    $statusHtml = '<ul>';
                    foreach ($order->return as $return) {
                        $statusHtml .= '<li>' . ($return->status_label ?? '-') . '</li>';
                    }
                    $statusHtml .= '</ul>';
                } else {
                    $statusHtml = $order->return->isEmpty() ? '-' : $order->return->first()->status_label;
                }
                return $statusHtml;
            })

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
                        $formattedDate = $carbonDate->locale('id')->translatedFormat('d M Y');
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
        $date = explode('+', $daterange);
        $start = Carbon::parse($date[0])->format('Y-m-d') . ' 00:00:01';
        $end = Carbon::parse($date[1])->format('Y-m-d') . ' 23:59:59';

        $orders = Order::with(['customer.district'])->has('return')->whereBetween('created_at', [$start, $end])->orderBy('created_at', 'DESC')->get();
        $pdf = PDF::loadView('admin.report.returnpdf', compact('orders', 'date'));
        
        $startpdf = Carbon::parse($date[0])->format('d-F-Y');
        $endpdf = Carbon::parse($date[1])->format('d-F-Y');
        return $pdf->download('Laporan Return Order '.$startpdf.' sampai '.$endpdf.'.pdf');
    }

    public function destroy($id)
    {
        $order = Order::find($id);

        if ($order) {
            $details = OrderDetail::where('order_id', $id)->delete();
            $returns = OrderReturn::where('order_id', $id)->delete();
            
            $order->delete();
        }

        // Check if the request is an AJAX request
        if (request()->ajax()) {
            return response()->json(['success' => 'Order Berhasil Dihapus']);
        }

        return redirect(route('orders.index'))->with(['success' => 'Order Sudah Dihapus']);
    }
}
