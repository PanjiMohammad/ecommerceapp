<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderReturn extends Model
{
    protected $guarded = [];

    protected $appends = ['status_label'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getStatusLabelAttribute()
    {
        if ($this->status == 0) {
            return '<span class="badge badge-secondary">Menunggu Konfirmasi</span>';
        } elseif ($this->status == 2) {
            return '<span class="badge badge-danger">Ditolak</span>';
        }
        return '<span class="badge badge-success">Disetujui</span>';
    }
}
