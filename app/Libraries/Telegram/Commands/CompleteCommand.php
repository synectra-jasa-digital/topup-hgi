<?php

namespace App\Libraries\Telegram\Commands;

use App\Models\OrderModel;

class CompleteCommand extends BaseCommand
{
    public function handle(array $update, ?array $session): void
    {
        if ($this->requireLogin($update, $session)) {
            return;
        }

        $chatId = $this->getChatId($update);
        $text   = $this->getText($update);

        $parts = explode(' ', trim($text));
        if (count($parts) < 2 || trim($parts[1]) === '') {
            $this->bot->sendMessage($chatId, "Gunakan format: /selesai <code>&lt;invoice&gt;</code>");
            return;
        }

        $invoice    = trim($parts[1]);
        $orderModel = new OrderModel();
        $order      = $orderModel->findByInvoice($invoice);

        if ($order === null) {
            $this->bot->sendMessage($chatId, "❌ Pesanan <code>{$invoice}</code> tidak ditemukan.");
            return;
        }

        if ($order['status'] !== 'diproses') {
            $this->bot->sendMessage($chatId, "⚠️ Pesanan <code>{$invoice}</code> berstatus <b>{$order['status']}</b>, hanya pesanan berstatus <b>diproses</b> yang dapat diselesaikan.");
            return;
        }

        $adminId = (int) $session['admin_id'];
        $success = $orderModel->complete((int) $order['id'], $adminId);

        if ($success) {
            $this->logActivity($adminId, 'telegram_selesai_pesanan', "Menyelesaikan pesanan {$invoice}");

            // Pemicu WA ke customer via WhatsAppNotifier
            $updatedOrder = $orderModel->find($order['id']);
            if ($updatedOrder) {
                (new \App\Libraries\WhatsApp\WhatsAppNotifier())->orderCompleted($updatedOrder);
            }

            $this->bot->sendMessage($chatId, "✅ Pesanan <code>{$invoice}</code> berhasil ditandai <b>SELESAI</b>.");
        } else {
            $this->bot->sendMessage($chatId, "❌ Gagal menyelesaikan pesanan <code>{$invoice}</code>. Kemungkinan sudah diubah oleh admin lain.");
        }
    }
}
