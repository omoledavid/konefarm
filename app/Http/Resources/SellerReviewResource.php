<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SellerReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'review',
            'id' => $this->id,
            'attributes' => [
                'rating' => $this->rating,
                'comment' => $this->comment,
                'created_at' => $this->created_at->toDateTimeString(),
            ],
            'seller' => new UserResource($this->seller),
        ];
    }
}
