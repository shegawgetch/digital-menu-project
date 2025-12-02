<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use Illuminate\Http\Request;

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

                $order->items = array_map(function ($item) use ($menuItems) {
                    $menuItem = $menuItems[$item['id']] ?? null;

                    return [
                        'item_name' => $menuItem ? $menuItem->item_name : 'Unknown',
                        'quantity' => $item['quantity'] ?? 0,
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
        'status' => 'required|string|in:pending,ready,served,completed,cancelled',
    ]);

    $order = Order::findOrFail($id);
    $order->status = $request->status;

    try {
        $order->save();
    } catch (\Exception $e) {
        // Return the actual error message and stack trace for debugging
        return response()->json([
            'message' => 'Failed to update status',
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ], 500);
    }

    return response()->json([
        'message' => 'Order status updated successfully',
        'order' => $order,
    ]);
}




    // Store new order from customer (already exists)
public function store(Request $request)
{
    // 1️⃣ Validate the incoming request
    $validated = $request->validate([
        'customer_name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'items' => 'required|array|min:1',
        'items.*.id' => 'required|integer|exists:menu_items,id',
        'items.*.quantity' => 'required|integer|min:1',
        'total_price' => 'required|numeric|min:0',
        'user_id' => 'required|exists:users,id',
    ]);

    // 2️⃣ Create the order
    $order = Order::create([
        'user_id' => $validated['user_id'],
        'customer_name' => $validated['customer_name'],
        'phone' => $validated['phone'],
        'items' => json_encode($validated['items']),
        'total_price' => $validated['total_price'],
        'status' => 'pending',
    ]);

    // 3️⃣ Attempt to send notification (fail-safe)
    try {
        $serviceProvider = User::find($validated['user_id']);
        if ($serviceProvider && $serviceProvider->email) {
            $serviceProvider->notify(new NewOrderNotification($order));
        }
    } catch (\Exception $e) {
        // Log the error but do not fail the order creation
        \Log::error('Notification failed for Order ID '.$order->id.': '.$e->getMessage());
    }

    // 4️⃣ Return JSON response for frontend
    return response()->json([
        'message' => 'Order placed successfully',
        'order' => $order,
    ], 201);
}


}
