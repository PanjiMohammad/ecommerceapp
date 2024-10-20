<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SellerBankAccount extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['seller_id', 'bank_name', 'account_name', 'account_number'];
    protected $table = 'seller_bank_accounts';

    protected $appends = ['bank_name_label', 'bank_name_image'];

    /**
     * Get the seller that owns the withdrawal request.
     */
    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    // accessor
    public function getBankNameLabelAttribute()
    {
        $bankName = strtolower($this->bank_name);

        switch ($bankName) {
            case 'bca':
                return 'Bank BCA';
            case 'bri':
                return 'Bank BRI';
            case 'bni':
                return 'Bank BNI';
            case 'mandiri':
                return 'Bank Mandiri';
            case 'ocbc':
                return 'Bank OCBC';
            case 'dki':
                return 'Bank DKI';
            case 'btpn':
                return 'Bank BTPN';
            case 'cimb_niaga':
                return 'Bank CIMB Niaga';
            case 'danamon':
                return 'Bank Danamon';
            default:
                return 'Bank Lain';
        }
    }

    // image accessor
    public function getBankNameImageAttribute()
    {
        $bankLogos = [
            'bca' => 'ecommerce/img/logo/bca.png',
            'bri' => 'ecommerce/img/logo/bri.png',
            'bni' => 'ecommerce/img/logo/bni.jpg',
            'mandiri' => 'ecommerce/img/logo/mandiri.png',
            'ocbc' => 'ecommerce/img/logo/ocbc.png',
            'dki' => 'ecommerce/img/logo/dki.jfif',
            'btpn' => 'ecommerce/img/logo/btpn.png',
            'cimb_niaga' => 'ecommerce/img/logo/cimb.png',
            'danamon' => 'ecommerce/img/logo/danamon.png',
        ];

        $bankName = strtolower($this->bank_name);

        return asset($bankLogos[$bankName] ?? 'ecommerce/img/logo/default.png');
    }

    public function withdrawals()
    {
        return $this->hasMany(SellerWithdrawal::class, 'bank_account_id');
    }
}
