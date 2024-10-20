<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use App\Order;
use App\Customer;
use App\Product;
use App\Category;
use App\User;
use App\Seller;
use App\Province;

use App\Mail\SellerRegisterMail;
use App\Mail\CustomerRegisterMail;
use Mail;

use DataTables;

class SellerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function index()
    {   
        return view('admin.seller.index');
    }

    public function getDatatables(Request $request)
    {
        $sellers = Seller::orderBy('created_at', 'DESC')->get();
        
        return DataTables::of($sellers)
            ->addColumn('action', function ($seller) {
                return '
                    <a href="'. route('seller.edit', $seller->id) .'" class="btn btn-sm btn-primary"><span class="fa fa-pencil"></span></a>
                    <button type="button" class="btn btn-sm btn-danger delete-seller" data-seller-id="'. $seller->id .'"><span class="fa fa-trash"></span></button>
 
                    <form id="deleteForm{{ $seller->id }}" action="'. route('seller.destroy', $seller->id) .'" method="post" class="d-none">
                        '. method_field('DELETE') . csrf_field() .'
                    </form>
                ';
            })
            ->editColumn('address', function ($seller) {
                return $seller->address . ', Kecamatan ' . $seller->district->name;
            })
            ->editColumn('status', function ($seller) {
                if ($seller->status == 1) {
                    return '<span class="badge badge-success">Aktif</span>';
                }

                if ($seller->status == 2) {
                    return '<span class="badge badge-danger">Tidak Aktif</span>';
                }
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    public function create()
    {
        $provinces = Province::orderBy('created_at', 'DESC')->get();
        return view('admin.seller.create', compact('provinces')); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'name' => 'required|string|max:100',
        //     'phone_number' => 'required',
        //     'gender' => 'required',
        //     'email' => 'required|email',
        //     'address' => 'required|string',
        //     'province_id' => 'required|exists:provinces,id',
        //     'city_id' => 'required|exists:cities,id',
        //     'district_id' => 'required|exists:districts,id'
        // ]);

        $validator = Validator::make([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'status' => $request->status,
            'gender' => $request->gender,
            'province_id' => $request->province_id,
            'city_id' => $request->city_id,
            'district_id' => $request->district_id,
        ], [
            'name' => 'required|string|max:100',
            'email' => 'required|email',
            'phone_number' => 'required',
            'address' => 'required|string',
            'status' => 'required',
            'gender' => 'required',
            'province_id' => 'required|exists:provinces,id',
            'city_id' => 'required|exists:cities,id',
            'district_id' => 'required|exists:districts,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validasi gagal, harap periksa kembali', 'errors' => $validator->errors(), 'input' => $request->all()], 400);
        }

        if(Seller::where('email', $request->email)->exists()){
            return redirect()->back()->with(['error' => 'Email Sudah Ada']);
        } else {
            try {
                $password = Str::random(8); 
                $seller = Seller::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => $password, 
                    'gender' => $request->gender,
                    'phone_number' => $request->phone_number,
                    'address' => $request->address,
                    'district_id' => $request->district_id,
                    'activate_token' => Str::random(30),
                    'status' => $request->status
                ]);

                // kirim ke email
                // Mail::to($request->email)->send(new SellerRegisterEmail($customer, $password));

                return response()->json(['success' => 'Konsumen baru berhasil tersimpan'], 200);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
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
        $seller = Seller::find($id);
        $provinces = Province::orderBy('name', 'ASC')->get();
        return view('admin.seller.edit', compact('seller', 'provinces'));
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

        $seller = Seller::findOrFail($id);

        // Validate incoming data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers,email,' . $id,
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string',
            'district_id' => 'required|exists:districts,id',
            'password' => 'nullable|string',
        ]);

        // Update customer data
        $seller->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'gender' => $request->gender,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'status' => $request->status,
            'district_id' => $request->district_id,
        ]);

        return response()->json(['success' => true, 'message' => 'Penjual Berhasil Diperbaharui', 'seller' => $seller]);

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
        $seller = Customer::find($id); 
        if (!$seller) {
            return response()->json(['success' => false, 'message' => 'Konsumen Tidak Ditemukan'], 404);
        }

        try {
            $seller->delete();
            return response()->json(['success' => true, 'message' => 'Konsumen Berhasil Dihapus'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Konsumen Gagal Dihapus'], 500);
        }
    }
}
