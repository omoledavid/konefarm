<?php

namespace App\Http\Controllers;

use App\Http\Resources\SellerReviewResource;
use App\Models\SellerReview;
use App\Models\User;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;

class SellerReviewController extends Controller
{
    use ApiResponses;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $sellerId)
    {
        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string',
        ]);

        $seller = User::find($sellerId);
        if (!$seller) {
            return $this->error('Seller not found', 404);
        } elseif ($seller->user_id == auth()->id()) {
            return $this->error('You are not allowed to rate yourself', 403);
        }

        $review = SellerReview::updateOrCreate(
            ['user_id' => auth()->id(), 'seller_id' => $sellerId],
            ['rating' => $request->rating, 'comment' => $request->comment]
        );

        return $this->ok('review added', new SellerReviewResource($review));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $review = SellerReview::where('id', $id)->where('user_id', auth()->id())->first();
        if($review){
            $review->delete();
            return $this->ok('review deleted');
        }
        return $this->error('Review not found', 404);
    }
}
