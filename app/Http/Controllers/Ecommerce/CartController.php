<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;
use App\Province;
use App\City;
use App\District;
use App\Customer;
use App\Order;
use App\Seller;
use App\OrderDetail;
use Illuminate\Support\Str;
use DB;
use Carbon\Carbon;
use App\Mail\CustomerRegisterMail;
use Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Midtrans\Config;
use Midtrans\Snap;

class CartController extends Controller
{
    private function getCarts()
    {
        $carts = json_decode(request()->cookie('e-carts'), true);
        $carts = $carts != '' ? $carts:[];

        return $carts;
    }

    public function addToCart(Request $request)
    {
        $this->validate($request, [
            'product_id' => 'required|exists:products,id', 
            'qty' => 'required|integer|min:1',
        ]);

        // Decode the existing cart or initialize an empty array
        $carts = json_decode($request->cookie('e-carts'), true) ?? [];

        $product = Product::find($request->product_id);

        if($product->stock == 0){
            return response()->json(['error' => 'Stok produk habis'], 400);
        }

        // Check if requested quantity exceeds available quantity
        if ($request->qty > $product->stock) {
            return response()->json(['error' => 'Jumlah kuantiti melebihi jumlah stok produk yang tersedia'], 400);
        }

        // Ensure the user is authenticated
        // if (!auth()->guard('customer')->check()) {
        //     return response()->json(['error' => 'Silahkan login terlebih dahulu'], 401);
        // }

        // Get seller and customer details
        $seller = Seller::with(['district'])->where('id', $product->seller_id)->first();
        $customer = $seller->load('district');
        $city = $seller->district->city;
        $province = $seller->district->city->province;

        // Group cart items by seller ID
        if (!isset($carts[$product->seller_id])) {
            $carts[$product->seller_id] = [
                'seller_id' => $product->seller_id,
                'seller_name' => $seller->name,
                'origin_details' => [
                    'province_id' => $seller->district->province_id,
                    'province_name' => $province->name,
                    'city_id' => $seller->district->city_id,
                    'city_name' => $city->name,
                    'district_id' => $seller->district->id,
                    'address' => $seller->district->name,
                ],
                'destination_details' => [
                    'province_id' => $customer->district->province_id,
                    'province_name' => $customer->district->city->province->name,
                    'city_id' => $customer->district->city_id,
                    'city_name' => $customer->district->city->name,
                    'district_id' => $customer->district->id,
                    'address' => $customer->district->name,
                ],
                'products' => [],
                'shippingCost' => null,
                'courier' => null,
                'service' => null,
            ];
        }

        // Add or update product within the seller's cart
        if (isset($carts[$product->seller_id]['products'][$request->product_id])) {
            $newQty = $carts[$product->seller_id]['products'][$request->product_id]['qty'] + $request->qty;

            if ($newQty > $product->stock) {
                return response()->json(['error' => 'Jumlah total dalam keranjang melebihi jumlah yang tersedia'], 400);
            }

            $carts[$product->seller_id]['products'][$request->product_id]['qty'] = $newQty;
        } else {
            $carts[$product->seller_id]['products'][$request->product_id] = [
                'qty' => $request->qty,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_price' => $product->promo_price === null || $product->promo_price === 0 ? $product->price : $product->promo_price,
                'product_image' => $product->image,
                'weight' => $product->weight,
            ];
        }

        // Update the cookie with the new cart data
        $cookie = cookie('e-carts', json_encode($carts), 2880);

        return response()->json(['success' => 'Berhasil menambahkan produk ke keranjang'])->cookie($cookie);
    }

    public function listCart()
    {
        $carts = $this->getCarts();

        $subtotal = collect($carts)->sum(function ($cart) {
            return collect($cart['products'])->sum(function ($product) {
                return $product['qty'] * $product['product_price'];
            });
        });

        $destination = null;

        if (auth()->guard('customer')->check()) {
            $customer = auth()->guard('customer')->user()->load('district');
            $district = District::where('id', $customer->district_id)->first();
            $destination = City::where('id', $district->city_id)->first();
        }

        return view('ecommerce.cart', compact('carts', 'subtotal', 'destination'));
    }

