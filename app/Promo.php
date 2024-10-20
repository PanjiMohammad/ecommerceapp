<?php

namespace App;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Promo extends Model
{
    protected $guarded = [];
    protected $appends = ['status_label', 'status'];

    //Accessor
    public function getStatusLabelAttribute()
    {
        if ($this->status == 0) {
            return '<span class="badge badge-secondary">Draft</span>';
        }
        return '<span class="badge badge-success">Publish</span>';
    }

    //MUTATORS
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value); 
    }

    // Accessor for promo status
    public function getStatusAttribute()
    {
        $currentDateTime = Carbon::now();
        if ($currentDateTime > Carbon::parse($this->end_date)) {
            return '<span class="badge badge-danger">Kadaluwarsa</span>';
        } else {
            return '<span class="badge badge-success">Aktif</span>';
        }
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
