<?php

namespace App\Http\Controllers;

use App\Enums\ProductStatus;
use App\Http\Filters\ProductFilter;
use App\Http\Resources\ProductCategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GeneralController extends Controller
{
    use ApiResponses;

    public function productCategories(){
        $productCategories = ProductCategory::query()->where('is_active', true)->latest()->get();
        return $this->success('All categories', ProductCategoryResource::collection($productCategories));
    }
    public function allProducts(ProductFilter $filter)
    {
        $products = Product::query()->where('status', ProductStatus::ACTIVE)->latest()->filter($filter)->paginate(20);

        return $this->ok('products retrieved successfully', [
            'products' => ProductResource::collection($products),
            'pagination' => rssPaginate($products)
        ]);
    }
}
