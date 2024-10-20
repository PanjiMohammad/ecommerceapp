<?php

namespace App\Http\Controllers;
use App\Product;
use App\Category;
use Illuminate\Support\Str;
use File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Jobs\ProductJob;
use DataTables;
use Carbon\Carbon;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $category = Category::orderBy('name', 'DESC')->get();
        return view('admin.products.index', compact('category'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getDatatables(Request $request)
    {
        $products = Product::with('category')->orderBy('created_at', 'DESC')->get();

        return DataTables::of($products)
            ->addColumn('action', function ($product) {
                return '
                    <button type="button" class="btn btn-sm btn-info detail-product" data-product-id="'. $product->id .'"><span class="fa fa-eye"></span></button>
                ';
            })
            ->editColumn('image', function ($product) {
                return '<img src="'. asset('/products/' . $product->image) .'" alt="'. $product->name .'" class="rounded" style="max-width: 150px; height: 100px; width: 110px; display: block; object-fit: contain;">';
            })
            ->editColumn('description', function ($product) {
                return $product->description; // CKEditor content is directly returned
            })
            ->editColumn('status', function ($product) {
                return $product->status_label; // Use the status label accessor
            })
            ->rawColumns(['action', 'image', 'description', 'seller', 'status'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //QUERY UNTUK MENGAMBIL SEMUA DATA CATEGORY
        $category = Category::orderBy('name', 'DESC')->get();
        return view('admin.products.create', compact('category'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'description' => 'required',
            'category_id' => 'required|exists:categories,id', 
            'price' => 'required|integer',
            'weight' => 'required|integer',
            'image' => 'required|image|mimes:png,jpeg,jpg,webp' 
        ]);

        //JIKA FILENYA ADA
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('/products/');
            $file->move($destinationPath, $filename);

            // $destinationPath = public_path('products');
            // $file = $request->file('image');
            // $filename = time().Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
            // $file->storeAs($destinationPath, $filename);

            $product = Product::create([
                'name' => $request->name,
                'slug' => $request->name,
                'category_id' => $request->category_id,
                'description' => $request->description,
                'image' => $filename, 
                'price' => $request->price,
                'weight' => $request->weight,
                'status' => $request->status
            ]);

            return redirect(route('product.index'))->with(['success' => 'Produk Baru Ditambahkan']);
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
        $product = Product::findOrFail($id);

        $weight = $product->weight;

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
        $start = $product->start_date !== null ? Carbon::parse($product->start_date)->locale('id')->translatedFormat('l, d F Y H:i:s') : '';
        $end = $product->end_date !== null ? Carbon::parse($product->end_date)->locale('id')->translatedFormat('l, d F Y H:i:s') : '';
        $promos = $start . ' - ' . $end;

        return response()->json([
            'seller' => $product->seller->name,
            'name' => $product->name,
            'description' => $product->description,
            'category' => $product->category->name,
            'price' => 'Rp ' . number_format($product->price, 0, ',', '.'),
            'image' => asset('/products/' . $product->image),
            'status' => $product->status_label,
            'weight' => $weightDisplay,
            'stock' => $product->stock == 0 ? 'Habis' : $product->stock . ' item',
            'type' => $product->type === null ? '-' : ucwords($product->type),
            'promo_date' => $promos,
            'promo_price' => $product->promo_price !== null || $product->promo_price != 0 ? 'Rp ' . number_format($product->promo_price, 0, ',', '.') : '-',
            'storage_instructions' => $product->storage_instructions,
            'storage_period' => $product->storage_period,
            'units' => $product->units,
            'packaging' => $product->packaging,
            'serving_suggestions' => $product->serving_suggestions,
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
        $product = Product::find($id); 
        $category = Category::orderBy('name', 'DESC')->get(); 
        return view('admin.products.edit', compact('product', 'category')); 
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
            'description' => 'required',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|integer',
            'weight' => 'required|integer',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:png,jpeg,jpg' //IMAGE BISA NULLABLE
        ]);

        $product = Product::find($id);
        $filename = $product->image;
        $seller = Auth::guard('seller')->user();
    
        //JIKA ADA FILE GAMBAR YANG DIKIRIM
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('/products/');
            $file->move($destinationPath, $filename);

            File::delete($destinationPath . $product->image);
        }

        $product->update([
            'name' => $request->name,
            'slug' => $request->name,
            'description' => $request->description,
            'seller_id' => $seller->id,
            'seller_name' => $seller->name,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'weight' => $request->weight,
            'stock' => $request->stock,
            'image' => $filename,
            'status' => $request->status
        ]);
        return response()->json(['success' => true, 'message' => 'Produk Berhasil Diperbarui']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id); 
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Produk Tidak Ketemu'], 404);
        }
        File::delete(public_path('/products/' . $product->image));
        $product->delete();
        return response()->json(['success' => true, 'message' => 'Produk Berhasil Dihapus']);
    }

    public function massUploadForm()
    {
        $category = Category::orderBy('name', 'DESC')->get();
        return view('admin.products.index', compact('category'));
    }

    public function massUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'file' => 'required|mimes:xlsx' 
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
            //ADAPUN PADA DISPATCH KITA MENGIRIMKAN DUA PARAMETER SEBAGAI INFORMASI
            //YAKNI KATEGORI ID DAN NAMA FILENYA YANG SUDAH DISIMPAN
            ProductJob::dispatchNow($request->category_id, $filename);
            return response()->json(['success' => true, 'message' => 'Produk Berhasil Ditambah']);
        }

        return response()->json(['success' => false, 'message' => 'Tidak Ada File Yang diupload']);
    }
}
