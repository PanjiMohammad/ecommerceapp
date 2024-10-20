<?php

namespace App\Http\Controllers\Auth;

use App\Seller;
use App\User;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use App\Mail\SellerResetPasswordMail;
use Mail;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    // use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    // protected $redirectTo = RouteServiceProvider::HOME;

    public function forgotPasswordForm(){
        return view('auth.passwords.reset');
    }

    public function sendPasswordResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // // Cari user berdasarkan ID
        // $user = User::where('email', $request->email)->first();
        
        // if ($user) {
        //     // Hash password baru menggunakan bcrypt
        //     $hashedPassword = bcrypt('admin');
            
        //     // Update password user
        //     $user->password = $hashedPassword;
        //     $user->save();
            
        //     return 'Password berhasil diupdate!';
        // }

        // return 'User tidak ditemukan!';

        try {
            $data = Seller::where('email', $request->email)->first();

            if($data != null){
                $seller = Seller::find($data->id);
                $password = Str::random(8); 
                $seller->update([
                    'password' => $password,
                    'activate_token' => Str::random(30),
                    'status' => 0
                ]);

                Mail::to($request->email)->send(new SellerResetPasswordMail($seller, $password));

                return response()->json(['success' => true, 'message' => 'Atur Ulang Kata Sandi Berhasil, Silahkan Cek Email.']);
            } else {
                return response()->json(['error' => true, 'message' => 'Atur Ulang Kata Sandi Gagal, Email Tidak Terdaftar.']);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
