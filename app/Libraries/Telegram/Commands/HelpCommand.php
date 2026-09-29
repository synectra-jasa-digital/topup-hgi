<?php

namespace App\Libraries\Telegram\Commands;

class HelpCommand extends BaseCommand
{
    public function handle(array $update, ?array $session): void
    {
        $chatId = $this->getChatId($update);
        $role   = $session['admin_role'] ?? null;

        $text = "📋 <b>Daftar Command</b>\n\n";
        $text .= "<b>Umum:</b>\n";
        $text .= "/start – Sapaan dan info toko\n";
        $text .= "/help – Daftar command ini\n";
        $text .= "/login – Login dengan email dan password\n";

        if ($role !== null) {
            $text .= "\n<b>Admin:</b>\n";
            $text .= "/logout – Akhiri sesi bot\n";
            $text .= "/laporan [pdf] – Unduh laporan penjualan\n";
            $text .= "/pending – Daftar pesanan menunggu verifikasi\n";
            $text .= "/pesanan <code>&lt;invoice&gt;</code> – Detail satu pesanan\n";
            $text .= "/selesai <code>&lt;invoice&gt;</code> – Tandai pesanan selesai\n";
            $text .= "/notif on|off – Atur notifikasi pesanan baru\n";
            $text .= "/tutup [alasan] – Tutup toko\n";
            $text .= "/buka – Buka toko\n";
            $text .= "/status – Status toko saat ini\n";
        }

        if ($role === 'owner') {
            $text .= "\n<b>Owner:</b>\n";
            $text .= "/sesi – Lihat dan cabut sesi bot semua admin\n";
        }

        $this->bot->sendMessage($chatId, $text);
    }
}
