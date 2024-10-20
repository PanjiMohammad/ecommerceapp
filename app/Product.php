<?php

namespace App;
use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Product extends Model
{
    protected $guarded = [];
    protected $appends = ['status_type', 'status_promo'];

    //Accessor
    public function getStatusLabelAttribute()
    {
        if ($this->status == 0) {
            return '<span class="badge badge-secondary">Draft</span>';
        }
        return '<span class="badge badge-success">Publish</span>';
    }

    // Accessor for promo status
    public function getStatusPromoAttribute()
    {
        if($this->type == 'promo'){
            $currentDateTime = Carbon::now();
            if ($currentDateTime > Carbon::parse($this->end_date)) {
                return '<span class="badge badge-danger">Kadaluwarsa</span>';
            } else {
                return '<span class="badge badge-success">Aktif</span>';
            }
        }
    }

    //MUTATORS
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value); 
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function userRating()
    {
        return $this->hasOne(Rating::class);
    }

    public function getStatusTypeAttribute($value)
    {
        if($this->type == 'promo'){
            return '<span class="badge badge-danger">Promo</span>';
        }
    }
}
