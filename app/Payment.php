<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $guarded = [];
    protected $appends = ['status_label', 'status_label_new', 'acquirer_name', 'payment_method_name'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getStatusLabelAttribute()
    {
        if ($this->status == 0) {
            return '<span class="badge badge-secondary">Menunggu Konfirmasi</span>';
        }
        return '<span class="badge badge-success">Diterima</span>';
    }

    public function getStatusLabelNewAttribute()
    {
        if ($this->status == 0) {
            return '<span>Menunggu Konfirmasi</span>';
        }
        return '<span>Diterima</span>';
    }

    public function getAcquirerNameAttribute()
    {
        if ($this->acquirer == 'bca') {
            return '<span class="text-uppercase">BCA</span>';
        } else if ($this->acquirer == 'mandiri'){
            return '<span>Mandiri</span>';
        } else if ($this->acquirer == 'bri'){
            return '<span class="text-uppercase">BRI</span>';
        } else if ($this->acquirer == 'bni'){
            return '<span class="text-uppercase">BNI</span>';
        } else if ($this->acquirer == 'gopay'){
            return '<span>GoPay</span>';
        } else if ($this->acquirer == 'indomaret'){
            return '<span>Indomart</span>';
        } else if ($this->acquirer == 'alfamart'){
            return '<span>Alfamart</span>';
        } else {
            return $this->acquirer;
        }
    }

    public function getPaymentMethodNameAttribute()
    {
        if ($this->payment_type == 'bank_transfer') {
            return '<span>Bank Transfer</span>';
        } else if ($this->payment_type == 'gopay'){
            return '<span>GoPay</span>';
        } else if ($this->payment_type == 'cstore'){
            return '<span>Gerai Offline</span>';
        } else if ($this->payment_type == 'qris'){
            return '<span>QRIS</span>';
        } else if ($this->payment_type == 'echannel'){
            return '<span>E-Channel</span>';
        } else {
            return $this->payment_type;
        }
    }
}
