<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'weight' => 'float',
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