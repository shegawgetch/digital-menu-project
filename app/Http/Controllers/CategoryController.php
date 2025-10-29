<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function __construct()
    {
        // Apply Sanctum auth middleware to all routes in this controller
        $this->middleware('auth:sanctum');
    }

    // List all categories
    public function index()
    {
        $categories = Category::all();
        return CategoryResource::collection($categories);
    }

    // Store a new category
public function store(Request $request)
{
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'image' => 'nullable|image|max:2048',
    ]);

    // Assign logged-in user as creator
    $data['user_id'] = $request->user()->id;

    if ($request->hasFile('image')) {
        $data['image'] = $request->file('image')->store('categories', 'public');
    }

    $category = Category::create($data);

    return response()->json(['data' => $category], 201);
}


    // Show a single category
    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    // Update an existing category
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['name', 'description']);

        // Handle image update
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // Delete old image if exists
            if ($category->image) {
                $oldPath = str_replace(asset('storage/') . '/', '', $category->image);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image')->store('categories', 'public');
            $data['image'] = asset('storage/' . $path);
        }

        $category->update($data);

        return new CategoryResource($category);
    }

    // Delete a category
    public function destroy(Category $category)
    {
        // Delete image if exists
        if ($category->image) {
            $oldPath = str_replace(asset('storage/') . '/', '', $category->image);
            Storage::disk('public')->delete($oldPath);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully.'], 200);
    }
}