    public function updateCart(Request $request)
    {
        try {
            $carts = $this->getCarts();

            if (empty($request->seller_id) || empty($request->product_id)) {
                return response()->json(['error' =>  'Tidak ada data'], 500);
            }

            $products = Product::whereIn('id', $request->product_id)->get();

            foreach ($products as $product) {
                $requestedQuantity = array_sum(array_filter($request->qty, function ($key) use ($product, $request) {
                    return $request->product_id[$key] == $product->id;
                }, ARRAY_FILTER_USE_KEY));
    
                if ($requestedQuantity > $product->stock) {
                    return response()->json([
                        'error' => "Jumlah kuantiti untuk produk {$product->name} melebihi jumlah yang tersedia. Maksimal stok yang tersedia adalah {$product->stock}."
                    ], 400);
                }
            }

            foreach ($request->seller_id as $seller_key => $seller_id) {
                if (isset($carts[$seller_id])) {
                    // Loop through each product ID for the current seller
                    foreach ($request->product_id as $product_key => $product_id) {
                        if (isset($carts[$seller_id]['products'][$product_id])) {
                            if ($request->qty[$product_key] == 0) {
                                unset($carts[$seller_id]['products'][$product_id]);
                            } else {
                                $carts[$seller_id]['products'][$product_id]['qty'] = $request->qty[$product_key] ?? $carts[$seller_id]['products'][$product_id]['qty'];
                            }
                        }
                    }

                    // If the seller has no products left, remove the seller
                    if (empty($carts[$seller_id]['products'])) {
                        unset($carts[$seller_id]);
                    } else {
                        // Update the seller's shipping cost, courier, and service
                        $carts[$seller_id]['shippingCost'] = $request->cost[$seller_key] ?? $carts[$seller_id]['shippingCost'];
                        $carts[$seller_id]['courier'] = $request->courier[$seller_key] ?? $carts[$seller_id]['courier'];
                        $carts[$seller_id]['service'] = $request->service[$seller_key] ?? $carts[$seller_id]['service'];
                    }
                }
            }

            // Save the updated carts back to a cookie
            $cookie = cookie('e-carts', json_encode($carts), 2880);

            return response()->json(['success' => 'Berhasil memperbarui data'])->cookie($cookie);
        } catch (\Exception $e) {

            // Return a generic error response
            return response()->json(['error' =>  $e->getMessage()], 500);
        }
    }

    public function deleteCart(Request $request)
    {
        $carts = $this->getCarts(); 
        $cookie = cookie('e-carts', json_encode([]));

        return response()->json(['success' => true, 'message' => 'Pesanan dalam keranjang berhasil dihapus.'])->withCookie($cookie);
    }

