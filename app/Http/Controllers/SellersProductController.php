<?php

namespace App\Http\Controllers;

use App\Enums\ProductStatus;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SellersProductController extends Controller
{
    use ApiResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $products = Product::query()->where('user_id', $user->id)->get();
        return $this->ok('data retrieved', ['products' => ProductResource::collection($products)]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'unit' => 'required|in:kg,litre,bag,crate,bunch,piece,dozen,other',
            'measurement' => 'nullable|string',
            'category_id' => 'required|exists:product_categories,id',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        $validatedData['slug'] = Str::slug($request->name . '-' . Str::random(5));
        $validatedData['user_id'] = auth()->id();
        $validatedData['thumbnail'] = null;
        if ($request->hasFile('thumbnail')) {
            $location = getFilePath('products');
            $path = fileUploader($request->thumbnail, $location);
            $validatedData['thumbnail'] = $path;
        }
        $autoApprove = gs('auto_approve');
        if($autoApprove){
            $validatedData['status'] = ProductStatus::ACTIVE;
        }else{
            $validatedData['status'] = ProductStatus::INACTIVE;
        }
        $product = Product::query()->create($validatedData);
        return $this->ok('product created', ['product' => new ProductResource($product)]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::where('id', $id)->where('user_id', auth()->id())->first();
        if ($product == null) {
            return $this->error('product not found',404);
        }
        return $this->ok('product retrieved', ['product' => new ProductResource($product)]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::where('id', $id)->where('user_id', auth()->id())->first();
        if ($product == null) {
            return $this->error('product not found',404);
        }
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'unit' => 'nullable|in:kg,litre,bag,crate,bunch,piece,dozen,other',
            'measurement' => 'nullable|string',
            'category_id' => 'nullable|exists:product_categories,id',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        if ($request->hasFile('thumbnail')) {
            $location = getFilePath('products');
            $path = fileUploader($request->thumbnail, $location);
            $validatedData['thumbnail'] = $path;
        }
        $product->update($validatedData);
        return $this->ok('product updated', ['product' => new ProductResource($product)]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::where('id', $id)->where('user_id', auth()->id())->first();
        if ($product == null) {
            return $this->error('product not found',404);
        }
        $product->delete();
        return $this->ok('product deleted');
    }
}
