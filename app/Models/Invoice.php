<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Invoice extends Model
{
    
   use HasFactory;

    protected $fillable = [
        'order_id',
        'invoice_number',
        'subtotal',
        'discount',
        'shipping_cost',
        'total',
    ];

    protected $casts = [
        'subtotal' => 'float',
        'discount' => 'float',
        'shipping_cost' => 'float',
        'total' => 'float',
    ];

    /* ================== Relationships ================== */

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}