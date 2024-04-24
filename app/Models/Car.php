<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $fillable = [
        'brand','title','description', 'colors', 'pictures', 'year', 'price', 'customs_price', 'available_quantity',
    ];

    public function shippingFees()
    {
        return $this->hasMany(ShippingFee::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}

