<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'product_id', 'quantity', 'price', 'total','delivery_fee', 'seller_id'];

    public function product() {
        return $this->belongsTo(Product::class);
    }
}
