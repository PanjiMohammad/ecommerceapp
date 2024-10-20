<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use App\Customer;
use App\Order;
use App\OrderDetail;
use App\User;
use App\Mail\CustomerResetPasswordMail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Mail;


class LoginController extends Controller
{
    public function loginForm()
    {
        if (auth()->guard('customer')->check()) return redirect(route('customer.dashboard'));
        return view('ecommerce.login');
    }

    public function forgotPassword()
    {
        return view('ecommerce.forgotpassword');
    }

    public function resetPassword(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);    

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = Customer::where('email', $request->email)->first();

        if($data != null){
            $customer = Customer::find($data->id);
            $password = Str::random(8); 
            $customer->update([
                'password' => $password,
                "activate_token" => Str::random(30),
                'status' => 0
                
            ]);

            Mail::to($request->email)->send(new CustomerResetPasswordMail($customer, $password));

            return response()->json(['success' => 'Atur ulang kata sandi berhasil, silahkan cek email.'], 200);
        } else {
            return response()->json(['error' => 'Email tidak terdaftar.'], 404);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $credentials = $request->only('email', 'password');
        $credentials['status'] = 1;

        $customer = Customer::where('email', $request->email)->first();
        
        if($customer->status !== 1) {
            return response()->json(['error' => 'Terjadi kesalahan saat mencoba masuk, Silahkan hubungi admin.'], 404);
        }

        if($customer === null) {
            return response()->json(['error' => 'Akun tidak terdaftar, Silahkan registrasi member.'], 404);
        }

        if (auth()->guard('customer')->attempt($credentials)) {
            return response()->json(['success' => 'Login berhasil'], 200);
        } else {
            return response()->json(['error' => 'Email / Password Salah'], 401);
        }
    }

    public function dashboard()
    {
        //Terdapat kondisi dengan menggunakan CASE, dimana jika kondisinya terpenuhi dalam hal ini status 
        //maka subtotal akan di-sum, kemudian untuk shipping dan complete hanya di count order

        // Fetch orders and include the sum of shipping_cost in the pending calculation
        // $orders = Order::selectRaw('
        //     COALESCE(sum(CASE WHEN status = 0 THEN (subtotal + (subtotal * 0.10)) + cost END), 0) as pending,
        //     COALESCE(count(CASE WHEN status = 3 THEN subtotal END), 0) as shipping,
        //     COALESCE(count(CASE WHEN status = 4 THEN subtotal END), 0) as completeOrder')->where('customer_id', auth()->guard('customer')->user()->id)
        //     ->get();

        $Ids = Order::where('customer_id', auth()->guard('customer')->user()->id)->pluck('id')->toArray();

        $orders = OrderDetail::selectRaw('
            COALESCE(SUM(CASE WHEN status = 0 THEN price * qty + (price * qty * 0.10) + shipping_cost END), 0) as pending,
            COALESCE(COUNT(CASE WHEN status = 2 THEN price * qty + (price * qty * 0.10) + shipping_cost END), 0) as confirm,
            COALESCE(COUNT(CASE WHEN status = 3 THEN price * qty + (price * qty * 0.10) + shipping_cost END), 0) as process,
            COALESCE(COUNT(CASE WHEN status = 4 THEN price * qty + (price * qty * 0.10) + shipping_cost END), 0) as shipping,
            COALESCE(COUNT(CASE WHEN status = 5 THEN price * qty + (price * qty * 0.10) + shipping_cost END), 0) as arrive,
            COALESCE(COUNT(CASE WHEN status = 6 THEN price * qty + (price * qty * 0.10) + shipping_cost END), 0) as completeOrder')
        ->whereIn('order_id', $Ids)->get();
        
        return view('ecommerce.dashboard', compact('orders'));
    }

    public function logout()
    {
        Auth::guard('customer')->logout();
        return response()->json(['success' => 'Berhasil Logout', 'redirect' => route('customer.login')], 200);

        // Set a flash message
        // session()->flash('success', 'Berhasil Logout');

        // // Redirect to the login route
        // return redirect()->route('customer.login');
    }
}
