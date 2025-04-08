<?php

namespace App\Http\Controllers;

use App\Http\Filters\OrderFilter;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class BuyerController extends Controller
{
    public function getOrders(OrderFilter $filter)
    {
        $user_id = auth()->id();
        $orders = Order::where('user_id', $user_id)->with('items')->filter($filter)->get();
        return OrderResource::collection($orders);
    }
}
