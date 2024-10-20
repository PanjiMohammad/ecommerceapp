<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderCancelledDetail extends Model
{
    protected $guarded = [];
    protected $appends = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
}
