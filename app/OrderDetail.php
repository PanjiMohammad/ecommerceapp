<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $guarded = [];
    protected $appends = ['status_label', 'total', 'display_info'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function promo()
    {
        return $this->belongsTo(Promo::class);
    }

    // accessor
    public function getStatusLabelAttribute()
    {
        if ($this->status == 0) {
            return '<span class="badge badge-light">Baru</span>';
        } elseif ($this->status == 1) {
            return '<span class="badge badge-primary">Menunggu</span>';
        } elseif ($this->status == 2) {
            return '<span class="badge badge-primary">Dikonfirmasi</span>';
        } elseif ($this->status == 3) {
            return '<span class="badge badge-info">Proses</span>';
        } elseif ($this->status == 4) {
            return '<span class="badge badge-warning">Dikirim</span>';
        } elseif ($this->status == 5) {
            return '<span class="badge badge-secondary">Sampai</span>';
        }
        return '<span class="badge badge-success">Selesai</span>';
    }

    public function getTotalAttribute()
    {
        return $this->subtotal + $this->shippng_cost;
    }

    public function getDisplayInfoAttribute()
    {
        $info = '';

        if ($this->product_id && $this->promo_id) {
            // If both product and promo IDs are present
            $info .= 'Product: ' . ($this->product ? $this->product->name : 'Unknown Product');
            $info .= ' | Promo: ' . ($this->promo ? $this->promo->name : 'Unknown Promo');
        } elseif ($this->product_id) {
            // If only product ID is present
            $info .= 'Product: ' . ($this->product ? $this->product->name : 'Unknown Product');
        } elseif ($this->promo_id) {
            // If only promo ID is present
            $info .= 'Promo: ' . ($this->promo ? $this->promo->name : 'Unknown Promo');
        }

        return $info;
    }
}
