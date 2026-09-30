<?php

namespace App\Http\Controllers;

use App\Models\ProfessionalProfile;

class ProfessionalVerificationController extends Controller
{
    /**
     * Public verification page for a professional's badge: "Ce professionnel
     * est-il réellement enregistré auprès d'IT Holding ?" (cahier des charges §11).
     */
    public function verify(string $proId)
    {
        $profile = ProfessionalProfile::where('pro_id', $proId)
            ->with('user')
            ->first();

        return view('pages.shop.professional_verify', compact('profile', 'proId'));
    }

    public function downloadQrCode(string $proId)
    {
        $profile = ProfessionalProfile::where('pro_id', $proId)->firstOrFail();

        $verifyUrl = route('professional.verify', $profile->pro_id);
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
                ->header('Content-Disposition', 'attachment; filename="qrcode-' . $profile->pro_id . '.png"');
        } catch (\Exception $e) {
            return redirect($qrApiUrl);
        }
    }
}
