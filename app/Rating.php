<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{

    protected $fillable = [
        'customer_id',
        'order_id',
        'product_id',
        'rating',
        'comment'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