    public function getOngkir($origin, $destination, $weight, $courier)
    {
        // Handle weight range if it's a range like "300 - 500"
        if (strpos($weight, ' - ') !== false) {
            $weightRange = explode(' - ', $weight);
            // Use the maximum value in the weight range, converting to an integer in grams
            $weight = (int) trim(max($weightRange));
        } else {
            // Ensure weight is an integer in grams
            $weight = (int) trim($weight);
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.rajaongkir.com/starter/cost",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_DNS_USE_GLOBAL_CACHE => false,
            CURLOPT_DNS_CACHE_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => "POST",
            // CURLOPT_POSTFIELDS => "origin=501&destination=114&weight=1700&courier=jne",
            // CURLOPT_POSTFIELDS => "origin=$origin&destination=$destination&weight=$weight&courier=$courier",
            CURLOPT_POSTFIELDS => http_build_query(array(
                'origin' => $origin,
                'destination' => $destination,
                'weight' => $weight,
                'courier' => $courier,
            )),
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded",
                "key: 79f2b835940489c164654cc868d70936"
            ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        
        if ($err) {
            error_log("cURL Error #: " . $err);
            return response()->json(['error' => "cURL Error #: " . $err], 500);
        } else {
            $response = json_decode($response, true);
            if (isset($response['rajaongkir']['results'])) {
                $data_ongkir = $response['rajaongkir']['results'];
                return response()->json($response['rajaongkir']['results'], 200);
                // return json_encode($data_ongkir);
            } else {
                error_log("Invalid API response: " . json_encode($response));
                return response()->json(['error' => "Invalid API response"], 500);
                // return "Invalid API response";
            }
        }
    }

    public function checkout(Request $request)
    {
        $carts = $this->getCarts();

        // Initialize arrays for storing courier, service, and shipping costs
        $tempCourier = [];
        $tempService = [];
        $tempOngkir = [];
        $productIds = [];

        // Loop through carts to extract shipping information and product IDs
        foreach ($carts as $key => $row) {
            $tempOngkir[] = $row['shippingCost'];
            $tempCourier[] = $row['courier'];
            $tempService[] = $row['service'];

            foreach ($row['products'] as $product) {
                $productIds[] = $product['product_id'];
            }
        }

        // // Validate that all couriers, services, and shipping costs are provided
        // if (in_array(null, $tempCourier, true) || in_array(null, $tempService, true) || in_array(null, $tempOngkir, true)) {
        //     return redirect()->back()->with(['error' => 'Terjadi Kesalahan, Silahkan Periksa Ulang Kembali.']);
        // }

        // Fetch products based on collected product IDs
        $products = Product::whereIn('id', $productIds)->get();

        // Fetch customer data if authenticated, else fetch all provinces
        if (auth()->guard('customer')->check()) {
            $customer = auth()->guard('customer')->user()->load('district');
        } else {
            return redirect(route('customer.login'))->with('error', 'Silahkan login terlebih dahulu.');
        }

        // Get all provinces
        $provinces = Province::orderBy('name', 'ASC')->get();

        // Calculate subtotal, shipping cost, tax, and total
        $subtotal = collect($carts)->sum(function ($q) {
            return collect($q['products'])->sum(function ($product) {
                return $product['qty'] * $product['product_price'];
            });
        });

        // packaging and service

        $serviceCost = 1000;
        $packagingCost = collect($carts)->count() * 1000;

        $shippingCost = array_sum($tempOngkir);
        $resultTotal = $subtotal + $packagingCost + $serviceCost + $shippingCost;

        return view('ecommerce.checkout', compact('provinces', 'customer', 'carts', 'subtotal', 'resultTotal', 'shippingCost', 'products', 'serviceCost', 'packagingCost'));
    }

    public function updateAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'phone_number' => 'required|max:15',
            'address' => 'required|string',
            'gender' => 'required',
            'province_id' => 'required|exists:provinces,id',
            'city_id' => 'required|exists:cities,id',
            'district_id' => 'required|exists:districts,id',
            'password' => 'nullable|string|min:5'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validasi gagal, harap periksa kembali', 'errors' => $validator->errors(), 'input' => $request->all()], 400);
        }

        $user = auth()->guard('customer')->user();
        $data = $request->only('name', 'phone_number', 'address', 'gender', 'district_id');

        if ($request->password != '') {
            $data['password'] = $request->password;
        }
        
