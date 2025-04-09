<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Resources\OrderResource;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\UserAddress;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    use ApiResponses;

    public function checkout(Request $request)
    {
        $request->validate([
            'user_address_id' => 'required|exists:user_addresses,id', // Validate the address
            'callback_url' => 'nullable|url',
        ]);

        $user = auth()->user();
        $cartItems = Cart::with('product')->where('user_id', $user->id)->get();
//        dd($cartItems);

        if ($cartItems->isEmpty()) {
            return $this->error('Cart is empty');
        }

        // Get the selected address
        $userAddress = UserAddress::query()->where('id',$request->user_address_id)->first();
        if (!$userAddress) {
            return $this->error('Address not found', 404);
        }


        try {
            $result = DB::transaction(function () use ($request, $user, $userAddress, $cartItems) {
                $total = 0;
                $reference = 'ORD-' . strtoupper(Str::random(10));

                // Create Order with the buyer's address and total amount
                $order = Order::create([
                    'user_id' => $user->id,
                    'user_address_id' => $userAddress->id,
                    'reference' => $reference,
                    'total_amount' => 0,  // Will update after order items are added
                    'status' => OrderStatus::PENDING,
                    'callback_url' => $request->callback_url ?? null
                ]);

                // Process each item, split by seller
                $totalAmount = 0;
                foreach ($cartItems as $item) {
                    // Calculate delivery fee for this item (you can adjust based on seller location or other factors)
                    $deliveryFee = $item->product->seller->delivery_fee ?? 0; // Assuming sellers have a delivery_fee field

                    // Add product total price and delivery fee
                    $itemTotal = $item->total_price + $deliveryFee;

                    // Add order item to database
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'seller_id' => $item->product->user_id, // Seller ID
                        'quantity' => $item->quantity,
                        'price' => $item->unit_price,
                        'total' => $itemTotal,
                        'delivery_fee' => $deliveryFee // Save delivery fee per item
                    ]);

                    // Update the overall order total
                    $totalAmount += $itemTotal;
                }

                // Update the total order amount with calculated item totals and delivery fees
                $order->update([
                    'total_amount' => $totalAmount
                ]);

                // Initialize Paystack payment
                $paystackData = [
                    'amount' => $totalAmount * 100, // in kobo
                    'email' => $user->email,
                    'reference' => $reference,
                    'callback_url' => url('/api/order/verify/' . $reference),
                ];

                $paystack = Http::withToken(env('PAYSTACK_SECRET_KEY'))
                    ->post('https://api.paystack.co/transaction/initialize', $paystackData);

                if (!$paystack->successful()) {
                    DB::rollBack();
                    return $this->error('payment unsuccessful '.$paystack->json()['message'] ?? 'Something went wrong', 500);
                }

                // Clear the cart after successful order creation
                Cart::where('user_id', $user->id)->delete();

                // return final data
                return [
                    'reference' => $reference,
                    'order' => new OrderResource($order),
                    'payment_url' => $paystack['data']['authorization_url']
                ];
            });
            return $this->ok('Order placed successfully', $result);
        } catch (\Throwable $e) {
            Log::error('Checkout Error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return $this->error('Checkout unsuccessful: '.$e->getMessage(), 500);
        }

    }

    public function verify($reference)
    {
        $verify = Http::withToken(env('PAYSTACK_SECRET_KEY'))
            ->get("https://api.paystack.co/transaction/verify/{$reference}");

        if (!$verify->successful() || $verify['data']['status'] !== 'success') {
//            return $this->error($verify['message'] ?? 'Payment verification failed',400);
            return redirect()->away($callbackURL ?? env('FRONTEND_URL'));
        }

        $order = Order::where('reference', $reference)->first();
        if ($order == null) {
//            return $this->error('Order not found');
            return redirect()->away($callbackURL ?? env('FRONTEND_URL'));
        }

        if ($order->status === 'paid') {
//            return $this->error('Order is already paid');
            return redirect()->away($callbackURL ?? env('FRONTEND_URL'));
        }

        // Update order status
        $order->status = OrderStatus::PAID;
        $order->save();
        $callbackURL = $order->callback_url;
        $buyer = $order->user;
        notify($buyer, 'user_order_confirmation', [
            'user_name' => $buyer->name,
            'order_id' => $order->reference,
            'order_total' => $order->total_amount,
            'site_name' => 'Kone Farms'
        ]);

        // Reduce product stock
        foreach ($order->items as $item) {
            $product = $item->product;
            $product->stock_quantity -= $item->quantity;
            $product->save();
            //pay seller
            $item->seller->wallet->deposit($item->total,['description' => "Payment for order #$order->reference"]);
            $seller = $item->seller;
            notify($seller, 'seller_order_notification', [
                'seller_name' => $seller->name,
                'order_id' => $item->order->reference,
                'buyer_name' => $buyer->name,
            ]);
        }

        return redirect()->away($callbackURL ?? env('FRONTEND_URL'));
    }
    public function repay($id)
    {
        $order = Order::query()->where('id', $id)->where('user_id', auth()->id())->first();
        if (!$order) {
            return $this->error('You\'re not authorize to make this payment', 400);
        }
        if ($order->status->value !== OrderStatus::PENDING->value) {
            return $this->error('This order has been paid for', 400);
        }
        if ($order->status->value === OrderStatus::CANCELLED->value) {
            return $this->error('This order has been cancelled', 400);
        }
        $reference = 'ORD-' . strtoupper(Str::random(10));
        // Initialize Paystack payment
        $paystackData = [
            'amount' => $order->total_amount * 100, // in kobo
            'email' => $order->user->email,
            'reference' => $reference,
            'callback_url' => url('/api/order/verify/' . $reference),
        ];

        $paystack = Http::withToken(env('PAYSTACK_SECRET_KEY'))
            ->post('https://api.paystack.co/transaction/initialize', $paystackData);

        if (!$paystack->successful()) {
            return $this->error('payment unsuccessful '.$paystack->json()['message'] ?? 'Something went wrong', 500);
        }
        $order->update([
            'reference' => $reference,
        ]);
        return $this->ok('payment link generated successfully', [
            'reference' => $reference,
            'order' => new OrderResource($order),
            'payment_url' => $paystack['data']['authorization_url']
        ]);
    }


}
