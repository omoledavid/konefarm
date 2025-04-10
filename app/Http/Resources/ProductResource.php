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
            'type' => 'Product',
            'id' => $this->id,
            'attributes' => [
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description,
                'price' => $this->price,
                'category' => $this->category()->pluck('name'),
                'stock_quantity' => $this->stock_quantity,
                'unit' => $this->unit,
                'measurement' => $this->measurement,
                'thumbnail' => $this->thumbnail ? url($this->thumbnail) : null,
                'status' => $this->status,
                'created_at' => $this->created_at->toDateTimeString(),
            ],
            'seller' => new UserResource($this->whenLoaded('seller')),
            'images' => ProductImageResource::collection($this->whenLoaded('images')),
            'reviews' => ProductReviewResource::collection($this->whenLoaded('reviews')),
            'related_products' => ProductResource::collection($this->whenLoaded('relatedProducts'))
        ];
    }
}
