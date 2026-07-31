<?php

namespace App\Http\Controllers;

use App\Models\Warranty;
use Illuminate\Http\Request;

class WarrantyVerificationController extends Controller
{
    public function verify($number)
    {
        $warranty = Warranty::where('number', $number)
            ->with(['product', 'client', 'order'])
            ->first();

        // If not found by warranty number, try to search by serial number
        if (!$warranty) {
            $warranty = Warranty::where('serial_number', $number)
                ->with(['product', 'client', 'order'])
                ->first();
        }

        return view('pages.shop.warranty_verify', compact('warranty', 'number'));
    }

    public function downloadQrCode($number)
    {
        $warranty = Warranty::where('number', $number)->firstOrFail();
        
        $verifyUrl = route('warranty.verify', $warranty->number);
        $qrApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=500x500&data=" . urlencode($verifyUrl);

        try {
            $context = stream_context_create([
                'http' => [
                    'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n",
                    'timeout' => 5
                ]
            ]);
            $content = file_get_contents($qrApiUrl, false, $context);
            if ($content === false) {
                throw new \Exception("Failed to fetch image");
            }
            return response($content)
                ->header('Content-Type', 'image/png')
                ->header('Content-Disposition', 'attachment; filename="qrcode-' . $warranty->number . '.png"');
        } catch (\Exception $e) {
            // Fallback: redirect directly to the API URL for viewing/download
            return redirect($qrApiUrl);
        }
    }
}
