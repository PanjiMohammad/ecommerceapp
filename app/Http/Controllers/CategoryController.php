<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Category;
use DataTables;
use Carbon\Carbon;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $parent = Category::getParent()->orderBy('name', 'ASC')->get();
        return view('admin.categories.index', compact('parent'));
    }

    public function getDatatables(Request $request){
        $category = Category::with(['parent'])->orderBy('created_at', 'ASC');

        return DataTables::of($category)
            ->addColumn('action', function ($cat) {
                return '
                    <button type="button" class="btn btn-sm btn-primary edit-category" data-category-id="'. $cat->id .'"><span class="fa fa-pencil"></span></button>
                    <button type="button" class="btn btn-sm btn-danger delete-category" data-category-id="'. $cat->id .'" data-category-name="' . $cat->name . '"><span class="fa fa-trash"></span></button>
                    <form id="deleteForm{{ $cat->id }}" action="'. route('category.destroy', $cat->id) .'" method="post" class="d-none">
                        '. method_field('DELETE') . csrf_field() .'
                    </form>
                ';
            })
            ->addColumn('parent_name', function ($cat) {
                return $cat->parent ? $cat->parent->name : '-';
            })
            ->editColumn('formattedDate', function($cat) {
                return Carbon::parse($cat->created_at)->locale('id')->translatedFormat('Y-m-d');
            })
            ->rawColumns(['action', 'parent_name', 'formattedDate'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'name' => 'required|string|max:50|unique:categories'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $request->request->add(['slug' => Str::slug($request->name)]);
        $category = Category::create($request->except('_token'));

        return response()->json(['success' => true, 'message' => 'Kategori Baru Ditambahkan', 'category' => $category]);
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
        $category = Category::find($id); 
        $parent = Category::getParent()->orderBy('name', 'ASC')->get(); 
        
        return response()->json(['category' => $category, 'parent' => $parent]);
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
            'name' => 'required|string|max:50|unique:categories,name,' . $id,
            'parent_id' => 'nullable|exists:categories,id'
        ]);

        $category = Category::findOrFail($id);
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->parent_id = $request->parent_id;
        $category->save();

        // Optionally, you can return a JSON response here
        return response()->json(['success' => true, 'message' => 'Kategori berhasil diperbaharui', 'category' => $category]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            // FUNGSI INI AKAN MEMBENTUK FIELD BARU YANG BERNAMA child_count dan product_count
            $category = Category::withCount(['child', 'product'])->findOrFail($id);
            
            if ($category->child_count == 0 && $category->product_count == 0) {
                $category->delete();
                return response()->json(['success' => true, 'message' => 'Kategori Berhasil Dihapus']);
            }

            return response()->json(['success' => false, 'message' => 'Kategori Ini Memiliki Anak Kategori atau Produk']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan, silakan coba lagi nanti.'], 500);
        }
    }

}
