<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Order;
use App\OrderDetail;
use App\OrderCancelled;
use App\OrderCancelledDetail;
use App\Customer;
use App\Seller;
use App\Province;
use App\City;
use App\District;
use App\Payment;
use App\Product;
use App\Rating;
use Carbon\Carbon;
use DB;
use PDF;
use App\OrderReturn;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

class OrderController extends Controller
{
    public function index()
    {
        $customerId = auth()->guard('customer')->user()->id;
        $orders = Order::with(['return','details' => function($q) {
                $q->whereIn('status', [0,1,2,3,4,5]);
            }])
            ->withCount(['return'])
            ->where('customer_id', $customerId)
            ->whereHas('details', function($query) {
                $query->whereIn('status', [0,1,2,3,4,5]);
            })
            ->orderBy('created_at', 'DESC')
            ->paginate(10);
        
        return view('ecommerce.orders.index', compact('orders'));
    }

    public function view($invoice)
    {
        $order = Order::with([
            'district.city.province', 
            'details',
            'details.product', 
            'payment', 
            'return'])
            ->where('invoice', $invoice)->first();

        if(!$order) {
            return redirect()->route('customer.orders')->with('error', 'Pesanan Ini Sudah tidak bisa diakses'); 
            // return redirect()->back()->with(['error' => 'Pesanan Ini Sudah tidak bisa diakses']);   
        }

        $detailOrder = OrderDetail::where('order_id', $order->id)->get();

        if(!$detailOrder) {
            return redirect()->back()->with(['error' => 'Pesanan Ini Sudah tidak bisa diakses']);   
        }

        // ambil data dari table returns berdasarkan order id & produk id pada detail order
        $orderIds = $detailOrder->pluck('order_id')->toArray();
        $orderProductIds = $detailOrder->pluck('product_id')->toArray();
        $returns = OrderReturn::whereIn('order_id', $orderIds)->whereIn('product_id', $orderProductIds)->get();

        // ambil data dari table payment berdasarkan order id pada detail order
        $orderPayments = Payment::whereIn('order_id', $orderIds)->get();
        $returnsGrouped = $returns->groupBy('order_id')->map(function ($group) {
            return $group->keyBy('product_id');
        });

        $orderPaymentGrouped = $orderPayments->groupBy('order_id')->map(function ($group) {
            return $group->keyBy('product_id');
        });

        // gabung payment and return pada detail
        $details = $detailOrder->map(function ($detail) use ($returnsGrouped, $orderPaymentGrouped) {
            $statusReturn = null;
            $statusLabel = null;
            $statusPayment = null;
            $formattedDate = null;

            // cek jika returnnya ada pada detail order ini
            if (isset($returnsGrouped[$detail->order_id]) && isset($returnsGrouped[$detail->order_id][$detail->product_id])) {
                $return = $returnsGrouped[$detail->order_id][$detail->product_id];
                $statusReturn = $return->status;
                $statusLabel = $return->status_label;
                $returnArray = $return->toArray();
                unset($returnArray['order_id']);
                $detail->return = $returnArray;

                $carbonDate = Carbon::parse($return->created_at);

                // format tanggal
                $formattedDate = $carbonDate->translatedFormat('H:i');
                
                $translations = [
                    'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
                    'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
                    'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April',
                    'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
                    'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
                ];

                $formattedDate = str_replace(array_keys($translations), array_values($translations), $formattedDate);
                $detail->formatted_created_return = $formattedDate; 
            }

            if (isset($orderPaymentGrouped[$detail->order_id]) && isset($orderPaymentGrouped[$detail->order_id][$detail->product_id])) {
                $payment = $orderPaymentGrouped[$detail->order_id][$detail->product_id];
                $statusPayment = $payment->status;
                $paymentArray = $payment->toArray();
                unset($paymentArray['order_id']);
                $detail->payment = $paymentArray;

                $carbonDate = Carbon::parse($payment->transfer_date);

                // format tanggal
                $formattedDate = $carbonDate->translatedFormat('l, d F Y H:i:s');
                
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

            // tambah status pada array order detail
            $detail->formatted_shop_date = Carbon::parse($detail->shop_date)->locale('id')->translatedFormat('l, d F Y');
            $detail->formatted_confirm_payment_date_day = Carbon::parse($detail->confirm_payment_date)->locale('id')->translatedFormat('l, d F Y');
            $detail->formatted_shippin_date_day = Carbon::parse($detail->shippin_date)->locale('id')->translatedFormat('l, d F Y');
            $detail->formatted_arrived_date_day = Carbon::parse($detail->arrived_date)->locale('id')->translatedFormat('l, d F Y');
            $detail->formatted_receive_date_day = Carbon::parse($detail->receive_date)->locale('id')->translatedFormat('l, d F Y');
            $detail->formatted_process_date_day = Carbon::parse($detail->process_date)->locale('id')->translatedFormat('l, d F Y');
            
            // for time
            $detail->formatted_shop_date_time = Carbon::parse($detail->confirm_payment_date)->format('H:i');
            $detail->formatted_confirm_payment_date = Carbon::parse($detail->confirm_payment_date)->format('H:i');
            $detail->formatted_process_date = Carbon::parse($detail->process_date)->format('H:i');
            $detail->formatted_shippin_date = Carbon::parse($detail->shippin_date)->format('H:i');
            $detail->formatted_arrived_date = Carbon::parse($detail->arrived_date)->format('H:i');
            $detail->formatted_receive_date = Carbon::parse($detail->receive_date)->format('H:i');
            
            $detail->status_return = $statusReturn;
            $detail->status_label_return = $statusLabel;
            $detail->status_payment = $statusPayment;

            return $detail;
        });

        $totalProduk = $detailOrder->unique('product_id')->count();

        // get city name
        $cities = City::where('id', $order->district_id)->first();
        
        // get province name
        $province = Province::where('id', $order->customer->district->city->province->id)->first();

        // get district name
        $temp = auth()->guard('customer')->user()->load('district');
        $district = District::find(auth()->guard('customer')->user()->district->id);

        $shippingCostSums = [];
        foreach ($detailOrder as $detail) {
            $sellerId = $detail['seller_id'];
            if (!isset($shippingCostSums[$sellerId])) {
                $shippingCostSums[$sellerId] = 0;
            }
            $shippingCostSums[$sellerId] += $detail['shipping_cost'];
        }

        // map value jika harga ongkir di setiap produknya sama pada satu order id
        $subtotal = $detailOrder->sum(function($q){
            return $q['price'] * $q['qty'];
        });

        $tax = 0.10;
        $pajak = $subtotal * $tax;

        $shippingCost = array_sum($shippingCostSums);

        $total = collect([$subtotal, $pajak, $shippingCost])->pipe(function($q){
            return $q[0] + $q[1] + $q[2];
        });

        $formattedDate = null;

        if (Order::where('invoice', $invoice)->exists()){
            if(\Gate::forUser(auth()->guard('customer')->user())->allows('order-view', $order)){
                return view('ecommerce.orders.view', compact('order', 'cities', 'province', 'district', 'subtotal', 'shippingCost', 'pajak', 'total', 'details', 'totalProduk'));
            }
        }else {
            return redirect()->back();
        }    
        
        return redirect(route('customer.orders'))->with(['error' => 'Anda Tidak Diizinkan Untuk Mengakses Order Orang Lain']);
    }

    public function paymentForm($invoice)
    {
        $order = Order::with(['details.product', 'customer', 'payment'])->where('invoice', $invoice)->first();
        if (!$order) {
            return response()->json(['error' => 'Pesanan Tidak Ditemukan'], 404);
        }

        $snapToken = null; // Initialize snapToken to null

        try {
            // Set Midtrans configuration
            Config::$serverKey = config('services.midtrans.server_key');
            Config::$isProduction = config('services.midtrans.is_production');
            Config::$isSanitized = config('services.midtrans.is_sanitized');
            Config::$is3ds = config('services.midtrans.is_3ds');
            
            try {
                $statusResponse = \Midtrans\Transaction::status($order->invoice);
                $paymentStatus = $statusResponse->transaction_status;

                if ($paymentStatus === 'pending') {
                    $snapToken = $order->snap_token;
                }
            } catch (\Exception $e) {
                if ($e->getCode() == 404 && strpos($e->getMessage(), "Transaction doesn't exist") !== false) {
                    $itemDetails = $order->details->map(function ($detail) {
                        return [
                            'id' => $detail->product_id,
                            'price' => $detail->price,
                            'quantity' => $detail->qty,
                            'name' => $detail->product->name,
                        ];
                    })->toArray();
                    
                    // Add tax as a separate item
                    $packagingCost = $order->packaging_cost;
                    $itemDetails[] = [
                        'id' => 'Kemasan',
                        'price' => $packagingCost,
                        'quantity' => 1,
                        'name' => 'Kemasan',
                    ];

                    $serviceCost = $order->service_cost; // Assuming 10% tax
                    $itemDetails[] = [
                        'id' => 'Layanan',
                        'price' => $serviceCost,
                        'quantity' => 1,
                        'name' => 'Layanan',
                    ];
                    
                    // Add shipping cost as a separate item
                    $shippingCost = $order->cost;
                    $itemDetails[] = [
                        'id' => 'Ongkos Kirim',
                        'price' => $shippingCost,
                        'quantity' => 1,
                        'name' => 'Ongkos Kirim',
                    ];
                    
                    // Calculate gross amount
                    $grossAmount = $order->subtotal + $order->packaging_cost + $order->service_cost + $order->cost;
                    
                    $params = [
                        'transaction_details' => [
                            'order_id' => $order->invoice,
                            'gross_amount' => $grossAmount, // Include tax and shipping in gross amount
                        ],
                        'credit_card' => [
                            'secure' => true,
                        ],
                        'customer_details' => [
                            'first_name' => $order->customer->name,
                            'email' => $order->customer->email,
                            'phone' => $order->customer->phone_number,
                            'billing_address' => [
                                'first_name' => $order->customer->name,
                                'email' => $order->customer->email,
                                'phone' => $order->customer->phone_number,
                                'address' => $order->customer_address,
                                'city' => $order->customer->district->city->name,
                                'postal_code' => $order->customer->district->city->postal_code,
                                'country_code' => "IDN",
                            ],
                            'shipping_address' => [
                                'first_name' => $order->customer->name,
                                'email' => $order->customer->email,
                                'phone' => $order->customer->phone_number,
                                'address' => $order->customer_address,
                                'city' => $order->customer->district->city->name,
                                'postal_code' => $order->customer->district->city->postal_code,
                                'country_code' => "IDN",
                            ],
                        ],
                        'item_details' => $itemDetails,
                    ];
                    
                    // Generate a new Snap token
                    $snapToken = Snap::getSnapToken($params);
                    
                    // Save the Snap token to the order
                    $order->update(['snap_token' => $snapToken]);
                    
                } else {
                    // Handle other exceptions that are not 404
                    throw $e;
                }
            }

            return view('ecommerce.payment', compact('order', 'snapToken'));

        } catch (\Exception $e) {
            // Handle API error, such as duplicate order_id
            if ($e->getCode() == 400 && strpos($e->getMessage(), 'transaction_details.order_id has already been taken') !== false) {
                return redirect()->back()->with(['error' => 'Pembayaran untuk order ini sudah diproses atau order ID sudah ada. Silakan coba lagi atau hubungi layanan pelanggan.']);
            } else if ($e->getCode() == 404 && strpos($e->getMessage(), "Transaction doesn't exist") !== false) {
                return redirect()->back()->with(['error' => 'Transaksi tidak ditemukan di Midtrans.']);
            } else {
                // Handle other exceptions
                return redirect()->back()->with(['error' => $e->getMessage()]);
            }
        }
    }

    public function listPaymentIndex()
    {
        $orders = Order::with([
            'payment',
            'details' => function($q) {
                $q->where('status', 0)->orderBy('created_at', 'DESC');
            }
        ])
        // ->whereHas('payment', function($query) {
        //     $query->where('transaction_status', 'pending')->orderBy('created_at', 'DESC');
        // })
        ->whereHas('details', function($query) {
            $query->where('status', 0)->orderBy('created_at', 'DESC');
        })
        ->where('customer_id', auth()->guard('customer')->user()->id)->orderBy('created_at', 'DESC')->paginate(10);
        
        // dd($orders);

        return view('ecommerce.orders.listpayment', compact('orders'));
    }

    public function storePayment(Request $request)
    {
        DB::beginTransaction();
        try {
            if (!is_array($request->product_id) || empty($request->product_id)) {
                throw new \Exception('Produk tidak ada');
            }

            $order = Order::where('invoice', $request->invoice)->firstOrFail();

            $orderDetails = OrderDetail::where('order_id', $order->id)
                ->whereIn('product_id', $request->product_id)
                ->get();

            $totalAmount = 0;

            foreach ($orderDetails as $orderDetail) {
                $amount = ($orderDetail->qty * $orderDetail->price) + ($orderDetail->qty * $orderDetail->price * 0.10) + $orderDetail->shipping_cost;

                $totalAmount += $amount;

                Payment::create([
                    'order_id' => $order->id,
                    'product_id' => $orderDetail->product_id,
                    'name' => $request->name,
                    'payment_type' => $request->payment_type,
                    'transaction_id' => $request->transaction_id,
                    'amount' => $amount,
                    'proof' => null,
                    'transaction_status' => $request->transaction_status,
                    'status' => false,
                    'transfer_date' => $request->transaction_date,
                    'created_at' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'),
                ]);

                $orderDetail->update([
                    'status' => 1,
                    'shop_date' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'),
                ]);
            }

            DB::commit();

            return response()->json(['message' => 'Pembayaran Berhasil'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function notificationHandler(Request $request)
    {
        // Capture the JSON notification from Midtrans
        $json = $request->getContent();
        $notification = json_decode($json, true);

        if (!$notification) {
            return response()->json(['error' => 'Notifikasi Tidak Valid'], 400);
        }

        // Retrieve necessary data from the notification
        $statusCode = $notification['status_code'];
        $transactionStatus = $notification['transaction_status'];
        $orderInvoice = $notification['order_id'];
        $paymentType = $notification['payment_type'];
        $transactionId = $notification['transaction_id'];
        $grossAmount = $notification['gross_amount'];
        $transactionTime = $notification['transaction_time'];

        if (!$orderInvoice || !$statusCode) {
            return response()->json(['error' => 'Data tidak lengkap'], 400);
        }

        $paymentName = match ($paymentType) {
            'credit_card' => $notification['acquirer'] ?? 'Credit Card',
            'bank_transfer' => $notification['va_numbers'][0]['bank'] ?? 'Bank Transfer',
            'echannel' => 'Mandiri Bill Payment',
            'gopay' => 'GoPay',
            'cstore' => $notification['store'] ?? 'Gerai Offline',
            'akulaku' => 'AkuLaku',
            'qris' => $notification['acquirer'] ?? 'QRIS',
            'bca_klikpay' => 'BCA KlikPay',
            'bri_epay' => 'BRI ePay',
            'cimb_clicks' => 'CIMB Clicks',
            'danamon_online' => 'Danamon Online Banking',
            'shopeepay' => 'ShopeePay',
            default => 'Metode Pembayaran Tidak Diketahui',
        };

        DB::beginTransaction();
        try {
            $order = Order::with(['details', 'payment'])->where('invoice', $orderInvoice)->firstOrFail();

            $currentTimestamp = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');

            switch ($statusCode) {
                case '200': // Success, payment is either 'capture' or 'settlement'
                    if (in_array($transactionStatus, ['capture', 'settlement'])) {
                        foreach ($order->details as $detail) {
                            $paymentData = [
                                'order_id' => $order->id,
                                'product_id' => $detail->product_id,
                                'name' => $order->customer_name,
                                'payment_type' => $paymentType,
                                'transaction_id' => $transactionId,
                                'amount' => $grossAmount, 
                                'proof' => null,
                                'transaction_status' => $transactionStatus,
                                'acquirer' => $paymentName,
                                'transfer_date' => $transactionTime,
                                'created_at' => $currentTimestamp,
                                'updated_at' => $currentTimestamp,
                            ];

                            $paymentData['status'] = false;
                            $detail->update([
                                'status' => 1,
                                'shop_date' => $currentTimestamp,
                            ]);

                            Payment::create($paymentData);
                        }
                    }
                    break;
                case '201': // Pending
                    // Check if the transaction has already reached settlement or capture status
                    $existingPayment = Payment::where('order_id', $order->id)
                        ->whereIn('transaction_status', ['settlement', 'capture'])
                        ->exists();
                
                    if (!$existingPayment && $transactionStatus === 'pending') {
                        foreach ($order->details as $detail) {
                            // Double-check the transaction status before saving
                            $alreadyProcessed = Payment::whereIn('order_id', $detail->order_id)
                                ->whereIn('transaction_status', ['settlement', 'capture'])
                                ->exists();
                
                            if (!$alreadyProcessed) { // Final safeguard check
                                $paymentData = [
                                    'order_id' => $order->id,
                                    'product_id' => $detail->product_id,
                                    'name' => $order->customer_name,
                                    'payment_type' => $paymentType,
                                    'transaction_id' => $transactionId,
                                    'amount' => $grossAmount,
                                    'proof' => null,
                                    'transaction_status' => $transactionStatus,
                                    'acquirer' => $paymentName,
                                    'transfer_date' => $transactionTime,
                                    'created_at' => $currentTimestamp,
                                    'updated_at' => $currentTimestamp,
                                ];
                
                                $paymentData['status'] = false;
                                $detail->update(['status' => 0]); // Mark as pending
                
                                Payment::create($paymentData);
                            } else {
                                // Log or take additional action if settlement or capture already exists
                                \Log::info("Pending skipped for Order: $orderInvoice, transaction already settled or captured.");
                            }
                        }
                    }
                    break;
                case '202': // Denied
                case '407': // Expired
                    // Delete the order and its related details/payments
                    $order->details()->delete();
                    $order->payment()->delete();
                    $order->delete();
                    break;
                case '402': // Canceled
                    
                    // Handle other statuses if needed
                    foreach ($order->details as $detail) {
                        $paymentData = [
                            'order_id' => $order->id,
                            'product_id' => $detail->product_id,
                            'name' => $order->customer_name,
                            'payment_type' => null,
                            'transaction_id' => null,
                            'amount' => $grossAmount,
                            'proof' => null,
                            'transaction_status' => null,
                            'acquirer' => null,
                            'transfer_date' => null,
                            'created_at' => $currentTimestamp,
                            'updated_at' => $currentTimestamp,
                        ];
        
                        $paymentData['status'] = false;
                        $detail->update([
                            'status' => -1
                        ]); // Mark as canceled or failed

                        Payment::create($paymentData);
                    }
                    break;

                default:
                    return response()->json(['error' => 'Status code tidak dikenali'], 400);
            }

            DB::commit();
            return response()->json(['message' => 'Berhasil diproses'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function pdf($invoice) 
    {
        $order = Order::with(['customer', 'district.city.province', 'details', 'details.product', 'payment'])
                        ->where('invoice', $invoice)->first();
                        
        if (!$order) {
            abort(404, 'Pesanan Tidak Ditemukan');
        }   


        $detailOrder = OrderDetail::where('order_id', $order->id)->get();
        $sellerIds = $detailOrder->pluck('seller_id')->toArray();

        $sellers = Seller::whereIn('id', $sellerIds)->get();
        $sellerPhones = $sellers->pluck('phone_number', 'id')->toArray();
        $sellerNames = $sellers->pluck('name', 'id')->toArray();
        $sellerAddress = $sellers->pluck('address', 'id')->toArray();
        $districtIds = $sellers->pluck('district_id')->toArray();

        $districts = District::whereIn('id', $districtIds)->get();
        $districtNames = $districts->pluck('name', 'id')->toArray();
        $cityIds = $districts->pluck('city_id')->toArray();

        // Get city names
        $cities = City::whereIn('id', $cityIds)->get();
        $cityNames = $cities->pluck('name', 'id')->toArray();
        $provinceIds = $cities->pluck('province_id')->toArray();;

        // Get province names
        $provinces = Province::whereIn('id', $provinceIds)->get();
        $provinceNames = $cities->pluck('name', 'id')->toArray();

        $shippingCostSums = [];
        foreach ($detailOrder as $detail) {
            $sellerId = $detail['seller_id'];
            if (!isset($shippingCostSums[$sellerId])) {
                $shippingCostSums[$sellerId] = 0;
            }
            $shippingCostSums[$sellerId] += $detail['shipping_cost'];
        }

        // map value jika harga ongkir di setiap produknya sama pada satu order id
        $subtotal = $detailOrder->sum(function($q){
            return $q['price'] * $q['qty'];
        });

        $tax = 0.10;
        $pajak = $subtotal * $tax;

        $shippingCost = array_sum($shippingCostSums);

        $total = collect([$subtotal, $pajak, $shippingCost])->pipe(function($q){
            return $q[0] + $q[1] + $q[2];
        });

        // Transform the order collection
        $combinedInfo = [];
        foreach ($sellers as $seller) {
            $district = $districts->where('id', $seller->district_id)->first();
            $city = $cities->where('id', $district->city_id)->first();
            $province = $provinces->where('id', $city->province_id)->first();

            $combinedInfo[] = [
                'name' => $seller->name,
                'phone' => $seller->phone_number,
                'address' => $seller->address,
                'district' => $district->name,
                'city' => $city->name,
                'province' => $province->name,
            ];
        }

        // Transform the order object
        $transformedOrder = $order->toArray();
        $transformedOrder['sellers'] = $combinedInfo;

        // Create a Carbon instance
        $carbonDate = Carbon::parse($order->created_at);

        // Format the date
        $formattedDate = $carbonDate->locale('id')->translatedFormat('l, d F Y');
        
        // Custom translations for day and month names
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

        if(Order::where('invoice', $invoice)->exists()) {
            if(\Gate::forUser(auth()->guard('customer')->user())->allows('order-view', $order)) {
                $pdf = PDF::loadView('ecommerce.orders.detailPDF', compact('order', 'transformedOrder', 'formattedDate'));
                $filename = $order->invoice;

                if(request()->ajax()) {
                    return response()->json(['success' => 'PDF berhasil diunduh'], 200);
                }    

                return $pdf->download(strtoupper($filename).'-invoice.pdf');
            }else {
                return redirect(route('customer.orders'))->with(['error' => 'Anda Tidak Diizinkan Untuk Mengakses Invoice Orang Lain']);
            }
        } else {
            return redirect(route('customer.orders'))->with(['error' => 'Invoice Tidak ada dalam Orderan Anda']);
        }
    }

    public function acceptOrder(Request $request)
    {
        $order = Order::find($request->order_id);

        if (!$order) {
            if ($request->ajax()) {
                return response()->json(['error' => false, 'message' => 'Pesanan Tidak Ditemukan'], 404);
            }
            return redirect()->back()->with(['error' => 'Pesanan Tidak Ditemukan']);
        }

        $orderDetail = OrderDetail::where('order_id', $request->order_id)->where('product_id', $request->product_id)->first();

        if (!$orderDetail) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Detail Pesanan Tidak Ditemukan'], 404);
            }
            return redirect()->back()->with(['error' => 'Detail Pesanan Tidak Ditemukan']);
        }

        if (!\Gate::forUser(auth()->guard('customer')->user())->allows('order-view', $order)) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Bukan Pesanan Kamu'], 403);
            }
            return redirect()->back()->with(['error' => 'Bukan Pesanan Kamu']);
        }
        
        // pesanan diterima
        $orderDetail->update([
            'status' => 6,
            'receive_date' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s')
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => 'Pesanan Diterima']);
        }
    }

    public function ratingStore(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'order_id' => 'required|exists:orders,id', // Ensure order_id is included in validation
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:255',
        ]);

        // Create a new rating
        $rating = new Rating();
        $rating->customer_id = auth()->guard('customer')->user()->id; // Assuming you have user authentication
        $rating->product_id = $request->input('product_id');
        $rating->order_id = $request->input('order_id'); // Include the order ID
        $rating->rating = $request->input('rating');
        $rating->comment = $request->input('comment');
        $rating->save();

        // Return a success response
        return response()->json(['success' => 'Terimakasih sudah memberikan rating dan komentar'], 200);
    }

    public function returnForm($invoice, $product_id)
    {
        $order = Order::where('invoice', $invoice)->first();
        $products = Product::where('id', $product_id)->first();
        $detailOrder = OrderDetail::with(['product'])->where('order_id', $order->id)->where('product_id', $product_id)->first();
        $payment = Payment::where('order_id', $order->id)->where('product_id', $product_id)->first();

        // Seller > get address, district, city, province
        $customers = auth()->guard('customer')->user()->load('district');
        $districts = District::where('id', $customers->district->id)->first();
        $cities = City::where('id', $customers->district->city_id)->first();
        $provinces = Province::where('id', $customers->district->province_id)->first();

        if (Order::where('invoice', $invoice)->exists()){
            if(\Gate::forUser(auth()->guard('customer')->user())->allows('order-view', $order)){
                return view('ecommerce.orders.return', compact('order', 'product_id', 'products', 'detailOrder', 'payment', 'customers', 'districts', 'cities', 'provinces'));
            }
        }else {
            return redirect()->back()->with(['error' => 'Produk sedang dalam proses return']);
        }  

        return redirect()->back()->with(['error' => 'Anda Tidak Diizinkan Untuk Mengakses Return Order Orang Lain']);
    }

    // public function historyOrders()
    // {
    //     $orders = Order::with([
    //         'details' => function($q) {
    //             $q->where('status', 6)->orderBy('created_at', 'DESC');
    //         }, 
    //         'payment', 
    //         'return'
    //         ])
    //         ->whereHas('details', function($query) {
    //             $query->where('status', 6)->orderBy('created_at', 'DESC');
    //         })
    //         ->where('customer_id', auth()->guard('customer')->user()->id)->orderBy('created_at', 'DESC')->get();

    //     $detailOrder = OrderDetail::with(['product'])->whereIn('order_id', $orders->pluck('id'))->where('status', 6)->orderBy('created_at', 'DESC')->get();

    //     // ambil data dari table returns berdasarkan order id & produk id pada detail order
    //     $orderIds = $detailOrder->pluck('order_id')->toArray();
    //     $orderProductIds = $detailOrder->pluck('product_id')->toArray();
    //     $returns = OrderReturn::whereIn('order_id', $orderIds)->whereIn('product_id', $orderProductIds)->get();

    //     // ambil data dari table payment berdasarkan order id pada detail order
    //     $orderPayments = Payment::whereIn('order_id', $orderIds)->get();
    //     $returnsGrouped = $returns->groupBy('order_id')->map(function ($group) {
    //         return $group->keyBy('product_id');
    //     });

    //     $orderPaymentGrouped = $orderPayments->groupBy('order_id')->map(function ($group) {
    //         return $group->keyBy('product_id');
    //     });

    //     // gabung payment and return pada detail
    //     $details = $detailOrder->map(function ($detail) use ($returnsGrouped, $orderPaymentGrouped) {
    //         $statusReturn = null;
    //         $statusLabel = null;
    //         $statusPayment = null;
    //         $formattedDate = null;

    //         // cek jika returnnya ada pada detail order ini
    //         if (isset($returnsGrouped[$detail->order_id]) && isset($returnsGrouped[$detail->order_id][$detail->product_id])) {
    //             $return = $returnsGrouped[$detail->order_id][$detail->product_id];
    //             $statusReturn = $return->status;
    //             $statusLabel = $return->status_label;
    //             $returnArray = $return->toArray();
    //             unset($returnArray['order_id']);
    //             $detail->return = $returnArray;
    //         }

    //         if (isset($orderPaymentGrouped[$detail->order_id]) && isset($orderPaymentGrouped[$detail->order_id][$detail->product_id])) {
    //             $payment = $orderPaymentGrouped[$detail->order_id][$detail->product_id];
    //             $statusPayment = $payment->status;
    //             $paymentArray = $payment->toArray();
    //             unset($paymentArray['order_id']);
    //             $detail->payment = $paymentArray;

    //             $carbonDate = Carbon::parse($payment->transfer_date);

    //             // format tanggal
    //             $formattedDate = $carbonDate->translatedFormat('l, d F Y');
                
    //             $translations = [
    //                 'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
    //                 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
    //                 'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April',
    //                 'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
    //                 'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
    //             ];

    //             $formattedDate = str_replace(array_keys($translations), array_values($translations), $formattedDate);
    //             $detail->formatted_transfer_date = $formattedDate; 
    //         }

    //         // tambah status pada array order detail
    //         $detail->formatted_shop_date = Carbon::parse($detail->shop_date)->locale('id')->translatedFormat('l, d F Y');
    //         $detail->formatted_confirm_payment_date_day = Carbon::parse($detail->confirm_payment_date)->locale('id')->translatedFormat('l, d F Y');
    //         $detail->formatted_shippin_date_day = Carbon::parse($detail->shippin_date)->locale('id')->translatedFormat('l, d F Y');
    //         $detail->formatted_arrived_date_day = Carbon::parse($detail->arrived_date)->locale('id')->translatedFormat('l, d F Y');
    //         $detail->formatted_receive_date_day = Carbon::parse($detail->receive_date)->locale('id')->translatedFormat('l, d F Y');
    //         $detail->formatted_process_date_day = Carbon::parse($detail->process_date)->locale('id')->translatedFormat('l, d F Y');
            
    //         // for time
    //         $detail->formatted_shop_date_time = Carbon::parse($detail->confirm_payment_date)->format('H:i');
    //         $detail->formatted_confirm_payment_date = Carbon::parse($detail->confirm_payment_date)->format('H:i');
    //         $detail->formatted_process_date = Carbon::parse($detail->process_date)->format('H:i');
    //         $detail->formatted_shippin_date = Carbon::parse($detail->shippin_date)->format('H:i');
    //         $detail->formatted_arrived_date = Carbon::parse($detail->arrived_date)->format('H:i');
    //         $detail->formatted_receive_date = Carbon::parse($detail->receive_date)->format('H:i');
            
    //         $detail->status_return = $statusReturn;
    //         $detail->status_label_return = $statusLabel;
    //         $detail->status_payment = $statusPayment;

    //         return $detail;
    //     });

    //     // Group details by order ID
    //     $groupedDetails = $details->groupBy('order_id');

    //     // Attach details to respective orders
    //     $orders->map(function ($order) use ($groupedDetails) {
    //         $order->details = $groupedDetails->get($order->id, collect());
    //         return $order;
    //     });

    //     return view('ecommerce.orders.history', compact('orders'));
    // }

    public function historyOrders()
    {
        // id customer
        $customerId = auth()->guard('customer')->user()->id;
        
        $orders = Order::with([
            'customer',
            'payment',
            'return' => function($q) {
                $q->where('status', 1);
            },
            'details' => function($q) {
                $q->where('status', 6)->orderBy('created_at', 'DESC');
            }
            ])
            ->whereHas('details', function($query) {
                $query->where('status', 6)->orderBy('created_at', 'DESC');
            })
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'DESC')
            ->paginate(5);

        return view('ecommerce.orders.history', compact('orders'));
    }

    public function historyOrdersPdf($invoice)
    {
        $order = Order::with([
            'customer',
            'payment',
            'return' => function($q) {
                $q->where('status', 1);
            },
            'details' => function($q) {
                $q->where('status', 6)->orderBy('created_at', 'DESC');
            }
            ])
            ->whereHas('details', function($query) {
                $query->where('status', 6)->orderBy('created_at', 'DESC');
            })
            ->where('invoice', $invoice)
            ->orderBy('created_at', 'DESC')
            ->first();

        if (!$order) {
            return redirect(route('customer.orders'))->with(['error' => 'Invoice Tidak ada dalam Orderan Anda']);
        }

        $sellerIds = [];
        foreach($order->details as $detail){
            $sellerIds[] = $detail->seller_id;
        }

        $sellers = Seller::whereIn('id', $sellerIds)->distinct()->get();

        if (\Gate::forUser(auth()->guard('customer')->user())->allows('order-view', $order)) {
            $payment = $order->payment;
            $pdf = PDF::loadView('ecommerce.orders.historyPDF', compact('order', 'sellers'));
            $filename = $order->invoice;

            // Set options for the PDF, including margin and enabling page footer
            // $pdf->setPaper('A4', 'portrait')
            // ->setOptions([
            //     'isHtml5ParserEnabled' => true,
            //     'isRemoteEnabled' => true
            // ]);

            if(request()->ajax()) {
                return response()->json(['success' => 'PDF berhasil dimuat'], 200);
            }  

            return $pdf->stream($filename . '-invoice.pdf');
        } else {
            return redirect(route('customer.orders'))->with(['error' => 'Anda Tidak Diizinkan Untuk Mengakses Invoice Orang Lain']);
        }
    }

    public function processReturn(Request $request)
    {
        $this->validate($request, [
            'reason' => 'required|string',
        ]);

        $return = OrderReturn::where('order_id', $request->order_id)->where('product_id', $request->product_id)->first();
        if ($return) return redirect()->back()->with(['error' => 'Permintaan Refund Pada Produk Ini Sedang Dalam Proses']);

        $order = Order::find($request->order_id);
        if (!$order) {
            return redirect()->back()->with(['error' => 'Pesanan Tidak Ditemukan']);
        }

        $detailOrder = OrderDetail::with(['product'])->where('order_id', $request->order_id)->where('product_id', $request->product_id)->first();
        if (!$detailOrder) {
            return response()->json(['error' => 'Pesanan sedang dalam proses return']);
        }
        
        $subtotal = $detailOrder->qty * $detailOrder->price;

        if ($order) {
            OrderReturn::create([
                'order_id' => $order->id,
                'product_id' => $request->product_id,
                'seller_id' => $detailOrder->seller_id,
                'tracking_number' => $detailOrder->tracking_number,
                'qty' => $detailOrder->qty,
                'photo' => $detailOrder->product->image,
                'reason' => $request->reason,
                'refund_transfer' => $subtotal,
                'status' => 0
            ]);

            $this->sendMessage($order->invoice, $detailOrder->tracking_number, $request->reason);

            return response()->json(['success' => 'Berhasil Return Produk pada Pesanan Ini']);
        }

        return response()->json(['error' => 'Gagal Return Produk pada Pesanan Ini']);
    }

    public function cancelOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cancel_reason' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation error, Harap periksa isian kembali', 'errors' => $validator->errors(), 'input' => $request->all()], 422);
        }

        DB::beginTransaction();
        try {
            // Retrieve the order and its details
            $order = Order::with('details')->where('invoice', $request->invoice)->firstOrFail();

            // Save to order_cancelled table
            $orderCancelled = OrderCancelled::create([
                'invoice' => $order->invoice,
                'customer_id' => $order->id,
                'customer_name' => $order->customer_name,
                'customer_phone' => $order->customer_phone,
                'customer_address' => $order->customer_address,
                'district_id' => $order->district_id,
                'subtotal' => $order->subtotal,
                'cost' => $order->cost,
                'service_cost' => $order->service_cost,
                'packaging_cost' => $order->packaging_cost,
                'reason' => $request->cancel_reason,
            ]);

            // Save to order_cancelled_details and update product stock
            foreach ($order->details as $detail) {
                // kembalikan stok produk jika produk dibatalkan
                Product::where('id', $detail->product_id)->increment('stock', $detail->qty);

                OrderCancelledDetail::create([
                    'order_id' => $orderCancelled->id,
                    'product_id' => $detail->product_id,
                    'seller_id' => $detail->seller_id,
                    'price' => $detail->price,
                    'qty' => $detail->qty,
                    'weight' => $detail->weight,
                    'shipping_courier' => $detail->shipping_courier,
                    'shipping_cost' => $detail->shipping_cost,
                    'shipping_service' => $detail->shipping_service,
                    'status' => 'cancel',
                ]);
            }

            // Delete the order
            $order->delete();

            DB::commit();

            return response()->json(['success' => 'Pesanan berhasil dibatalkan', 'redirect' => route('customer.dashboard')], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Gagal membatalkan pesanan: ' . $e->getMessage()], 500);
        }
    }

    //Curl Telegram
    private function getTelegram($url, $params)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . $params); 

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        $content = curl_exec($ch);
        curl_close($ch);
        return json_decode($content, true);
    }

    private function sendMessage($invoice, $reason)
    {
        $key = env('7797063599:AAGXZqR5TRQG1mDnwT3Q_B7o1p26ECgF1LI'); 

        $chat = $this->getTelegram('https://api.telegram.org/'. $key .'/getUpdates', '');

        if ($chat['ok']) {
            //cukup ambil key 0 atau admin saja untuk mendapatkan chat_id
            $chat_id = $chat['result'][0]['message']['chat']['id'];

            $text = 'Hai Admin E-Commerce, OrderID '.$invoice.' Melakukan Permintaan Refund Dengan Alasan "'. $reason.'", Silahkan Segera Dicek Ya!';
        
            //kirim request ke telegram untuk mengirim pesan
            return $this->getTelegram('https://api.telegram.org/'. $key .'/sendMessage', '?chat_id=' . $chat_id . '&text=' . $text);
        }
    }

}
