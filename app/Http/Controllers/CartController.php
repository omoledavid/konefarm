<?php

namespace App\Http\Controllers;

use App\Enums\ProductStatus;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Product;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use ApiResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Cart::with('product')->where('user_id', auth()->id())->get();
        return $this->ok('cart data retrieved',  CartResource::collection($items));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::query()->where('status', ProductStatus::ACTIVE)->find($request->product_id);
        if(!$product)
        {
            return $this->error('product not found', 404);
        }
        if($user->is_seller)
        {
            return $this->error('switch to a buyer\'s account to purchase this product', 403);
        }
        // Check if requested quantity exceeds available stock
        if ($request->quantity > $product->stock_quantity) {
            return $this->error('Requested quantity exceeds available stock', 400);
        }
        $existing = Cart::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        $quantity = $request->quantity;
        $unitPrice = $product->price;
        $totalPrice = $quantity * $unitPrice;

        if ($existing) {
            $existing->update([
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice
            ]);
            $cartItem = $existing;
            $msg = 'cart item updated successfully';
        } else {
            $cartItem = Cart::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice
            ]);
            $msg = 'cart item created successfully';
        }
        return $this->ok($msg, new CartResource($cartItem), 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cart = Cart::where('user_id', auth()->id())
            ->where('id', $id)
            ->first();
        if($cart == null)
        {
            return $this->error('Cart not found', 404);
        }

        $cart->delete();
        return $this->ok('cart item deleted successfully');
    }
    public function clearCart()
    {
        Cart::where('user_id', auth()->id())->delete();
        return $this->ok('cart items cleared successfully');
    }
}
