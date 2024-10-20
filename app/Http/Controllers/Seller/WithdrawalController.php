<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Order;
use App\Customer;
use App\Product;
use App\Category;
use App\User;
use App\Seller;
use App\Province;
use App\OrderDetail;
use App\OrderCancelledDetail;
use DataTables;
use Carbon\Carbon;
use App\SellerWithdrawal;
use App\SellerBankAccount;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\WithdrawalsFundExport;

class WithdrawalController extends Controller
{
    public function index()
    {
        $sellerId = auth()->guard('seller')->user()->id;
        
        // hitung semua total (harga * kuantiti) berdasarkan seller id
        $details = OrderDetail::where('seller_id', $sellerId)->where('status', 6)->sum(DB::raw('qty * price'));

        // hitung semua total berdasarkan seller id & berdasarkan status yang disetujui
        $withdrawal = SellerWithdrawal::where('seller_id', $sellerId)->where('status', 'disetujui')->sum('amount');

        // kalkulasikan total dari order detail dikurangi jumlah penarikan uang yang statusnya sudah disetujui
        $activeAmount = $details - $withdrawal;
        $nonActiveAmount = OrderCancelledDetail::where('seller_id', $sellerId)->sum(DB::raw('price * qty'));

        // hitung jumlah penarikan per minggu
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        $countWithdrawal = SellerWithdrawal::where('seller_id', $sellerId)
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek]) 
                ->count();

        return view('seller.withdraw.index', compact('activeAmount', 'nonActiveAmount', 'countWithdrawal'));
    }

    public function indexAccount()
    {
        $sellerId = auth()->guard('seller')->user()->id;
        $datas = SellerBankAccount::where('seller_id', $sellerId)
        ->get()
        ->map(function ($data) {
            $data->formatted_account_number = '****' . substr($data->account_number, 4);  // Only replace the first 4 digits with ****
            return $data;
        });

        return view('seller.withdraw.account', compact('datas'));
    }

    public function withdrawalsDatatables(Request $request)
    {
        $start = $request->query('start_date') ? Carbon::parse($request->query('start_date'))->startOfDay() : Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
        $end = $request->query('end_date') ? Carbon::parse($request->query('end_date'))->endOfDay() : Carbon::now()->endOfMonth()->format('Y-m-d H:i:s');

        $withdrawals = SellerWithdrawal::with(['seller', 'bankAccount'])
            ->where('seller_id', auth()->guard('seller')->user()->id)
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'DESC')
            ->get();

        return DataTables::of($withdrawals)
            ->addColumn('sellerName', function ($withdrawal) {
                return $withdrawal->bankAccount->account_name;
            })
            ->editColumn('amount', function($withdrawal) {
                return 'Rp ' . number_format($withdrawal->amount, 0, ',', '.');
            })
            ->addColumn('bankName', function ($withdrawal) {
                return $withdrawal->bankAccount->bank_name_label;
            })
            ->editColumn('status', function($withdrawal){
                return $withdrawal->status_label;
            })
            ->editColumn('account_number', function($withdrawal){
                $format = '*****' . substr($withdrawal->bankAccount->account_number, 5);
                return $format;
            })
            ->addColumn('formattedDated', function($withdrawal) {
                return Carbon::parse($withdrawal->created_at)->locale('id')->translatedFormat('l, d F Y');
            })
            ->rawColumns(['sellerName', 'formattedDated', 'bankName', 'amount', 'status', 'account_number'])  // Use rawColumns (plural) here
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validasi Error, Harap periksa kembali', 'errors' => $validator->errors(), 'input' => $request->all()], 400);
        }

        try {
            $seller = auth()->guard('seller')->user();

            $bankAccount = SellerBankAccount::where('seller_id', $seller->id)
                ->where('bank_name', $request->bank_name)
                ->orwhere('account_number', $request->account_number)
                ->first();

            if(!$bankAccount){
                $bankAccount = SellerBankAccount::create([
                    'seller_id' => $seller->id,
                    'bank_name' => $request->bank_name,
                    'account_name' => $seller->name,
                    'account_number' => $request->account_number,
                    'created_at' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'),
                ]);

                return response()->json(['success' => true, 'message' => 'Rekening baru berhasil ditambahkan'], 200);
            } else {
                return response()->json(['error' => 'Rekening sudah ada'], 409);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function detailAccount($id)
    {
        $account = SellerBankAccount::find($id);

        return response()->json([
            'bank_name' => $account->bank_name_label,
            'account_name' => $account->account_name,
            'account_id' => $account->id,
            'account_number' => '*****' . substr($account->account_number, 5),
            'created_at' => Carbon::parse($account->created_at)->locale('id')->translatedFormat('l, d F Y'),
        ], 200);
    }

    public function selectAccount(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'account_id' => 'required|integer|exists:seller_bank_accounts,id',
            ]);

            // Assume the authenticated user is the one selecting the account
            $seller = auth()->guard('seller')->user();
            $seller->selected_account_id = $validated['account_id'];
            $seller->save();

            // Return a success response
            return response()->json(['success' => true, 'message' => 'Berhasil memilih rekening'], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return a custom error response on validation failure
            return response()->json(['error' => 'Harap pilih rekening terlebih dahulu'], 422);
        } catch (\Exception $e) {
            // Handle any other errors
            return response()->json(['error' => 'Terjadi kesalahan, silakan coba lagi.'], 500);
        }
    }

    public function storeWithdrawals(Request $request)
    {
        try {
            $seller = auth()->guard('seller')->user();

            // request amount
            $amount = $request->amount;

            $details = OrderDetail::where('seller_id', $seller->id)->where('status', 6)->sum(DB::raw('qty * price'));
            $amountAccept = SellerWithdrawal::where('seller_id', $seller->id)->where('status', 'disetujui')->sum('amount');

            $totalAmount = $details - $amountAccept;

            if($amount > $totalAmount){
                return response()->json(['error' => 'Dana tidak mencukupi'], 400);
            } else {
                $withdrawal = SellerWithdrawal::where('seller_id', $seller->id)
                    ->where('bank_account_id', $request->account_id)
                    ->where('amount', $amount)
                    ->first();

                if(!$withdrawal){
                    SellerWithdrawal::create([
                        'seller_id' => $seller->id,
                        'bank_account_id' => $request->account_id,
                        'amount' => $amount,
                        'status' => 'menunggu',
                        'created_at' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'),
                        'updated_at' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'),
                    ]);

                    return response()->json(['success' => 'Berhasil mengajukan penarikan, harap menunggu konfirmasi dari super admin'], 200);
                } else {
                    return response()->json(['error' => 'Penarikan dengan nominal serupa sedang dalam proses'], 409);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function withdrawReport($daterange){
        $date = explode('+', $daterange); 

        $start = Carbon::parse($date[0])->format('Y-m-d') . ' 00:00:01';
        $end = Carbon::parse($date[1])->format('Y-m-d') . ' 23:59:59';

        $sellerId = auth()->guard('seller')->user()->id;

        $withdrawal = SellerWithdrawal::where('seller_id', $sellerId)->whereBetween('created_at', [$start, $end])->where('status', '<>', 'null')->orderBy('created_at', 'DESC')->get();

        $startFormattedDate = Carbon::parse($start)->locale('id')->translatedFormat('l, d F Y');
        $endFormattedDate = Carbon::parse($end)->locale('id')->translatedFormat('l, d F Y');
        $formattedDate = $startFormattedDate . ' - ' . $endFormattedDate;

        // get total where status disetujui
        $totalAmount = SellerWithdrawal::where('seller_id', $sellerId)->where('status', 'Disetujui')->sum('amount');

        $pdf = PDF::loadView('seller.withdraw.withdrawpdf', compact('withdrawal', 'formattedDate', 'totalAmount'));

        $startpdf = Carbon::parse($date[0])->locale('id')->translatedFormat('l, d F Y');
        $endpdf = Carbon::parse($date[1])->locale('id')->translatedFormat('l, d F Y');

        $fileName = 'Laporan Penarikan Dana Periode ' . $startpdf . ' sampai ' . $endpdf . '.pdf';
        $filePath = storage_path('app/public/withdrawals/' . $fileName);

        // Save the PDF temporarily
        $pdf->save($filePath);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'PDF berhasil diunduh',
                'file_url' => asset('storage/withdrawals/' . $fileName)
            ], 200);
        }

        // Download the PDF directly
        return $pdf->download($fileName);
    }

    public function withdrawReportExcel($daterange)
    {
        try {
            $date = explode('+', $daterange);

            $start = Carbon::parse($date[0])->format('Y-m-d') . ' 00:00:01';
            $end = Carbon::parse($date[1])->format('Y-m-d') . ' 23:59:59';

            $sellerId = auth()->guard('seller')->user()->id;

            $startDate = Carbon::parse($date[0])->locale('id')->translatedFormat('l, d F Y');
            $endDate = Carbon::parse($date[1])->locale('id')->translatedFormat('l, d F Y');

            // count total
            $total = SellerWithdrawal::where('seller_id', $sellerId)->where('status', 'disetujui')->whereBetween('created_at', [$start, $end])->sum('amount');
            $grandTotal = 'Rp ' . number_format($total, 0, ',', '.');

            // File name for the report
            $fileName = 'Laporan Penarikan Dana Periode ' . $startDate . ' sampai ' . $endDate . '.xlsx';

            // Path to store the Excel file in storage/app/public/reports/excel
            $filePath = 'reports/excel/' . $fileName;

            // Generate the Excel report
            $export = new WithdrawalsFundExport($start, $end, $sellerId, $grandTotal);

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

            // return Excel::download(new WithdrawalsFundExport($start, $end, $sellerId), 'Laporan Penarikan Dana Periode ' . $start . ' sampai ' . $end . '.xlsx');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to generate file.', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $account = SellerBankAccount::find($id);

        if ($account) {
            $withdrawals = SellerWithdrawal::where('bank_account_id', $id)->delete();
            $account->delete();
        }

        if (!$account) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Rekening tidak ditemukan'], 404);
            }
        }

        if (request()->ajax()) {
            return response()->json(['success' => 'Rekening berhasil dihapus'], 200);
        } else {
            return response()->json(['error' => 'Rekening Gagal Dihapus']);
        }

    }

}
