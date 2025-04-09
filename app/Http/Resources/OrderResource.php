<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'Order',
            'id' => $this->id,
            'attributes' => [
                'reference' => $this->reference,
                'total_amount' => $this->total_amount,
                'delivery_fee' => $this->delivery_fee,
                'status' => $this->status,
                'created_at' => $this->created_at->toDateTimeString(),
            ],
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'buyer_address' => new UserAddressResource($this->whenLoaded('buyerAddress')),
        ];
    }
}
