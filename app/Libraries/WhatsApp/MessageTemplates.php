<?php

namespace App\Libraries\WhatsApp;

class MessageTemplates
{
    public static function maskGameId(?string $gameId): string
    {
        if (empty($gameId)) {
            return '-';
        }

        $length = strlen($gameId);
        if ($length <= 4) {
            return substr($gameId, 0, 1) . str_repeat('*', max(1, $length - 2)) . substr($gameId, -1);
        }

        return substr($gameId, 0, 2) . str_repeat('*', $length - 4) . substr($gameId, -2);
    }

    public static function orderCreated(array $order): string
    {
        $invoice = $order['invoice_number'] ?? '';
        $total   = number_format((float) ($order['total_amount'] ?? 0), 0, ',', '.');
        $checkUrl = base_url('cek-pesanan/' . $invoice);

        return "Halo!\n\n"
            . "Pesanan Anda di Ayong Store telah dibuat.\n"
            . "No. Invoice: {$invoice}\n"
            . "Total Bayar: Rp{$total}\n\n"
            . "Cek status pesanan Anda di:\n{$checkUrl}\n\n"
            . "Terima kasih!";
    }

    public static function paymentReceived(array $order): string
    {
        $invoice  = $order['invoice_number'] ?? '';
        $checkUrl = base_url('cek-pesanan/' . $invoice);

        return "Halo!\n\n"
            . "Bukti pembayaran untuk pesanan {$invoice} telah kami terima.\n"
            . "Status saat ini: Menunggu Verifikasi Admin.\n\n"
            . "Cek status pesanan Anda di:\n{$checkUrl}";
    }

    public static function paymentVerified(array $order): string
    {
        $invoice  = $order['invoice_number'] ?? '';
        $checkUrl = base_url('cek-pesanan/' . $invoice);

        return "Halo!\n\n"
            . "Pembayaran untuk pesanan {$invoice} telah TERVERIFIKASI.\n"
            . "Pesanan Anda sedang dalam proses pengiriman.\n\n"
            . "Cek status pesanan Anda di:\n{$checkUrl}";
    }

    public static function paymentRejected(array $order, string $reason = ''): string
    {
        $invoice  = $order['invoice_number'] ?? '';
        $checkUrl = base_url('cek-pesanan/' . $invoice);
        $reasonText = $reason !== '' ? "Alasan: {$reason}\n" : '';

        return "Halo!\n\n"
            . "Bukti pembayaran untuk pesanan {$invoice} DITOLAK.\n"
            . "{$reasonText}\n"
            . "Silakan unggah ulang bukti pembayaran yang valid melalui tautan berikut:\n{$checkUrl}";
    }

    public static function orderCompleted(array $order): string
    {
        $invoice  = $order['invoice_number'] ?? '';
        $product  = $order['product_name_snapshot'] ?? ($order['product_name'] ?? '');
        $nominal  = $order['nominal_snapshot'] ?? '';
        $gameId   = self::maskGameId($order['game_id'] ?? '');
        $checkUrl = base_url('cek-pesanan/' . $invoice);

        $prodInfo = $nominal !== '' ? "{$product} ({$nominal})" : $product;

        return "Halo!\n\n"
            . "Pesanan Anda di Ayong Store dengan No. Invoice {$invoice} telah SELESAI diproses.\n"
            . "Produk {$prodInfo} telah dikirim ke ID Game: {$gameId}.\n\n"
            . "Cek detail pesanan Anda di:\n{$checkUrl}\n\n"
            . "Terima kasih telah berbelanja di Ayong Store!";
    }

    public static function bongkarStatusChanged(array $request): string
    {
        $reqNum = $request['request_number'] ?? '';
        $status = $request['status'] ?? 'pending';
        $label  = function_exists('bongkar_status_label') ? bongkar_status_label($status) : $status;

        $catalog = $request['catalog_name_snapshot'] ?? '';
        $unit    = $request['unit_label_snapshot'] ?? '';
        $qty     = $request['quantity'] ?? 0;
        $amount  = number_format((float) ($request['estimated_amount'] ?? 0), 0, ',', '.');

        $itemInfo = $unit !== '' ? "{$catalog} ({$unit})" : $catalog;

        return "Halo!\n\n"
            . "Pengajuan bongkar No. {$reqNum} telah diperbarui ke status: {$label}.\n\n"
            . "Item: {$itemInfo}\n"
            . "Jumlah: {$qty}\n"
            . "Perkiraan Dana: Rp{$amount}\n\n"
            . "Terima kasih telah bertransaksi di Ayong Store!";
    }
}
