<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderCancelled extends Model
{
    protected $guarded = [];
    protected $table = 'order_cancelled';
    protected $appends = ['total'];
    
    public function district()
    {
        return $this->belongsTo(District::class);
    }

    //Relasi ke details
    public function details()
    {
        return $this->hasMany(OrderCancelledDetail::class, 'order_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function getTotalAttribute()
    {
        return $this->subtotal + $this->cost + $this->packaging_cost + $this->service_cost;
    }
}
