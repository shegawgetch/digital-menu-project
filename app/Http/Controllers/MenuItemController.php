<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;
use App\Http\Resources\MenuItemResource;

class MenuItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

public function index(Request $request)
{
    $query = MenuItem::with('category'); // eager load category

    // ✅ Filter by category if provided
    if ($request->has('category_id') && $request->category_id != 'all') {
        $query->where('category_id', $request->category_id);
    }

    $menuItems = $query->get();

    return response()->json($menuItems);
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
        $menuItem->delete();
        return response()->json(['message' => 'Menu item deleted successfully.'], 200);
    }
}
