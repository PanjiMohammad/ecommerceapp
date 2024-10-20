<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Jobs\ProductJob;
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
use App\Imports\ProductImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
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
        $category = Category::whereNotNull('parent_id')->orderBy('name', 'DESC')->get();

        return view('seller.products.index', compact('category'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function datatables(Request $request)
    {
        $products = Product::where('seller_id', auth()->guard('seller')->user()->id)
            ->where('type', null)
            ->with('category')
            ->orderBy('created_at', 'DESC')
            ->get();

        return DataTables::of($products)
            ->addColumn('action', function ($product) use (&$index) {
                static $index = 0;
                $index++;

                return '
                    <a href="'. route('product.newEdit', $product->id) .'" class="btn btn-sm btn-primary" title="Edit Produk ' . $product->name . '"><span class="fa fa-pencil"></span></a>
                    <button type="button" class="btn btn-sm btn-danger delete-product ml-1" data-index="'.$index.'" data-product-id="'. $product->id .'" title="Hapus Produk ' . $product->name . '"><span class="fa fa-trash"></span></button>
                    <button type="button" class="btn btn-sm btn-info detail-product ml-1" data-index="'.$index.'" data-product-id="'. $product->id .'" title="Detail Produk ' . $product->name . '"><span class="fa fa-eye"></span></button>
 
                    <form id="deleteForm{{ $product->id }}" action="'. route('product.newDestroy', $product->id) .'" method="post" class="d-none">
                        '. method_field('DELETE') . csrf_field() .'
                    </form>
                ';
            })
            ->editColumn('productName', function($product){
                return $product->name . '<span class="ml-2">' . $product->status_type . '</span>';
            })
            ->editColumn('image', function ($product) {
                return '<img src="'. asset('/products/' . $product->image) .'" alt="'. $product->name .'" class="img-thumbnail rounded" style="width: 110px; height: 100px; object-fit: contain; display: block;">';
            })
            ->editColumn('description', function ($product) {
                return $product->description; 
            })
            ->editColumn('stock', function ($product) {
                return $product->stock . ' item'; 
            })
            ->editColumn('status', function ($product) {
                return $product->status_label; 
            })
            ->rawColumns(['action', 'image', 'description', 'status', 'stock', 'productName'])
            ->make(true);
    }

    public function create()
    {
        //QUERY UNTUK MENGAMBIL SEMUA DATA CATEGORY
        $category = Category::orderBy('name', 'DESC')->get();
        return view('seller.products.create', compact('category'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Start a try-catch block to handle any exceptions that might occur
        try {
            // Validate the incoming request
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100',
                'description' => 'required',
                'category_id' => 'required|exists:categories,id',
                'price' => 'required',
                'weight' => 'required',
                'stock' => 'required',
                'image' => 'required|image|mimes:png,jpeg,jpg,webp'
            ]);

            // Check if the validation fails
            if ($validator->fails()) {
                return response()->json(['error' => 'Validasi gagal, Harap periksa kembali', 'errors' => $validator->errors(), 'input' => $request->all()], 422);
            }

            // Get the authenticated seller
            $seller = Auth::guard('seller')->user();

            // Process price and promo price if available
            $processedCurrency = null;
            $processedCurrency1 = null;

            if($request->price !== '' || $request->promo_price !== ''){
                $processedCurrency = intval(str_replace('.', '', str_replace(',', '.', $request->price)));
                $processedCurrency1 = intval(str_replace('.', '', str_replace(',', '.', $request->promo_price)));
            }

            // Handle file upload
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('/products/');
                $file->move($destinationPath, $filename);

                // Create the product
                $product = Product::create([
                    'name' => $request->name,
                    'slug' => Str::slug($request->name),
                    'category_id' => $request->category_id,
                    'seller_id' => $seller->id,
                    'seller_name' => $seller->name,
                    'description' => $request->description,
                    'image' => $filename,
                    'price' => $processedCurrency,
                    'weight' => $request->weight,
                    'stock' => $request->stock,
                    'type' => $request->type,
                    'storage_instructions' => $request->storage_instructions,
                    'storage_period' => $request->storage_period,
                    'packaging' => $request->packaging,
                    'units' => $request->units,
                    'serving_suggestions' => $request->serving_suggestions,
                    'status' => $request->status,
                    'promo_price' => $processedCurrency1,
                    'start_date' => $request->estimate_input_start,
                    'end_date' => $request->estimate_input_end,
                ]);

                return response()->json(['success' => true, 'message' => 'Produk berhasil ditambahkan'], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'Gagal mengunggah gambar'], 422);
            }

        } catch (\Exception $e) {
            // Catch any exception that occurs and return an error response
            return response()->json(['error' => $e->getMessage()], 500);
        }
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
            'name' => $product->name,
            'seller' => $product->seller->name,
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

        $weights = explode(' - ', $product->weight);
        $weight1 = $weights[0];
        $weight2 = $weights[1] ?? null;

        return view('seller.products.edit', compact('product', 'category', 'weight1', 'weight2')); 
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
        try {
            $this->validate($request, [
                'name' => 'required|string|max:100',
                'description' => 'required',
                'category_id' => 'required|exists:categories,id',
                'price' => 'required',
                'weight' => 'required|string',
                'stock' => 'required|integer',
                'image' => 'nullable|image|mimes:png,jpeg,jpg,webp' // IMAGE CAN BE NULLABLE
            ]);
    
            $product = Product::find($request->product_id);
            if (!$product) {
                return redirect()->back()->with('error', 'Produk tidak ditemukan.');
            }
    
            $filename = $product->image;
            $seller = Auth::guard('seller')->user();

            // Get the input value
            $currency = $request->price;
            $processedCurrency = intval(str_replace('.', '', str_replace(',', '.', $currency)));
        
            // IF IMAGE FILE IS UPLOADED
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('/products/');
                $file->move($destinationPath, $filename);
    
                // Delete the old image file
                File::delete($destinationPath . $product->image);
            }

            // convert
            if($request->type == 'promo'){
                if($request->promo_price == null || $request->promo_price == ''){
                    return response()->json(['error' => 'Harap diisi harga promo'], 400);
                } else if ($request->estimate_input_start == null && $request->estimate_input_end == null) {
                    return response()->json(['error' => 'Harap diisi estimasi promo'], 400);
                }
            }

            $processedCurrencyPromo = intval(str_replace('.', '', str_replace(',', '.', $request->promo_price)));
    
            // Update the product details
            $product->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name), // Generate a slug for the name
                'description' => $request->description,
                'seller_id' => $seller->id,
                'seller_name' => $seller->name,
                'category_id' => $request->category_id,
                'price' => $processedCurrency,
                'weight' => $request->weight,
                'stock' => $request->stock,
                'type' => $request->type,
                'storage_instructions' => $request->storage_instructions,
                'storage_period' => $request->storage_period,
                'units' => $request->units,
                'packaging' => $request->packaging,
                'serving_suggestions' => $request->serving_suggestions,
                'image' => $filename,
                'status' => $request->status,
                'promo_price' => $processedCurrencyPromo,
                'start_date' => $request->estimate_input_start,
                'end_date' => $request->estimate_input_end,
            ]);
    
            return response()->json(['success' => true, 'message' => 'Produk berhasil diperbarui'], 200);
        } catch (\Exception $e) {
            // Log the error message for debugging
            Log::error('Product Update Error: ' . $e->getMessage());
    
            // Return a JSON response with an error message
            return response()->json(['error' => $e->getMessage()], 500);
        }
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
        return view('seller.products.index', compact('category'));
    }

    public function massUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'file' => 'required',
            'extension' => 'required||in:xlsx'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation error, Harap isi field yang kosong.', 'errors' => $validator->errors(), 'input' => $request->all()]);
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '-product.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/');
            $file->move($destinationPath, $filename);

            $directory = public_path('/products/');
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true, true);
            }

            try {
                $files = (new ProductImport)->toArray($destinationPath . $filename);

                foreach ($files as $sheet) {
                    foreach ($sheet as $index => $row) {
                        // Skip rows with insufficient columns
                        if (count($row) < 6) {
                            Log::error("Row $index does not have enough columns: ", $row);
                            continue;
                        }

                        $productName = $row[0];
                        $existingProduct = Product::where('name', $productName)
                            ->orWhere('slug', Str::slug($productName))
                            ->first();

                        if ($existingProduct) {
                            Log::error("Product Already Exists: '{$productName}' at row $index");
                            return response()->json(['error' => "Produk '{$row[0]}' sudah ada pada baris $index."]);
                        }

                        $imageUrl = trim($row[4]);
                        Log::info("Processing URL: $imageUrl on row $index");

                        if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                            Log::error("Invalid URL: '$imageUrl' at row $index");
                            continue;
                        }

                        $imageContent = @file_get_contents($imageUrl);
                        if ($imageContent === false) {
                            Log::error("Failed to download image from URL: '$imageUrl' at row $index");
                            continue;
                        }

                        $filename = time() . Str::random(6) . '.' . pathinfo($imageUrl, PATHINFO_EXTENSION);
                        $imagePath = $directory . '/' . $filename;

                        // Save the image content to the products directory
                        if (!@file_put_contents($imagePath, $imageContent)) {
                            Log::error("Failed to save image to path: $imagePath for row $index");
                            continue;
                        }

                        try {
                            Product::create([
                                'name' => $row[0],
                                'slug' => Str::slug($row[0]),
                                'category_id' => $request->category_id,
                                'seller_id' => auth()->guard('seller')->user()->id,
                                'seller_name' => auth()->guard('seller')->user()->name,
                                'description' => $row[1],
                                'price' => $row[2],
                                'weight' => $row[3],
                                'image' => $filename,
                                'stock' => $row[5],
                                'type' => $row[6] === 'undefined' ? null : $row[6],
                                'storage_instructions' => $row[7],
                                'storage_period' => $row[8],
                                'units' => $row[9],
                                'packaging' => $row[10],
                                'serving_suggestions' => $row[11],
                                'status' => true
                            ]);
                        } catch (\Exception $e) {
                            Log::error("Failed to create product record for row $index: " . $e->getMessage());
                            return response()->json(['error' => $e->getMessage()], 500);
                        }
                    }
                }

                File::delete($destinationPath . $filename);

                return response()->json(['success' => true, 'message' => 'Berhasil menambahkan produk.'], 200);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        return response()->json(['success' => false, 'message' => 'Tidak ada file yang diupload']);
    }

}
