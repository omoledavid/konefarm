<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'user',
            'id' => $this->id,
            'attributes' => [
                'name' => $this->name,
                'email' => $this->email,
                'wallet_balance' => $this->wallet->balance,
                'is_buyer' => $this->is_buyer,
                'is_seller' => $this->is_seller,
                'phone' => $this->phone,
                'address' => $this->address,
                'state' => $this->state,
                'city' => $this->city,
                'country' => $this->country,
                'bio' => $this->bio,
                'profile_photo' => $this->profile_photo ? url(getFilePath('user_profile').'/'.$this->profile_photo) : null,
                'farm_name' => $this->farm_name,
                'delivery_fee' => $this?->delivery_fee,
                'avg_delivery_rating' => $this->avg_delivery_rating,
                'avg_quality_rating' => $this->avg_quality_rating,
                'total_reviews' => $this->total_reviews,
                'created_at' => $this->created_at->toDateTimeString(),
            ],
                'products' => ProductResource::collection($this->whenLoaded('products')),
                'reviews_got' => SellerReviewResource::collection($this->whenLoaded('sellerReviews')),
        ];
    }
}
