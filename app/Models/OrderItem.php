<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class OrderItem extends Model
{
     use HasFactory;

    protected $fillable = [
        'order_id',
        'product_variant_id',
        'price',
        'quantity',
    ];

    protected $casts = [
        'price' => 'float',
    ];

    /* ================== Relationships ================== */

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}