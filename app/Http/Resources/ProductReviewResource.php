<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'ProductReview',
            'id' => $this->id,
            'attributes' => [
                'rating' => $this->rating,
                'comment' => $this->comment,
                'created_at' => $this->created_at->toDateTimeString(),
            ],
            'product' => new ProductResource($this->product),
        ];
    }
}
