<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingFee extends Model
{
    protected $fillable = [
        'type', 'fee',
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}