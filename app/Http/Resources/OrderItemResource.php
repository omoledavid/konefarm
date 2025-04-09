<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'OrderItem',
            'id' => $this->id,
            'attributes' => [
                'quantity' => $this->quantity,
                'price' => $this->price,
                'total' => $this->total,
                'created_at' => $this->created_at->toDateTimeString(),
            ],
            'seller' => new UserResource($this->whenLoaded('seller')),
            'product' => new ProductResource($this->whenLoaded('product'))
        ];
    }
}
