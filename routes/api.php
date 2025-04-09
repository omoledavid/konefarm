<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\BuyerController;
use App\Http\Controllers\BuyOrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\SellerOrderController;
use App\Http\Controllers\SellerReviewController;
use App\Http\Controllers\SellersProductController;
use App\Http\Controllers\UserAddressController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\WishListController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('test', function (Request $request) {
    return 45;
});

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
    Route::post('/resend-verification-code', [AuthController::class, 'resendVerificationCode']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/verify-code', [AuthController::class, 'verifyCode']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::get('/user', [AuthController::class, 'me']);
    });
});
Route::middleware(['auth:sanctum'])->group(function () {
    //Sellers
    Route::prefix('sellers')->group(function () {
        Route::apiResource('product', SellersProductController::class);
        Route::apiResource('bank', BankAccountController::class);
        Route::get('orders', [SellerOrderController::class, 'index']);
        Route::post('orders/{order}', [SellerOrderController::class, 'changeStatus']);
    });

    //User
    Route::prefix('users')->group(function () {
        Route::controller(UserProfileController::class)->group(function () {
            Route::get('profile', 'getProfile')->name('profile');
            Route::post('profile', 'updateProfile')->name('profile.update');
            Route::get('toggle', 'toggleUser')->name('user.toggle');
        });
        Route::post('review-seller/{sellerId}',[SellerReviewController::class,'store']);
        Route::delete('review-seller/{id}',[SellerReviewController::class,'destroy']);
        Route::post('review-product/{productId}', [ProductReviewController::class,'store']);
        Route::delete('review-product/{id}', [ProductReviewController::class,'destroy']);
    });

    //Buyers
    Route::prefix('buyers')->group(function () {
        Route::get('clear-cart', [CartController::class, 'clearCart']);
        Route::apiResource('cart', CartController::class);
        Route::apiResource('address', UserAddressController::class);
        Route::apiResource('wishlist', WishlistController::class);
        Route::controller(OrderController::class)->group(function () {
            Route::post('checkout', 'checkout')->name('checkout');
            Route::post('checkout-repay/{id}', 'repay')->name('checkout.repay');
        });
        Route::get('orders', [BuyerController::class, 'getOrders'])->name('seller.orders');
    });
});
//General
Route::controller(GeneralController::class)->group(function () {
    Route::prefix('generals')->group(function () {
        Route::get('product-categories', 'productCategories');
        Route::get('products', 'allProducts');
        Route::get('users', 'users');
        Route::get('banks', 'banks');
        Route::post('verify-account-number', 'verifyAccountNumber');
    });
});
Route::get('order/verify/{reference}', [OrderController::class, 'verify']);
