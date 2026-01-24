<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'payment_status',
        'payment_method',
        'total',
    ];

    protected $casts = [
        'total' => 'float',
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

    /* ================== Helpers ================== */

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }
}