        if($user) {
            $user->update($data);
            return response()->json(['success' => 'Profil berhasil diperbaharui'], 200);
        } else {
            return response()->json(['error' => 'Terjadi Kesalahan, Silahkan Coba Lagi.'], 404);
        }
        // return redirect()->back()->with(['success' => 'Profil berhasil diperbaharui']);
    }

    public function getCity()
    {
        $cities = City::where('province_id', request()->province_id)->get();
        return response()->json(['status' => 'success', 'data' => $cities], 200);
    }

    public function getDistrict()
    {
        $districts = District::where('city_id', request()->city_id)->get();
        return response()->json(['status' => 'success', 'data' => $districts], 200);
    }

    // public function processCheckout(Request $request)
    // {
    //     // $this->validate($request, [
    //     //     'customer_name' => 'required|string|max:100',
    //     //     'customer_phone' => 'required',
    //     //     'email' => 'required|email',
    //     //     'customer_address' => 'required',
    //     //     'province_id' => 'required|exists:provinces,id',
    //     //     'destination' => 'required|exists:cities,id',
    //     //     'district_id' => 'required|exists:districts,id'
    //     // ]);

    //     //DATABASE TRANSACTION BERFUNGSI UNTUK MEMASTIKAN SEMUA PROSES SUKSES UNTUK KEMUDIAN DI COMMIT AGAR DATA BENAR BENAR DISIMPAN, JIKA TERJADI ERROR MAKA KITA ROLLBACK AGAR DATANYA SELARAS
    //     DB::beginTransaction();
    //     try {
    //         $auth = auth()->guard('customer')->user();
    //         $customer = Customer::where('email', $auth->email)->first();
    //         //JIKA DIA TIDAK LOGIN DAN DATA CUSTOMERNYA ADA
    //         if (!auth()->guard('customer')->check() && $customer) {
    //             return redirect()->back()->with(['error' => 'Silahkan Login Terlebih Dahulu']);
    //         }

    //         $carts = $this->getCarts();

    //         // // Ambil produk id
    //         // $product = collect($carts)->map(function($q) {
    //         //     return $q['product_id'];
    //         // });

    //         if (!auth()->guard('customer')->check()) {
    //             $password = Str::random(8); 
    //             $customer = Customer::create([
    //                 'name' => $auth->name,
    //                 'email' => $auth->email,
    //                 'password' => $password, 
    //                 'phone_number' => $auth->phone_number,
    //                 'address' => $auth->address,
    //                 'district_id' => $auth->district_id,
    //                 'activate_token' => Str::random(30),
    //                 'status' => false,
    //                 'created_at' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'),
    //                 'updated_at' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'),
    //             ]);
    //         }
            
    //         // count subtotal
    //         $subtotal = collect($carts)->sum(function ($q) {
    //             return collect($q['products'])->sum(function ($product) {
    //                 return $product['qty'] * $product['product_price'];
    //             });
    //         });

    //         $shippingCost = collect($carts)->sum(function($q) {
    //             return $q['shippingCost'];
    //         });

    //         $order = Order::create([
    //             'invoice' => strtoupper(Str::random(4)) . '-' . Carbon::now('Asia/Jakarta')->format('YmdHis'), 
    //             'customer_id' => $auth->id,
    //             'customer_name' => $auth->name,
    //             'customer_phone' => $auth->phone_number,
    //             'customer_address' => $auth->address,
    //             'district_id' => $auth->district_id,
    //             'subtotal' => $subtotal,
    //             'cost' => $shippingCost,
    //             'created_at' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'),
    //             'updated_at' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'),
    //             // 'shipping' => $shipping[0] . '-' . $shipping[1]
    //         ]);
            
    //         // update stock
    //         foreach($carts as $row){
    //             foreach($row['products'] as $product){
    //                 Product::where('id', $product['product_id'])->decrement('stock', $product['qty']);
    //             }
    //         }

    //         // order detail update
    //         foreach ($carts as $index => $row) {
    //             foreach ($row['products'] as $product) {

    //                 $services = explode(' - ', $row['service']);

    //                 OrderDetail::create([
    //                     'order_id' => $order->id,
    //                     'product_id' => $product['product_id'],
    //                     'seller_id' => $row['seller_id'],
    //                     'price' => $product['product_price'],
    //                     'qty' => $product['qty'],
    //                     'weight' => $product['weight'],
    //                     'shipping_courier' => $row['courier'],
    //                     'shipping_cost' => $row['shippingCost'],
    //                     'shipping_service' => $services[0] . ' - ' . $services[1],
    //                 ]);
    //             }
    //         }
            
    //         //TIDAK TERJADI ERROR, MAKA COMMIT DATANYA UNTUK MENINFORMASIKAN BAHWA DATA SUDAH FIX UNTUK DISIMPAN
    //         DB::commit();

    //         $carts = [];
    //         $cookie = cookie('e-carts', json_encode($carts), 2880);

    //         if (!auth()->guard('customer')->check()) {
    //             Mail::to($request->email)->send(new CustomerRegisterMail($customer, $password));
    //         }

    //         return response()->json([
    //             'success' => 'Checkout Berhasil', 
    //             'redirect' => route('customer.paymentForm', ['invoice' => $order->invoice])
    //         ])->cookie($cookie);
    //         // return redirect(route('customer.paymentForm', ['invoice' => $order->invoice]))->cookie($cookie);
    //     } catch (\Exception $e) {
    //         //JIKA TERJADI ERROR, MAKA ROLLBACK DATANYA
    //         DB::rollback();
    //         //DAN KEMBALI KE FORM TRANSAKSI SERTA MENAMPILKAN ERROR
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }

    public function processCheckout(Request $request)
    {
        DB::beginTransaction();
        try {
            // Step 1: Authenticate the customer
            $auth = auth()->guard('customer')->user();
            $customer = Customer::where('email', $auth->email)->first();
            
            // Check if the user is authenticated
            if (!auth()->guard('customer')->check() && $customer) {
                // return redirect()->back()->with(['error' => 'Silahkan Login Terlebih Dahulu']);
                return redirect(route('customer.login'))->with('error', 'Silahkan login terlebih dahulu');
            }

            $carts = $this->getCarts();

            // Step 2: If not logged in, create a new customer
            if (!auth()->guard('customer')->check()) {
                $password = Str::random(8); 
                $customer = Customer::create([
                    'name' => $auth->name,
                    'email' => $auth->email,
                    'password' => $password,
                    'phone_number' => $auth->phone_number,
                    'address' => $auth->address,
                    'district_id' => $auth->district_id,
                    'activate_token' => Str::random(30),
                    'status' => false,
                    'created_at' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'),
                ]);
            }
            
            // Step 3: Calculate subtotal and shipping cost
            $subtotal = collect($carts)->sum(function ($q) {
                return collect($q['products'])->sum(function ($product) {
                    return $product['qty'] * $product['product_price'];
                });
            });

            $shippingCost = collect($carts)->sum(function($q) {
                return $q['shippingCost'];
            });

            if($shippingCost === null || $shippingCost === 0){
                return response()->json(['error' => 'Harap pilih ekspedisi terlebih dahulu'], 400);
            }
            
            $serviceCost = 1000;
            $packagingCost = collect($carts)->count() * 1000;

            $order = Order::create([
                'invoice' => strtoupper(Str::random(4)) . '-' . Carbon::now('Asia/Jakarta')->format('YmdHis'), 
                'customer_id' => $auth->id,
                'customer_name' => $auth->name,
                'customer_phone' => $auth->phone_number,
                'customer_address' => $auth->address,
                'district_id' => $auth->district_id,
                'subtotal' => $subtotal,
                'cost' => $shippingCost,
                'service_cost' => $serviceCost,
                'packaging_cost' => $packagingCost,
                'created_at' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'),
            ]);

            foreach($carts as $row){
                foreach($row['products'] as $product){
                    Product::where('id', $product['product_id'])->decrement('stock', $product['qty']);
                    
                    $services = explode(' - ', $row['service']);
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'product_id' => $product['product_id'],
                        'seller_id' => $row['seller_id'],
                        'price' => $product['product_price'],
                        'qty' => $product['qty'],
                        'weight' => $product['weight'],
                        'shipping_courier' => $row['courier'],
                        'shipping_cost' => $row['shippingCost'],
                        'shipping_service' => $services[0] . ' - ' . $services[1],
                    ]);
                }
            }

            // Step 6: Commit the database transaction
            DB::commit();

            $carts = [];
            $cookie = cookie('e-carts', json_encode($carts), 2880);

            if (!auth()->guard('customer')->check()) {
                Mail::to($request->email)->send(new CustomerRegisterMail($customer, $password));
            }

            // Step 7: Generate Midtrans Snap token
            $snapToken = null;

            try {
                Config::$serverKey = config('services.midtrans.server_key');
                Config::$isProduction = config('services.midtrans.is_production');
                Config::$isSanitized = config('services.midtrans.is_sanitized');
                Config::$is3ds = config('services.midtrans.is_3ds');

                $order = Order::with(['details', 'details.product', 'customer', 'payment'])->where('invoice', $order->invoice)->first();
                
                $itemDetails = $order->details->map(function ($detail) {
                    return [
                        'id' => $detail->product_id,
                        'price' => $detail->price,
                        'quantity' => $detail->qty,
                        'name' => $detail->product->name,
                    ];
                })->toArray();
                
                $serviceCost = $order->service_cost;
                $itemDetails[] = [
                    'id' => 'Layanan',
                    'price' => $serviceCost,
                    'quantity' => 1,
                    'name' => 'Service',
                ];

                $packagingCost = $order->packaging_cost;
                $itemDetails[] = [
                    'id' => 'Kemasan',
                    'price' => $packagingCost,
                    'quantity' => 1,
                    'name' => 'Packaging',
                ];

                $shippingCost = $order->cost;
                $itemDetails[] = [
                    'id' => 'Ongkos Kirim',
                    'price' => $shippingCost,
                    'quantity' => 1,
                    'name' => 'Ongkos Kirim',
                ];

                $grossAmount = $order->subtotal + $packagingCost + $serviceCost + $order->cost;

                $params = [
                    'transaction_details' => [
                        'order_id' => $order->invoice,
                        'gross_amount' => $grossAmount,
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

                // Generate the Snap token and save it
                $snapToken = Snap::getSnapToken($params);
                $order->update(['snap_token' => $snapToken]);

                return response()->json([
                    'success' => 'Checkout berhasil!',
                    'snapToken' => $snapToken,
                    'redirect' => route('front.finish_checkout', $order->invoice)
                ])->cookie($cookie);
            } catch (\Exception $e) {
                DB::rollback(); // In case Snap token generation fails
                return response()->json(['error' => 'Snap token generation failed: ' . $e->getMessage()], 500);
            }

            // Step 8: Clear the cart and return the checkout view with the Snap token
            $carts = [];
            $cookie = cookie('e-carts', json_encode($carts), 2880);
            return view('ecommerce.checkout', compact('snapToken'))->cookie($cookie);
            
        } catch (\Exception $e) {
            DB::rollback(); // If any error occurs, rollback the transaction
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function checkoutFinish($invoice)
    {
        $order = Order::with(['customer', 'district.city', 'details.product'])->where('invoice', $invoice)->first();

        if (!$order) {
            return redirect()->back()->withErrors('Order not found.');
        }

        // Get customer details
        $custCity = City::find($order->district_id);
        $custProvince = $custCity ? Province::find($custCity->province_id) : null;
        $temp = auth()->guard('customer')->user()->load('district');
        $custDistrict = $temp->district_id ? District::find($temp->district_id) : null;

        // Get product and seller details from the order
        $detailOrder = OrderDetail::with(['product'])->where('order_id', $order->id)->get();
        $productIds = $detailOrder->pluck('product_id')->toArray();
        $products = Product::whereIn('id', $productIds)->get();
        $sellerIds = $detailOrder->pluck('seller_id')->toArray();
        $sellers = Seller::whereIn('id', $sellerIds)->get();
        
        // Create associative arrays for easy lookup
        $sellerPhones = $sellers->pluck('phone_number', 'id')->toArray();
        $sellerNames = $sellers->pluck('name', 'id')->toArray();
        $sellerAddress = $sellers->pluck('address', 'id')->toArray();
        $districtIds = $sellers->pluck('district_id')->toArray();

        // Get district names and associated city IDs
        $districts = District::whereIn('id', $districtIds)->get();
        $districtNames = $districts->pluck('name', 'id')->toArray();
        $cityIds = $districts->pluck('city_id')->toArray();
        $cities = City::whereIn('id', $cityIds)->get();
        $cityNames = $cities->pluck('name', 'id')->toArray();
        $provinceIds = $cities->pluck('province_id')->toArray();
        $provinces = Province::whereIn('id', $provinceIds)->get();
        $provinceNames = $provinces->pluck('name', 'id')->toArray();

        // Combine seller information
        $combinedInfo = [];
        foreach ($sellers as $seller) {
            $district = $districts->where('id', $seller->district_id)->first();
            $city = $district ? $cities->where('id', $district->city_id)->first() : null;
            $province = $city ? $provinces->where('id', $city->province_id)->first() : null;

            $combinedInfo[] = [
                'name' => $seller->name,
                'phone' => $seller->phone_number,
                'address' => $seller->address,
                'district' => $district ? $district->name : 'Unknown',
                'city' => $city ? $city->name : 'Unknown',
                'province' => $province ? $province->name : 'Unknown',
            ];
        }

        // Transform the order object
        $transformedOrder = $order->toArray();
        $transformedOrder['sellers'] = $combinedInfo;

        // Create a Carbon instance
        $carbonDate = Carbon::parse($order->created_at);

        // Format the date
        $formattedDate = $carbonDate->translatedFormat('l, d F Y H:i');
        
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

        return view('ecommerce.checkout_finish', compact('order', 'detailOrder', 'custDistrict', 'custCity', 'custProvince', 'transformedOrder', 'formattedDate'));
    }
}
