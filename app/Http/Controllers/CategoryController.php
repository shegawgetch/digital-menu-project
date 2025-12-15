<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
       // List all categories
public function index(Request $request)
{
    $user = $request->user();
    if (!$user) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    $query = Category::where('user_id', $user->id);

    if ($request->boolean('trashed')) { // cleaner way
        $query = $query->onlyTrashed();
    }

    $categories = $query->latest()->get();

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
                $oldPath = str_replace(asset('storage/').'/', '', $category->image);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image')->store('categories', 'public');
            $data['image'] = asset('storage/'.$path);
        }

        $category->update($data);

        return new CategoryResource($category);
    }

public function destroy(Category $category)
{
    // Soft delete only
    $category->delete();

    return response()->json(['message' => 'Category moved to trash.'], 200);
}

// Optional: Force delete (permanent)
public function forceDestroy($id)
{
    $category = Category::withTrashed()->findOrFail($id);

    if ($category->image) {
        Storage::disk('public')->delete(str_replace(asset('storage/').'/', '', $category->image));
    }

    $category->forceDelete();

    return response()->json(['message' => 'Category permanently deleted.'], 200);
}

// Optional: Restore from trash
public function restore($id)
{
    $category = Category::onlyTrashed()->findOrFail($id);
    $category->restore();

    return response()->json(['message' => 'Category restored.'], 200);
}
}
