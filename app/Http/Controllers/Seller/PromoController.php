<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Jobs\PromoProductJob;
use App\Promo;
use App\Product;
use App\Category;
use App\Seller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use File;
use DataTables;
use Carbon\Carbon;
use App\Imports\PromoProductImport; 
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;


class PromoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $category = Category::orderBy('name', 'DESC')->get();
        return view('seller.promos.index', compact('category'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function datatables(Request $request)
    {
        $promos = Product::where('seller_id', auth()->guard('seller')->user()->id)
            ->where('type', 'promo')
            ->with('category')
            ->orderBy('created_at', 'DESC')
            ->get();

        return DataTables::of($promos)
            ->addColumn('action', function ($promo) use (&$index) {
                static $index = 0;
                $index++;

                return '
                    <a href="'. route('promoProduct.newEdit', $promo->id) .'" class="btn btn-sm btn-primary"><span class="fa fa-pencil"></span></a>
                    <button type="button" class="btn btn-sm btn-info detail-promo ml-1" data-index="'.$index.'" data-promo-id="'. $promo->id .'" title="Detail Produk '. $promo->name .'"><span class="fa fa-eye"></span></button>
 
                    <form id="deleteForm{{ $promo->id }}" action="'. route('promoProduct.newDestroy', $promo->id) .'" method="post" class="d-none">
                        '. method_field('DELETE') . csrf_field() .'
                    </form>
                ';
            })
            ->editColumn('rangeDate', function ($promo) {
                $startDate = Carbon::parse($promo->start_date)->locale('id')->translatedFormat('l, d F Y H:i:s');
                $endDate = Carbon::parse($promo->end_date)->locale('id')->translatedFormat('l, d F Y H:i:s');
                return $startDate . ' WIB - <br> ' . $endDate . ' WIB <br> ' . $promo->status_promo;
            })
            ->editColumn('infoProduct', function ($promo) {
                // set weight product
                $weight = $promo->weight;
            
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

                // discount
                if($promo->promo_price !== $promo->price){
                    $price = (float) $promo->price;
                    $promoPrice = (float) $promo->promo_price;
                    $discount = '<span class="ml-2 badge badge-info">' . round((($price - $promoPrice) / $price) * 100) . '%' . '</span>';
                } else {
                    $discount = '';
                }

                // filter stock
                if($promo->stock == 0) {
                    $stock = '<p> Jumlah stok : ' . $promo->stock . ' item <span class="badge badge-danger">Habis</span></p>';
                } else {
                    $stock = '<p> Jumlah stok : ' . $promo->stock . ' item</p>';
                }

                $image = '<img src="'. asset('/products/' . $promo->image) .'" alt="'. $promo->name .'" class="img-thumbnail" style="width: 110px; height: 100px; margin-right: 15px;">';
                $name = '<p class="font-weight-bold">' . $promo->name . '</p>';
                $weight = '<p>' . $weightDisplay . '</p>';
                $price = '<p> Harga : Rp ' . number_format($promo->price, 0, ',', '.') . '</p>';
                $promoPrice = 'Harga Promo : Rp ' . number_format($promo->promo_price, 0, ',', '.') . $discount;
                $description = '<p>' . $promo->description . '</p>';
                return '<div style="display: flex;">' . $image . '<div>' . $name . $stock . $weight . $price . $promoPrice . $description . '</div></div>';
            })
            ->editColumn('status', function ($promo) {
                return $promo->status_label; // Use the status label accessor
            })
            ->rawColumns(['action', 'infoProduct', 'description', 'status', 'rangeDate'])
            ->make(true);
    }

    public function create()
    {
        //QUERY UNTUK MENGAMBIL SEMUA DATA CATEGORY
        $category = Category::whereNotNull('parent_id')->orderBy('name', 'DESC')->get();
        return view('seller.promos.create', compact('category'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'required',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required',
            'weight' => 'required|integer',
            'stock' => 'required|integer',
            'image' => 'required|image|mimes:png,jpeg,jpg,webp'
        ]);
    
        // Check if the validation fails
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }
    
        $seller = Auth::guard('seller')->user();
    
        // Get the input value
        $currency = $request->price;
        $processedCurrency = intval(str_replace('.', '', str_replace(',', '.', $currency)));
    
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('/products/');
            $file->move($destinationPath, $filename);
    
            $promo = Promo::create([
                'name' => $request->name,
                'slug' => $request->name,
                'category_id' => $request->category_id,
                'seller_id' => $seller->id,
                'description' => $request->description,
                'image' => $filename,
                'price' => $processedCurrency,
                'weight' => $request->weight,
                'stock' => $request->stock,
                'status' => $request->status,
                'start_date' => $request->estimate_input_start,
                'end_date' => $request->estimate_input_end,
            ]);
    
            return response()->json(['success' => true, 'message' => 'Produk Baru Ditambahkan']);
        } else {
            return response()->json(['success' => false, 'message' => 'Gagal Mengunggah Gambar']);
        }
    
        return response()->json(['success' => false, 'message' => 'Gagal Tambah Produk']);
    }

    // product.newIndex

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $promo = Product::findOrFail($id);

        $weight = $promo->weight;

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

        // carbon
        $start = $promo->start_date !== null ? Carbon::parse($promo->start_date)->locale('id')->translatedFormat('l, d F Y H:i:s') : '';
        $end = $promo->end_date !== null ? Carbon::parse($promo->end_date)->locale('id')->translatedFormat('l, d F Y H:i:s') : '';
        $promos = ($start) . ' - ' . $end;
    
        return response()->json([
            'name' => $promo->name,
            'description' => $promo->description,
            'category' => $promo->category->name,
            'price' => 'Rp ' . number_format($promo->price, 0, ',', '.'),
            'image' => asset('/products/' . $promo->image),
            'status' => $promo->status_label,
            'weight' => $weightDisplay,
            'stock' => $promo->stock == 0 ? 'Habis' : $promo->stock . ' item',
            'type' => $promo->type === null ? '-' : ucwords($promo->type),
            'promo_date' => $promos,
            'promo_price' => 'Rp ' . number_format($promo->promo_price, 0, ',', '.'),
            'storage_instructions' => $promo->storage_instructions,
            'storage_period' => $promo->storage_period,
            'units' => $promo->units,
            'packaging' => $promo->packaging,
            'serving_suggestions' => $promo->serving_suggestions,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $promo = Product::find($id);
        $category = Category::whereNotNull('parent_id')->orderBy('name', 'DESC')->get(); 
        
        return view('seller.promos.edit', compact('promo', 'category')); 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'required',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required',
            'weight' => 'required|string',
            'stock' => 'required|integer',
            'promo_price' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation error, Harap periksa isian kembali', 'errors' => $validator->errors(), 'input' => $request->all()], 422);
        }

        $promo = Product::find($request->product_id);
        if (!$promo) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan.');
        }
        $filename = $promo->image;
        $seller = Auth::guard('seller')->user();

        $currency = $request->price;
        $processedCurrency = intval(str_replace('.', '', str_replace(',', '.', $currency)));
    
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('/products/');
            $file->move($destinationPath, $filename);

            File::delete($destinationPath . $promo->image);
        }

        $type = $request->type ?? null;
        $promoPrice = $type ? intval(str_replace('.', '', str_replace(',', '.', $request->promo_price))) : null;
        $startDate = $type ? $request->estimate_input_start : null;
        $endDate = $type ? $request->estimate_input_end : null;

        $promo->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'seller_id' => $seller->id,
            'seller_name' => $seller->name,
            'category_id' => $request->category_id,
            'price' => $processedCurrency,
            'weight' => $request->weight,
            'stock' => $request->stock,
            'type' => $type,
            'storage_instructions' => $request->storage_instructions,
            'storage_period' => $request->storage_period,
            'units' => $request->units,
            'packaging' => $request->packaging,
            'serving_suggestions' => $request->serving_suggestions,
            'image' => $filename,
            'status' => $request->status,
            'promo_price' => $promoPrice,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        return response()->json(['success' => 'Produk berhasil diperbarui'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $promo = Promo::find($id); 
        if (!$promo) {
            return response()->json(['success' => false, 'message' => 'Produk Tidak Ketemu'], 404);
        }
        File::delete(public_path('/products/' . $promo->image));
        $promo->delete();
        return response()->json(['success' => true, 'message' => 'Produk Berhasil Dihapus']);
    }

    public function massUploadForm()
    {
        $category = Category::orderBy('name', 'DESC')->get();
        return view('seller.products.index', compact('category'));
    }

    public function massUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'file' => 'required|mimes:xlsx',
            'estimate_input_start' => 'required',
            'estimate_input_end' => 'required'
        ]);

        // Inside your controller method
        $token = $request->input('_token');

        if (!hash_equals(Session::token(), $token)) {
            return response()->json(['success' => false, 'message' => 'CSRF token mismatch.']);
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '-product.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/');
            $file->move($destinationPath, $filename);

            //BUAT JADWAL UNTUK PROSES FILE TERSEBUT DENGAN MENGGUNAKAN JOB
            //ADAPUN PADA DISPATCH KITA MENGIRIMKAN 4 PARAMETER SEBAGAI INFORMASI
            //YAKNI KATEGORI ID DAN NAMA FILENYA YANG SUDAH DISIMPAN
            if($request->estimate_input_start == null && $request->estimate_end == null){
                return response()->json(['error' => 'Tidak Ada Waktu Promo'], 400);
            } else {
                PromoProductJob::dispatchNow($request->category_id, $filename, $request->estimate_input_start, $request->estimate_input_end);
                return response()->json(['success' => 'Produk Berhasil Ditambah'], 200);
            }
        } else {
            return response()->json(['error' => 'Produk Gagal Ditambah']);
        }

        return response()->json(['error' => 'Tidak Ada File Yang diupload']);
    }
}
