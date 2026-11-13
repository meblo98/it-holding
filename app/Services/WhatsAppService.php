<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send a WhatsApp message to the admin.
     *
     * @param string $message
     * @return bool
     */
    public static function sendToAdmin(string $message): bool
    {
        $enabled = env('WHATSAPP_ENABLED', false);
        $adminPhone = env('ADMIN_WHATSAPP_NUMBER');
        $apiUrl = env('WHATSAPP_API_URL', 'https://api.whatsapp-gateway.local/v1/send');
        $token = env('WHATSAPP_TOKEN', 'dummy-token');

        if (!$enabled) {
            Log::info("WhatsApp Notification (Simulation):\nTo: Admin ({$adminPhone})\nMessage: {$message}");
            return true;
        }

        if (empty($adminPhone)) {
            Log::warning("WhatsApp notification requested but ADMIN_WHATSAPP_NUMBER is not set.");
            return false;
        }

        try {
            // Post payload to WhatsApp gateway API
            $response = Http::timeout(5)->post($apiUrl, [
                'token' => $token,
                'to' => $adminPhone,
                'body' => $message,
            ]);

            if ($response->successful()) {
                Log::info("WhatsApp Notification sent successfully to {$adminPhone}.");
                return true;
            }

            Log::error("Failed to send WhatsApp message. Status: " . $response->status() . " Response: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("WhatsApp Notification Exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Envoie un message WhatsApp à un numéro quelconque (fournisseur, livreur…) via la même passerelle.
     * Retourne false si la passerelle est désactivée ou si l'envoi échoue : il faut alors transmettre le message à la main.
     */
    public static function send(?string $to, string $message): bool
    {
        $to = self::normalizePhone($to);
        if (!$to) {
            return false;
        }

        if (!env('WHATSAPP_ENABLED', false)) {
            Log::info("WhatsApp (simulation) vers {$to} : {$message}");
            return false;
        }

        try {
            $response = Http::timeout(5)->post(env('WHATSAPP_API_URL', 'https://api.whatsapp-gateway.local/v1/send'), [
                'token' => env('WHATSAPP_TOKEN', 'dummy-token'),
                'to'    => $to,
                'body'  => $message,
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('WhatsApp send exception: ' . $e->getMessage());
            return false;
        }
    }

    /** Numéros sénégalais : 77xxxxxxx devient 22177xxxxxxx. */
    public static function normalizePhone(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);
        if ($digits === '') {
            return null;
        }

        return strlen($digits) === 9 ? '221' . $digits : $digits;
    }

    /**
     * Send order notification to admin.
     *
     * @param \App\Models\Order $order
     * @return bool
     */
    public static function notifyAdminForOrder(\App\Models\Order $order): bool
    {
        $order->load('items.product');

        $message = "📦 *Nouvelle Commande sur It-Holding*\n\n";
        $message .= "*ID Commande:* #{$order->id}\n";
        $message .= "*Client:* {$order->customer_name}\n";
        $message .= "*Email:* {$order->customer_email}\n";
        $message .= "*Téléphone:* " . ($order->customer_phone ?: 'N/A') . "\n";
        $message .= "*Adresse:* {$order->customer_address}\n";
        $message .= "*Montant Total:* " . number_format($order->total_amount, 0, ',', ' ') . " FCFA\n";
        $message .= "*Moyen de Paiement:* " . strtoupper($order->payment_method) . "\n";
        $message .= "*Statut Paiement:* " . strtoupper($order->payment_status) . "\n\n";
        
        $message .= "*Produits commandés :*\n";
        foreach ($order->items as $item) {
            $productName = $item->product ? $item->product->name : 'Produit inconnu';
            $message .= "- {$productName} x{$item->quantity} (" . number_format($item->price, 0, ',', ' ') . " FCFA)\n";
        }

        return self::sendToAdmin($message);
    }
}
