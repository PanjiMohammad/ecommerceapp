<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use DataTables;
use Mail;
use App\Mail\CustomerRegisterMail;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.customer.index');
    }

    public function getDatatables(Request $request){
        $customers = Customer::orderBy('created_at', 'DESC');

        return DataTables::of($customers)
            ->addColumn('action', function ($customer) {
                return '
                    <a href="'. route('consumen.edit', $customer->id) .'" class="btn btn-sm btn-primary" title="Edit Konsumsen '. $customer->name .'"><span class="fa fa-pencil"></span></a>
                    <button type="button" class="btn btn-sm btn-danger delete-customer" data-customer-id="'. $customer->id .'" title="Hapus Konsumsen '. $customer->name .'"><span class="fa fa-trash"></span></button>
 
                    <form id="deleteForm{{ $customer->id }}" action="'. route('consumen.destroy', $customer->id) .'" method="post" class="d-none">
                        '. method_field('DELETE') . csrf_field() .'
                    </form>
                ';
            })
            ->editColumn('status', function ($customer) {
                if ($customer->status == 1) {
                    return '<span class="badge badge-success">Aktif</span>';
                }

                if ($customer->status == 2) {
                    return '<span class="badge badge-danger">Tidak Aktif</span>';
                }

                if($customer->status !== 1 && $customer->status !== 2){
                    return '<span class="badge badge-secondary">Belum Aktivasi</span>';
                }
            })
            ->rawColumns(['action', 'status'])
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
        return view('admin.customer.create', compact('provinces')); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
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

        if(Customer::where('email', $request->email)->exists()){
            return response()->json(['error' => 'Email Sudah Ada'], 400);
        } else {
            try {
                $password = Str::random(8); 
                $customer = Customer::create([
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
                // Mail::to($request->email)->send(new CustomerRegisterMail($customer, $password));

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
        $customer = Customer::find($id);
        $provinces = Province::orderBy('name', 'ASC')->get();
        return view('admin.customer.edit', compact('customer', 'provinces'));
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
        // Validate incoming data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers,email,' . $request->customer_id,
            'gender' => 'required',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string',
            'district_id' => 'required|exists:districts,id',
            'password' => 'nullable|string',
        ]);
        
        try {
            // Update customer data
            $customer = Customer::findOrFail($request->customer_id);
            
            $customer->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'gender' => $request->gender,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'status' => $request->status,
                'district_id' => $request->district_id,
            ]);

            return response()->json(['success' => true, 'message' => 'Konsumen berhasil diperbarui', 'customer' => $customer], 200);
        } catch (\Exception $e) {
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
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Konsumen Tidak Ditemukan'], 404);
        }

        try {
            $customer->delete();
            return response()->json(['success' => true, 'message' => 'Konsumen Berhasil Dihapus'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Konsumen Gagal Dihapus'], 500);
        }
    }
}
