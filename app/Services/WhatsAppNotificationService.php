<?php

namespace App\Services;

use App\Models\CustomerMutation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppNotificationService
{
    protected string $botUrl;

    public function __construct()
    {
        $this->botUrl = config('services.whatsapp_bot.url', 'http://localhost:3000/send-message');
    }

    /**
     * Sends transaction notification for a customer mutation ala BCA Mobile format.
     */
    public function sendMutationNotification(CustomerMutation $mutation): bool
    {
        $customer = $mutation->customer;
        if (!$customer || !$customer->phone) {
            return false;
        }

        $message = $this->buildMessageText($mutation);

        try {
            $response = Http::timeout(5)->post($this->botUrl, [
                'phone' => $customer->phone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info("WA Bot notification sent to {$customer->phone} for mutation #{$mutation->id}");
                return true;
            } else {
                Log::warning("WA Bot returned status {$response->status()} for mutation #{$mutation->id}: {$response->body()}");
                return false;
            }
        } catch (\Exception $e) {
            Log::info("WA Bot offline or unreachable at {$this->botUrl}. Message logged: {$message}");
            return false;
        }
    }

    /**
     * Build BCA Mobile style message text
     */
    public function buildMessageText(CustomerMutation $mutation): string
    {
        $customer = $mutation->customer;
        $formattedAmount = 'Rp ' . number_format($mutation->amount, 0, ',', '.');
        $formattedBalance = 'Rp ' . number_format($mutation->balance_after, 0, ',', '.');
        $timeStr = $mutation->created_at ? $mutation->created_at->format('d/m/Y H:i') . ' WIB' : now()->format('d/m/Y H:i') . ' WIB';
        $publicUrl = route('public.membership.show', ['token' => $customer->unique_token]);

        if (in_array($mutation->type, ['topup', 'change_deposit', 'refund'])) {
            $typeTitle = $mutation->type === 'change_deposit' 
                ? 'Kembalian Belanja ke Saldo' 
                : ($mutation->type === 'refund' ? 'Pengembalian Dana (Refund)' : 'Top-Up Saldo');

            return "☕ *[SIMALU MEMBERSHIP]*\n" .
                   "Saldo Bertambah! ✨\n\n" .
                   "-----------------------------------\n" .
                   "Member     : {$customer->name}\n" .
                   "Kategori   : {$typeTitle}\n" .
                   "Penambahan : *{$formattedAmount}*\n" .
                   "Total Saldo: *{$formattedBalance}*\n" .
                   "Waktu      : {$timeStr}\n" .
                   "-----------------------------------\n\n" .
                   "Terima kasih atas kepercayaan Anda!\n" .
                   "Cek kartu & mutasi: {$publicUrl}";
        } else {
            // Payment or negative adjustment
            $orderNo = $mutation->order ? $mutation->order->order_number : ($mutation->notes ?? 'Transaksi POS');

            return "☕ *[SIMALU MEMBERSHIP]*\n" .
                   "Transaksi Berhasil! 🛒\n\n" .
                   "-----------------------------------\n" .
                   "Member     : {$customer->name}\n" .
                   "Ref / Order: {$orderNo}\n" .
                   "Pengeluaran: *{$formattedAmount}*\n" .
                   "Sisa Saldo : *{$formattedBalance}*\n" .
                   "Waktu      : {$timeStr}\n" .
                   "-----------------------------------\n\n" .
                   "Terima kasih telah menikmati Kopi Simalu!\n" .
                   "Cek kartu & mutasi: {$publicUrl}";
        }
    }
}
