<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;
use SimpleSoftwareIO\QrCode\Facades\QrCode; // ✅ correct import

class PublicMenuController extends Controller
{
    // Public access for customers
    public function generateQRCode($userId)
    {
        $menuUrl = url("/menu/{$userId}");

        // Generate QR code as SVG string
        $qrCodeSvg = QrCode::format('svg')->size(300)->generate($menuUrl);

        return response()->json([
            'qr_code_svg' => (string) $qrCodeSvg, // ensure string
            'menu_url' => $menuUrl
        ]);
    }

    public function show($userId)
    {
        $user = User::findOrFail($userId);

        $categories = Category::where('user_id', $user->id)
                              ->with('menuItems')
                              ->get();

        return response()->json([
            'service_provider' => [
                'name' => $user->business_name,
                'email' => $user->email,
                'phone' => $user->phone
            ],
            'categories' => $categories
        ]);
    }
}
