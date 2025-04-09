<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Resources\OrderItemResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ProductResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class SellerOrderController extends Controller
{
    use ApiResponses;
    public function index()
    {
        $user = auth()->user();

        // Get all order items where the product belongs to this seller
        $orderItems = OrderItem::with(['order', 'product'])
            ->where('seller_id', $user->id)
            ->latest()
            ->get();

        // Optionally group by order
        $orders = $orderItems->groupBy('order_id')->map(function ($items) {
            return [
                'order' => new OrderResource($items->first()->order),
                'items' => $items->map(function ($item) {
                    return [
                        new OrderItemResource($item),
                    ];
                }),
            ];
        })->values();
        return $this->ok('All orders retrieved', $orders);
    }
    public function changeStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => ['required', new Enum(OrderStatus::class)],
        ]);
        $status = $request->status;
        if ($status == OrderStatus::PAID->value || $status == OrderStatus::CANCELLED->value || $status == OrderStatus::PENDING->value) {
            return $this->error("You cannot set status to $status",422);
        }
        $order->update(['status' => $request->status]);
        return $this->ok('Order status updated',new OrderResource($order->load('items.product')));
    }
}
