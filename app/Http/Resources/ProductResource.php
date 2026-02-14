<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'slug'             => $this->slug,
            'sku'              => $this->sku,
            'description'      => $this->description,
            'base_price'       => $this->base_price,
            'is_weight_based'  => $this->is_weight_based,
            'is_active'        => $this->is_active,

           'category'    => new CategoryResource($this->whenLoaded('category')),

            'main_image' => $this->mainImage 
                ? asset('storage/' . $this->mainImage->path)
                : null,

            'images'   => ProductImageResource::collection($this->images),
            'variants' => ProductVariantResource::collection($this->variants),
            'created_at'  => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}