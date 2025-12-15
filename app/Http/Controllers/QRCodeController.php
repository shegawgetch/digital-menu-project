<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeController extends Controller
{   
    public function generate(Request $request)
    {
        $user = $request->user();

        // Frontend URL where the menu is displayed
        $frontendUrl = config('app.frontend_url', 'http://127.0.0.1:5173');
        $menuUrl = $frontendUrl . "/menu/{$user->id}";

        // Check if QR code already exists
        if ($user->qr_code_path && Storage::disk('public')->exists($user->qr_code_path)) {
            $qrCodeSvg = Storage::disk('public')->get($user->qr_code_path);
            $qrCodeUrl = asset('storage/' . $user->qr_code_path);
        } else {
            // Generate new QR code SVG
            $qrCodeSvg = QrCode::format('svg')->size(300)->generate($menuUrl);

            // Save it in public storage
            $fileName = "qr_codes/user_{$user->id}.svg";
            Storage::disk('public')->put($fileName, $qrCodeSvg);

            // Save path in user table
            $user->qr_code_path = $fileName;
            $user->save();

            $qrCodeUrl = asset('storage/' . $fileName);
        }

        return response()->json([
            'menu_url' => $menuUrl,      // Vue frontend route
            'qr_code_svg' => $qrCodeSvg,  // SVG content
            'qr_code_url' => $qrCodeUrl,  // public URL for <img>
        ]);
    }
}
