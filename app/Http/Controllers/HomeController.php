<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Order;
use App\OrderDetail;
use App\Customer;
use App\Product;
use App\Province;
use App\Category;
use App\User;
use App\Seller;
use App\Payment;
use App\SellerWithdrawal;
use DataTables;
use App\Helpers\SettingsHelper;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $datas = OrderDetail::selectRaw('COALESCE(sum(CASE WHEN status = 6 THEN (((price * qty) + (price * qty * 0.10)) + shipping_cost) * 0.3 * 0.1 END), 0) as turnover, 
            COALESCE(count(CASE WHEN status = 0 THEN price * qty END), 0) as newOrder,
            COALESCE(count(CASE WHEN status = 3 THEN price * qty END), 0) as processOrder,
            COALESCE(count(CASE WHEN status = 4 THEN price * qty END), 0) as shipping,
            COALESCE(count(CASE WHEN status = 5 THEN price * qty END), 0) as arriveOrder,
            COALESCE(count(CASE WHEN status = 6 THEN price * qty END), 0) as completeOrder')
        ->get();

        $customers = Customer::get();
        $categories = Category::get();
        $products = Product::get();
        $sellers = Seller::get();

        // hitung semua jumlah amount pada table payments berdasarkan status transaksi (settlement & capture)
        $tempPayment = DB::table('payments')
            ->where('transaction_status', 'settlement')
            ->orwhere('transaction_status', 'capture')
            ->sum('amount');

        // hitung semua jumlah amount pada table withdraw berdasarkan status yang disetujui
        $tempWithdraw = SellerWithdrawal::where('status', 'disetujui')->sum('amount');

        // totalOmset = payment dikurangi semua withdraw yang sudah disetujui
        $totalOmset = $tempPayment - $tempWithdraw;

        $withdrawals = SellerWithdrawal::where('status', '<>', null)->orderBy('created_at', 'DESC')->get();

        $topSelling = OrderDetail::with(['product'])
            ->select(
                'product_id', 
                DB::raw('SUM(qty) as total_sales'),
                DB::raw('COUNT(qty) as totalSales')
            )
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

        $orders = Order::with(['details'])->orderBy('created_at', 'DESC')->get();

        // Prepare data for Highcharts (e.g., sales per month)
        $monthlySales = $orders->flatMap(function($order) {   
            $serviceCost = $order->service_cost;
            $packagingCost = $order->packaging_cost;
            $shippingCost = $order->cost;
            return $order->details->map(function($detail) use ($serviceCost, $packagingCost, $shippingCost) {
                return [
                    'month' => Carbon::parse($detail->created_at)->locale('id')->translatedFormat('F'),
                    'total' => ($detail->qty * $detail->price) + $serviceCost + $packagingCost + $shippingCost
                ];
            });
        })->groupBy('month')->map(function ($monthDetails) {
            return $monthDetails->sum('total');
        });

        $totals = Order::get()->sum('service_cost');

        return view('admin.home', compact('datas', 'orders', 'totals', 'customers', 'categories', 'products', 'sellers', 'totalOmset', 'withdrawals', 'chartDataTopSelling', 'dataTopSelling', 'monthlySales'));
    }

    public function indexWithdraw()
    {
        return view('admin.withdrawals.index');
    }

    public function indexWithdrawDatatables(Request $request)
    {
        $withdrawals = SellerWithdrawal::with(['seller', 'bankAccount'])
            ->where('status', 'menunggu')
            ->orderBy('created_at', 'DESC')
            ->get();

        return DataTables::of($withdrawals)
            ->addColumn('sellerName', function($withdrawal) {
                return $withdrawal->bankAccount->account_name ?? 'Tidak Diketahui';
            })
            ->editColumn('bankName', function($withdrawal) {
                return $withdrawal->bankAccount->bank_name_label;
            })
            ->editColumn('accountNumber', function($withdrawal) {
                $formatNumber = '****' . substr($withdrawal->bankAccount->account_number, 4);  // Only replace the first 4 digits with ****
                return $formatNumber;
            })
            ->editColumn('status', function($withdrawal) {
                return $withdrawal->status_label;
            })
            ->addColumn('action', function ($withdrawal) {
                $approveUrl = route('admin.updateWithdraw', ['id' => $withdrawal->id, 'status' => 'disetujui']);
                $rejectUrl = route('admin.updateWithdraw', ['id' => $withdrawal->id, 'status' => 'ditolak']);
    
                return '
                    <form class="withdraw-action-form" action="'.$approveUrl.'" method="POST" style="display: inline;">
                        '. csrf_field() .'
                        <button type="submit" class="btn btn-success btn-sm">Setuju <i class="fa-solid fa-check ml-1"></i></button>
                    </form>
                    <form class="withdraw-action-form" action="'.$rejectUrl.'" method="POST" style="display: inline;">
                        '. csrf_field() .'
                        <button type="submit" class="btn btn-danger btn-sm">Tolak <i class="fa-solid fa-xmark ml-1"></i></button>
                    </form>
                ';
            })
            ->rawColumns(['action', 'sellerName', 'bankName', 'status', 'accountNumber'])
            ->make(true);
    }

    public function updateStatusWithdraw($id, $status)
    {
        try {
            $withdrawal = SellerWithdrawal::find($id);

            if(!$withdrawal){
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
            }

            $withdrawal->update(['status' => $status]);
            return response()->json(['success' => true, 'message' => 'Status penarikan berhasil diperbarui.']);
        } catch (\Exception $e) {
            \Log::error('Error updating withdrawal status: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function accountSetting($id){
        $admin = auth()->guard('web')->user();
        return view('admin.setting.setting', compact('admin'));
    }
    
    public function postAccountSetting(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required|string|max:100',
                'email' => 'required',
                'password' => 'nullable|string|min:6'
            ]);
    
            $user = User::find($request->user_id);
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);

            return response()->json(['success' => 'Profil berhasil diperbarui'], 200);
            // return redirect()->back()->with(['success' => 'Profil berhasil diperbaharui']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function contentSetting()
    {
        // Load current settings
        $aboutContent = SettingsHelper::get('about_us', '');
        $logo = SettingsHelper::get('logo', '');
        return view('admin.setting.content', compact('aboutContent', 'logo'));
    }

    public function postContentSetting(Request $request)
    {
        $request->validate([
            'about' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp,ico|max:2048'
        ]);

        if ($request->has('hot_deals_visibility')) {
            // Update the "about us" content
            SettingsHelper::set('about_us', $request->input('about'));

            // Handle the logo upload
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('logos', 'public');
                SettingsHelper::set('logo', $logoPath);
            }
            SettingsHelper::setHotDealsVisibility($request->hot_deals_visibility);

            return response()->json(['success' => true, 'message' => 'Konten berhasil diperbaharui']);
        } else {
            return response()->json(['success' => false, 'message' => 'Konten Gagal diperbaharui']);
        }
    }
}
