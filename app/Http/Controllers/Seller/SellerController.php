<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Order;
use App\Customer;
use App\Product;
use App\Category;
use App\User;
use App\Seller;
use App\SellerWithdrawal;
use App\Province;
use App\OrderDetail;
use DataTables;
use Carbon\Carbon;

class SellerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('seller');
        $this->middleware(function ($request, $next) {

            $this->user = Auth::user();

            return $next($request);
        });
    }

    public function verifySellerRegistration($token)
    {
        $seller = Seller::where('activate_token', $token)->first();
        if ($seller) {

            $seller->update([
                'activate_token' => null,
                'status' => 1
            ]);
            return redirect(route('login'))->with(['success' => 'Verifikasi Berhasil, Silahkan Login']);
        }
        return redirect(route('login'))->with(['error' => 'Invalid Verifikasi Token']);
    }

    public function index()
    {
        $sellerId = Auth::guard('seller')->user()->id;

        $detailOrder = OrderDetail::selectRaw('
            COALESCE(SUM(CASE WHEN status = 6 THEN (price * qty + (price * qty * 0.10) + shipping_cost) END), 0) AS turnover, 
            COALESCE(COUNT(CASE WHEN status = 0 THEN price * qty END), 0) AS newOrder,
            COALESCE(COUNT(CASE WHEN status = 3 THEN price * qty END), 0) AS processOrder,
            COALESCE(COUNT(CASE WHEN status = 4 THEN price * qty END), 0) AS shipping,
            COALESCE(COUNT(CASE WHEN status = 5 THEN (price * qty + (price * qty * 0.10) + shipping_cost) END), 0) AS arriveOrder,
            COALESCE(COUNT(CASE WHEN status = 6 THEN price * qty END), 0) AS completeOrder')
            ->where('seller_id', $sellerId)
            ->get();

        // get total omset
        $totalOmset = SellerWithdrawal::where('seller_id', $sellerId)
            ->where('status', 'disetujui')
            ->sum('amount');

        $customers = Customer::get();
        $categories = Category::get();
        $products = Product::where('seller_id', $sellerId)->get();

        // $topSelling = DB::table('order_items')
        //     ->select('product_id', DB::raw('SUM(quantity) as total_sales'))
        //     ->join('products', 'order_items.product_id', '=', 'products.id')
        //     ->where('products.seller_id', $sellerId)
        //     ->groupBy('product_id')
        //     ->orderBy('total_sales', 'desc')
        //     ->limit(5) // Limit to top 5 products
        //     ->get();

        $topSelling = OrderDetail::with(['product' => function ($query) use ($sellerId) {
            $query->where('seller_id', $sellerId);
        }])
            ->select(
                'product_id', 
                DB::raw('SUM(qty) as total_sales'),
                DB::raw('COUNT(qty) as totalSales')
            )
            ->where('seller_id', $sellerId)
            ->groupBy('product_id')
            ->orderBy('total_sales', 'desc')
            ->limit(5)
            ->get();
        
        // Prepare data for Highcharts
        $chartDataTopSelling = $topSelling->map(function($orderDetail) {
            return [
                'name' => $orderDetail->product->category->name,
                'y' => (float)$orderDetail->total_sales,
            ];
        });

        $dataTopSelling = $topSelling->map(function($orderDetail) {
            return [
                'name' => $orderDetail->product->name,
                'image' => $orderDetail->product->image,
                'stok' => $orderDetail->product->stock,
                'sales' => $orderDetail->total_sales,
            ];
        });

        $orders = Order::with([
            'details' => function($q) use ($sellerId){
                $q->where('seller_id', $sellerId)->where('status', 6);
            }
        ])
        ->whereHas('details', function($query) use ($sellerId){
            $query->where('seller_id', $sellerId)->where('status', 6);
        })
        ->get();

        // Prepare data for Highcharts (e.g., sales per month)
        $monthlySales = $orders->flatMap(function($order) {
            return $order->details->map(function($detail) {
                return [
                    'month' => Carbon::parse($detail->created_at)->locale('id')->translatedFormat('F'),
                    'total' => ($detail->qty * $detail->price) + 1000 + 1000 + $detail->shipping_cost
                ];
            });
        })->groupBy('month')->map(function ($monthDetails) {
            return $monthDetails->sum('total');
        });

        $minProduct = Product::where('seller_id', auth()->guard('seller')->user()->id)->where('stock', '<=', 50)->get();
        
        return view('seller.home', compact('customers', 'categories', 'products', 'detailOrder', 'orders', 'monthlySales', 'totalOmset', 'chartDataTopSelling', 'dataTopSelling'));
    }

    public function getDatatablesIndex(Request $request){
        // Fetch orders with details having status 0 for the logged-in seller
        $newOrders = Order::with(['details' => function ($query) {
            $query->where('status', 0)
                  ->where('seller_id', auth()->guard('seller')->user()->id);
        }])->get();
    
        // Flatten the orders and details into a single collection
        $detailsCollection = $newOrders->flatMap(function ($order) {
            return $order->details->map(function ($detail) use ($order) {
                return [
                    'date' => Carbon::parse($detail->created_at)->locale('id')->translatedFormat('l, d F Y H:i'),
                    'invoice' => $order->invoice,
                    'customer_name' => $order->customer_name,
                    'product' => $detail->product->name,
                    'total' => 'Rp ' . number_format($detail->qty * $detail->price, 0, ',', '.'),
                    'status' => $detail->status_label,
                    'status_display' => '<span class="badge badge-light">' . $detail->status_label . '</span>',
                    'action' => '
                        <a href="' . route('orders.newView', $order->invoice) . '" class="btn btn-sm btn-primary">Detail <span class="fa fa-eye ml-1"></span></a>
                    ',
                ];
            });
        });
    
        return DataTables::of($detailsCollection)
                ->rawColumns(['status_display','action']) // Ensure HTML is not escaped in action column
                ->make(true);
    }      

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $provinces = Province::orderBy('created_at', 'DESC')->get();
        return view('seller.seller.create', compact('provinces')); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $this->validate($request, [
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required',
            'password' => 'required',
            'email' => 'required|email',
            'customer_address' => 'required|string',
            'province_id' => 'required|exists:provinces,id',
            'city_id' => 'required|exists:cities,id',
            'district_id' => 'required|exists:districts,id'
        ]);

        if(Seller::where('email', $request->email)->exists()){
            return redirect()->back()->with(['error' => 'Email Sudah Ada']);
        } else {
            try {
                if (!auth()->guard('seller')->check()) {
                    $password = Str::random(8); 
                    $seller = Seller::create([
                        'name' => $request->customer_name,
                        'email' => $request->email,
                        'password' => $password, 
                        'phone_number' => $request->customer_phone,
                        'address' => $request->customer_address,
                        'district_id' => $request->district_id,
                        'activate_token' => Str::random(30),
                        'status' => false
                    ]);
                }

                // return redirect(route('seller.index'))->with(['success' => 'Registrasi Member Berhasil, Silahkan Cek Email.']);
                if (!auth()->guard('seller')->check()) {
                    Mail::to($request->email)->send(new SellerRegisterMail($seller, $password));
                }
                return redirect(route('seller.index'))->with(['success' => 'Registrasi Member Berhasil, Silahkan Cek Email.']);

            } catch (\Exception $e) {
                return redirect()->back()->with(['error' => $e->getMessage()]);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $customer = Customer::find($id);
        $provinces = Province::orderBy('name', 'ASC')->get();
        return view('customer.edit', compact('customer', 'provinces'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'phone_number' => 'required|max:15',
            'address' => 'required|string',
            'district_id' => 'required|exists:districts,id',
            'password' => 'nullable|string'
        ]);
        
        // $user = auth()->guard('customer')->user();

        $customer = Customer::find($id);

        $data = $request->only('name', 'phone_number', 'address', 'district_id');

        if ($request->password != '') {
            $data['password'] = $request->password;
        }

        $customer->update([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'district_id' => $request->district_id,
        ]);

        return redirect(route('customer.index'))->with(['success' => 'Customer Berhasil Diperbaharui']);

        // return redirect(route('customer.index'))->with(['success' => 'Data Produk Diperbaharui']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer = Customer::find($id); 
        $customer->delete();
        return redirect(route('customer.index'))->with(['success' => 'Customer Berhasil Dihapus']);
    }

    public function accountSetting($id){
        $seller = auth()->guard('seller')->user()->load('district');
        // dd($seller);
        $provinces = Province::orderBy('name', 'ASC')->get();
        return view('seller.setting.setting', compact('seller', 'provinces'));
    }

    public function postAccountSetting(Request $request, $id)
    {
        try {
            $this->validate($request, [
                'name' => 'required|string|max:100',
                'phone_number' => 'required|max:15',
                'address' => 'required|string',
                'district_id' => 'required|exists:districts,id',
                'password' => 'nullable|string|min:5'
            ]);
    
            $user = Auth::guard('seller')->user();
            $data = $request->only('name', 'phone_number', 'address', 'district_id');
    
            if ($request->password != '') {
                $data['password'] = $request->password;
            }
            $user->update($data);
            
            return response()->json(['success' => true, 'message' => 'Profil berhasil diperbarui'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
