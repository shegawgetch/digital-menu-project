<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    // Generate QR code for logged-in service provider
    public function generate(Request $request)
    {
        $user = $request->user();

        // Generate slug for restaurant (you can use business_name or user id)
        $slug = $user->id; // or Str::slug($user->business_name)

        $menuUrl = url("/api/menu/{$slug}");

        // Generate QR code as SVG
        $qrCode = QrCode::size(300)->generate($menuUrl);

        return response()->json([
            'qr_code_svg' => $qrCode,
            'menu_url' => $menuUrl
        ]);
    }
}
