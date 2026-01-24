<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
   use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'description',
        'base_price',
        'is_weight_based',
        'is_active',
    ];

    protected $casts = [
        'is_weight_based' => 'boolean',
        'is_active' => 'boolean',
    ];

    /* ================== Relationships ================== */

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function mainImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_main', true);
    }

    /* ================== Scopes ================== */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /* ================== Helpers ================== */

    public function getMinPriceAttribute()
    {
        return $this->variants()->min('price') ?? $this->base_price;
    }
}
