<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Category;

class PublicMenuController extends Controller
{
    // No auth middleware because this is public
    public function show($userId)
    {
        $user = User::findOrFail($userId);

        $categories = Category::where('user_id', $user->id)
            ->with('menuItems') // Make sure Category model has menuItems relationship
            ->get();

        return response()->json([
            'service_provider' => [
                'name' => $user->business_name,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
            'categories' => $categories,
        ]);
    }
}
