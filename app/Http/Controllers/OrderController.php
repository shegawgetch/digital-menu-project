<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Resources\OrderResource;
use App\Notifications\NewOrderNotification;
use App\Models\User;


class OrderController extends Controller
{
    // List all orders for logged-in service provider
    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
                       ->latest()
                       ->get();

        return OrderResource::collection($orders);
    }

public function getOrdersForLoggedInUser(Request $request)
{
    $userId = $request->user()->id; // Get currently logged-in user ID

    $orders = Order::where('user_id', $userId)
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($order) {
            $itemsArray = json_decode($order->items, true) ?? [];

            if (empty($itemsArray)) {
                $order->items = [];
                return $order;
            }

            $menuItems = \App\Models\MenuItem::whereIn('id', array_column($itemsArray, 'id'))
                                             ->get()
                                             ->keyBy('id');

            $order->items = array_map(function($item) use ($menuItems) {
                $menuItem = $menuItems[$item['id']] ?? null;
                return [
                    'item_name' => $menuItem ? $menuItem->item_name : 'Unknown',
                    'quantity' => $item['quantity'] ?? 0
                ];
            }, $itemsArray);

            return $order;
        });

    return response()->json(['data' => $orders]);
}




    // Update order status
   public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|string|in:Pending,Ready,Served,Completed,Cancelled',
    ]);

    $order = Order::findOrFail($id);
    $order->status = $request->status;

    $order->save();

    return response()->json([
        'message' => 'Order status updated',
        'order' => $order,
    ]);
}


    // Store new order from customer (already exists)
public function store(Request $request)
{
    $validated = $request->validate([
        'customer_name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'items' => 'required|array',
        'total_price' => 'required|numeric',
        'user_id' => 'required|exists:users,id',
    ]);

    // Create order
    $order = Order::create([
        'user_id' => $validated['user_id'],
        'customer_name' => $validated['customer_name'],
        'phone' => $validated['phone'],
        'items' => json_encode($validated['items']),
        'total_price' => $validated['total_price'],
        'status' => 'pending',
    ]);

    // ✅ Send notification to service provider
    $serviceProvider = User::find($validated['user_id']);
    if ($serviceProvider && $serviceProvider->email) {
        $serviceProvider->notify(new NewOrderNotification($order));
    }

    return response()->json([
        'message' => 'Order placed successfully',
        'order' => $order
    ], 201);
}

}
