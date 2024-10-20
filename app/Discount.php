<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'amount',
        'type',
        'valid_from',
        'valid_until',
    ];
}
