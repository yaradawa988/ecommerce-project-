<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
   use HasFactory;

    protected $fillable = [
        'product_id',
        'size',
        'color',
        'weight',
        'price',
        'stock',
    ];

    protected $casts = [
        'weight' => 'float',
        'price' => 'float',
    ];

    /* ================== Relationships ================== */

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stockLogs()
    {
        return $this->hasMany(StockLog::class);
    }

    /* ================== Scopes ================== */

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }
}