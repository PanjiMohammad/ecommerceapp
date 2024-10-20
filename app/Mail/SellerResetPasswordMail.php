<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Seller; 

class SellerResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    protected $seller;
    protected $randomPassword;

    //MEMINTA DATA BERUPA INFORMASI CUSTOMER DAN RANDOM PASSWORD YANG BELUM DI-ENCRYPT
    public function __construct($seller, $randomPassword)
    {
        $this->seller = $seller;
        $this->randomPassword = $randomPassword;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Atur Ulang Kata Sandi Anda')
            ->view('seller.emails.resetpassword')
            ->with([
                'seller' => $this->seller,
                'password' => $this->randomPassword
            ]);
    }
}
