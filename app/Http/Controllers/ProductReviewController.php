<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductReviewResource;
use App\Models\Product;
use App\Models\ProductReview;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
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
    public function store(Request $request, $productId)
    {
        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string',
        ]);

        $product = Product::find($productId);
        if(!$product)
        {
            return $this->error('Product not found', 404);
        }elseif($product->user_id == auth()->id())
        {
            return $this->error('You are not allowed to rate your own product', 403);
        }

        $review = ProductReview::updateOrCreate(
            ['user_id' => auth()->id(), 'product_id' => $productId],
            ['rating' => $request->rating, 'comment' => $request->comment]
        );

        return $this->ok('review added', new ProductReviewResource($review));
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $review = ProductReview::where('id', $id)->where('user_id', auth()->id())->first();
        if($review){
            $review->delete();
            return $this->ok('review deleted');
        }
        return $this->error('Review not found', 404);
    }
}
