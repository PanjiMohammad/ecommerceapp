<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];
    protected $appends = ['status_label', 'total'];
    
    public function district()
    {
        return $this->belongsTo(District::class);
    }

    //Relasi ke details
    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function return()
    {
        return $this->hasMany(OrderReturn::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    
    //Accessor
    public function getStatusLabelAttribute()
    {
        if ($this->status == 0) {
            return '<span class="badge badge-secondary">Baru</span>';
        } elseif ($this->status == 1) {
            return '<span class="badge badge-primary">Dikonfirmasi</span>';
        } elseif ($this->status == 2) {
            return '<span class="badge badge-info">Proses</span>';
        } elseif ($this->status == 3) {
            return '<span class="badge badge-warning">Dikirim</span>';
        }
        return '<span class="badge badge-success">Selesai</span>';
    }

    public function getTotalAttribute()
    {
        return $this->subtotal + $this->cost + $this->packaging_cost + $this->service_cost;
    }
}
