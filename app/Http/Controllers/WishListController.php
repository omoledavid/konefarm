<?php

namespace App\Http\Controllers;

use App\Http\Resources\WishlistResource;
use App\Models\WishList;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;

class WishListController extends Controller
{
    use ApiResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->ok(WishlistResource::collection(WishList::query()->where('user_id', auth()->id())->get()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);
        $exist = WishList::query()->where('product_id', $validatedData['product_id'])->where('user_id', auth()->id())->first();
        if($exist){
            return $this->error('Product is already in your wish list');
        }
        $validatedData['user_id'] = auth()->id();
        $wishlist = WishList::create($validatedData);
        return $this->ok('Product added to wish list', new WishListResource($wishlist));

    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $wishlist = WishList::query()->where('user_id', auth()->id())->find($id);
        if($wishlist){
            $wishlist->delete();
            return $this->ok('Product removed from wish list');
        }
        return $this->error('Product not found');
    }
}
