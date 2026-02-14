<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Order extends Model
{

    use HasFactory;

    protected $fillable = [
        'user_id',

        // Pricing
        'subtotal',
        'discount',
        'shipping_cost',
        'total',
        'coupon_id',

        // Status
        'status',
        'payment_status',
        'payment_method',

        // Customer Info
        'customer_name',
        'customer_phone',
        'customer_email',

        // Shipping Info
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_zip',
        'shipping_country',

        // Tracking
        'tracking_number',
        'shipping_provider',

        // Timestamps
        'confirmed_at',
        'processed_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
        'returned_at',
    ];

    protected $casts = [
        'subtotal' => 'float',
        'discount' => 'float',
        'shipping_cost' => 'float',
        'total' => 'float',

        'confirmed_at' => 'datetime',
        'processed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    /* ================== Relationships ================== */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function shipment()
    {
        return $this->hasOne(Shipment::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    /* ================== Helpers ================== */

    public function getTotalQuantityAttribute()
    {
        return $this->items->sum('quantity');
    }
}