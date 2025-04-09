<?php

namespace App\Http\Controllers;

use App\Enums\ProductStatus;
use App\Enums\UserStatus;
use App\Http\Filters\ProductFilter;
use App\Http\Filters\UserFilter;
use App\Http\Resources\ProductCategoryResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\UserResource;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use App\Services\PaystackService;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GeneralController extends Controller
{
    use ApiResponses;

    protected $paystackService;

    public function __construct(PaystackService $paystackService)
    {
        $this->paystackService = $paystackService;
    }

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
    public function users(UserFilter $filter)
    {
        return $this->ok('users retrieved successfully', UserResource::collection(User::query()->where('status', UserStatus::ACTIVE)->filter($filter)->latest()->get()));
    }
    public function banks(){
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY'),
            'Cache-Control' => 'no-cache',
        ])->get('https://api.paystack.co/bank');

        if ($response->failed()) {
            return $this->error($response->json('message') ?? 'something went wrong');
        } else {
            return $this->success('All banks retrieved successfully',[
                'banks_count' => count($response->json('data')),
                'banks' => $response->json('data')
            ]);
//            echo $response->body();;
        }
    }
    public function verifyAccountNumber(Request $request){
        $request->validate([
            'account_number' => 'required|max:40',
            'bank_code' => 'required|string|max:200'
        ]);

        $accountNumber = $request->input('account_number');
        $bankCode = $request->input('bank_code');

        $result = $this->paystackService->validateBankAccount($accountNumber, $bankCode);
        if ($result['error']) {
            return $this->error('Failed to validate bank account, check account number');
        }
        return $this->ok('Account number verified successfully',$result['data']);
    }
}
