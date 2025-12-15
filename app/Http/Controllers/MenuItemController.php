<?php

namespace App\Http\Controllers;

use App\Http\Resources\MenuItemResource;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
public function index(Request $request)
{
    $user = $request->user(); // get logged-in user

    $query = MenuItem::with('category')
        ->whereHas('category', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        });

    // Only trashed
    if ($request->has('trashed') && $request->trashed) {
        $query->onlyTrashed();
    }

    // Optional: filter by category
    if ($request->has('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    $menuItems = $query->get();

    // Map category name for frontend
    $menuItems->transform(function ($item) {
        $item->category_name = $item->category ? $item->category->name : 'Uncategorized';
        return $item;
    });

    return response()->json([
        'data' => $menuItems
    ]);
}

    public function store(Request $request)
    {
        $data = $request->validate([
            'item_name' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric',
            'tax_percentage' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('menu-items', 'public');
        }

        $menuItem = MenuItem::create($data);

        // Return full resource with final_price and category_name
        return new MenuItemResource($menuItem);
    }

    public function show(MenuItem $menuItem)
    {
        return new MenuItemResource($menuItem->load('category'));
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $request->validate([
            'item_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'tax_percentage' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'photo' => 'nullable|string',
        ]);

        $menuItem->update($request->all());

        return new MenuItemResource($menuItem);
    }
public function destroy(MenuItem $menuItem)
{
    // Soft delete only
    $menuItem->delete();

    return response()->json(['message' => 'Menu item moved to trash.'], 200);
}

public function restore($id)
{
    $item = MenuItem::onlyTrashed()->findOrFail($id);
    $item->restore();
    return response()->json(['message' => 'Menu item restored successfully']);
}

 public function forceDestroy($id)
    {
        $item = MenuItem::withTrashed()->find($id);

        if (!$item) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        $item->forceDelete(); // This permanently removes it
        return response()->json(['message' => 'Item permanently deleted']);
    }
}
