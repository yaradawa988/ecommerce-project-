<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Cart extends Model
{
   use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'total',
    ];

    protected $casts = [
        'total' => 'float',
    ];

    /* ================== Relationships ================== */

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /* ================== Helpers ================== */

    public function getTotalQuantityAttribute()
    {
        return $this->items->sum('quantity');
    }
}