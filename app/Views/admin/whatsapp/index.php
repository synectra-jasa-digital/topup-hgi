<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-neutral-900">WhatsApp Gateway</h1>
            <p class="text-sm text-neutral-500">Kelola koneksi, driver, dan antrean pesan WhatsApp toko.</p>
        </div>
    </div>

    <!-- Status & Connection Card -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 rounded-2xl border border-neutral-200/80 bg-white p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-neutral-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl">chat</span>
                    </div>
                    <div>
                        <h2 class="font-bold text-neutral-900">Status Gateway</h2>
                        <p class="text-xs text-neutral-500">Driver aktif: <span class="font-semibold uppercase text-primary"><?= esc($driver) ?></span></p>
                    </div>
                </div>
                <div>
                    <?php if (! empty($status['connected'])): ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                            <span class="w-2 h-2 rounded-full bg-emerald-600"></span> Terhubung
                        </span>
                    <?php elseif (($status['status'] ?? '') === 'waiting_qr'): ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                            <span class="w-2 h-2 rounded-full bg-amber-600 animate-pulse"></span> Menunggu Scan QR
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">
                            <span class="w-2 h-2 rounded-full bg-rose-600"></span> Terputus / Off
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div class="bg-neutral-50 p-3.5 rounded-xl border border-neutral-100">
                    <span class="text-xs text-neutral-500 block">Nomor WA Tertaut</span>
                    <span class="font-mono font-bold text-neutral-800"><?= esc($status['phone'] ?? '-') ?></span>
                </div>
                <div class="bg-neutral-50 p-3.5 rounded-xl border border-neutral-100">
                    <span class="text-xs text-neutral-500 block">Terakhir Terhubung</span>
                    <span class="font-medium text-neutral-800"><?= esc($status['last_seen'] ?? '-') ?></span>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <?php if (! empty($status['connected'])): ?>
                    <form action="<?= base_url('admin/whatsapp/logout') ?>" method="post" onsubmit="return confirm('Yakin ingin memutuskan koneksi WhatsApp?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100">
                            <span class="material-symbols-outlined text-[18px]">power_settings_new</span> Putuskan Koneksi
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <!-- Container QR Code -->
            <div id="qr-container" class="mt-4 p-4 border border-dashed border-neutral-200 rounded-xl bg-neutral-50 hidden text-center">
                <p class="text-xs font-semibold text-neutral-700 mb-2">Scan QR Code dengan WhatsApp Business:</p>
                <div id="qr-code-img" class="inline-block bg-white p-2 border rounded-lg shadow-xs"></div>
                <p class="text-[11px] text-neutral-400 mt-2">QR menyegar otomatis setiap 15 detik</p>
            </div>
        </div>

        <!-- Tes Kirim Pesan -->
        <div class="rounded-2xl border border-neutral-200/80 bg-white p-6 shadow-sm space-y-4">
            <h2 class="font-bold text-neutral-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">send</span> Tes Kirim Pesan
            </h2>
            <form action="<?= base_url('admin/whatsapp/test') ?>" method="post" class="space-y-3">
                <?= csrf_field() ?>
                <div>
                    <label class="block text-xs font-semibold text-neutral-700 mb-1">Nomor WhatsApp</label>
                    <input type="text" name="phone" placeholder="08xxxxxxxxxx" required class="w-full rounded-xl border border-neutral-200 p-2.5 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                </div>
                <button type="submit" class="btn btn-primary w-full justify-center">
                    Kirim Pesan Tes
                </button>
            </form>
        </div>
    </div>

    <!-- Pengaturan Driver & Event Swithes -->
    <div class="rounded-2xl border border-neutral-200/80 bg-white p-6 shadow-sm space-y-5">
        <h2 class="font-bold text-neutral-900 border-b border-neutral-100 pb-3">Pengaturan WhatsApp Gateway</h2>
        
        <form action="<?= base_url('admin/whatsapp/settings') ?>" method="post" class="space-y-6">
            <?= csrf_field() ?>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-neutral-700 mb-1">Driver Gateway</label>
                    <select name="wa_driver" class="w-full rounded-xl border border-neutral-200 p-2.5 text-sm focus:border-primary outline-none">
                        <option value="baileys" <?= $driver === 'baileys' ? 'selected' : '' ?>>Baileys (Self-hosted Node.js)</option>
                        <option value="wablas" <?= $driver === 'wablas' ? 'selected' : '' ?>>Wablas (Layanan Berbayar)</option>
                        <option value="off" <?= $driver === 'off' ? 'selected' : '' ?>>Off (Nonaktifkan WA)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral-700 mb-1">Gateway URL</label>
                    <input type="url" name="wa_gateway_url" value="<?= esc($gatewayUrl) ?>" placeholder="https://wa.domainanda.com" class="w-full rounded-xl border border-neutral-200 p-2.5 text-sm focus:border-primary outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral-700 mb-1">API Key (x-api-key)</label>
                    <input type="password" name="wa_gateway_key" value="<?= esc($gatewayKey) ?>" placeholder="Kosongkan jika tidak diubah" class="w-full rounded-xl border border-neutral-200 p-2.5 text-sm focus:border-primary outline-none">
                </div>
            </div>

            <div>
                <h3 class="text-xs font-bold text-neutral-800 uppercase tracking-wider mb-3">Notifikasi Otomatis per Kejadian</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    <label class="flex items-center gap-2 p-3 border rounded-xl bg-neutral-50 cursor-pointer">
                        <input type="checkbox" name="wa_enable_order_created" value="1" <?= $enableOrderCreated === '1' ? 'checked' : '' ?> class="rounded text-primary focus:ring-primary">
                        <span class="text-xs font-medium">Pesanan Dibuat</span>
                    </label>
                    <label class="flex items-center gap-2 p-3 border rounded-xl bg-neutral-50 cursor-pointer">
                        <input type="checkbox" name="wa_enable_payment_received" value="1" <?= $enablePaymentRecv === '1' ? 'checked' : '' ?> class="rounded text-primary focus:ring-primary">
                        <span class="text-xs font-medium">Bukti Bayar Diterima</span>
                    </label>
                    <label class="flex items-center gap-2 p-3 border rounded-xl bg-neutral-50 cursor-pointer">
                        <input type="checkbox" name="wa_enable_payment_verified" value="1" <?= $enablePaymentVerif === '1' ? 'checked' : '' ?> class="rounded text-primary focus:ring-primary">
                        <span class="text-xs font-medium">Pembayaran Diverifikasi</span>
                    </label>
                    <label class="flex items-center gap-2 p-3 border rounded-xl bg-neutral-50 cursor-pointer">
                        <input type="checkbox" name="wa_enable_payment_rejected" value="1" <?= $enablePaymentRej === '1' ? 'checked' : '' ?> class="rounded text-primary focus:ring-primary">
                        <span class="text-xs font-medium">Bukti Bayar Ditolak</span>
                    </label>
                    <label class="flex items-center gap-2 p-3 border rounded-xl bg-neutral-50 cursor-pointer">
                        <input type="checkbox" name="wa_enable_order_completed" value="1" <?= $enableOrderCompl === '1' ? 'checked' : '' ?> class="rounded text-primary focus:ring-primary">
                        <span class="text-xs font-medium">Pesanan Selesai</span>
                    </label>
                    <label class="flex items-center gap-2 p-3 border rounded-xl bg-neutral-50 cursor-pointer">
                        <input type="checkbox" name="wa_enable_bongkar_status" value="1" <?= $enableBongkarStat === '1' ? 'checked' : '' ?> class="rounded text-primary focus:ring-primary">
                        <span class="text-xs font-medium">Status Bongkar Berubah</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
        </form>
    </div>

    <!-- Riwayat Outbox (50 Pesan Terakhir) -->
    <div class="rounded-2xl border border-neutral-200/80 bg-white p-6 shadow-sm space-y-4">
        <h2 class="font-bold text-neutral-900">Riwayat Antrean WhatsApp (50 Terakhir)</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-neutral-700">
                <thead class="bg-neutral-50 text-neutral-500 font-semibold uppercase border-b">
                    <tr>
                        <th class="p-3">ID</th>
                        <th class="p-3">Penerima</th>
                        <th class="p-3">Tipe</th>
                        <th class="p-3">Pesan</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Percobaan</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 font-mono">
                    <?php if (empty($outboxMessages)): ?>
                        <tr><td colspan="7" class="p-4 text-center text-neutral-400 font-sans">Belum ada antrean pesan.</td></tr>
                    <?php else: ?>
                        <?php foreach ($outboxMessages as $msg): ?>
                            <tr>
                                <td class="p-3">#<?= $msg['id'] ?></td>
                                <td class="p-3 font-semibold"><?= esc($msg['recipient']) ?></td>
                                <td class="p-3"><span class="px-2 py-0.5 rounded bg-neutral-100 text-neutral-600 font-sans text-[11px]"><?= esc($msg['type']) ?></span></td>
                                <td class="p-3 max-w-xs truncate font-sans text-neutral-600" title="<?= esc($msg['message']) ?>"><?= esc(mb_strimwidth($msg['message'], 0, 45, '...')) ?></td>
                                <td class="p-3 font-sans">
                                    <?php if ($msg['status'] === 'sent'): ?>
                                        <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[11px] font-semibold">Terkirim</span>
                                    <?php elseif ($msg['status'] === 'pending'): ?>
                                        <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 text-[11px] font-semibold">Pending</span>
                                    <?php elseif ($msg['status'] === 'invalid_number'): ?>
                                        <span class="px-2 py-0.5 rounded bg-purple-100 text-purple-800 text-[11px] font-semibold">Invalid No</span>
                                    <?php else: ?>
                                        <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-800 text-[11px] font-semibold" title="<?= esc($msg['error_message']) ?>">Gagal</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3 text-center"><?= $msg['attempts'] ?>/5</td>
                                <td class="p-3 font-sans">
                                    <?php if ($msg['status'] !== 'sent'): ?>
                                        <form action="<?= base_url('admin/whatsapp/retry/' . $msg['id']) ?>" method="post">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="px-2.5 py-1 text-[11px] font-medium bg-neutral-100 hover:bg-neutral-200 text-neutral-700 rounded transition-all">Kirim Ulang</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-neutral-300">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const qrContainer = document.getElementById('qr-container');
    const qrImg = document.getElementById('qr-code-img');

    function checkQr() {
        fetch('<?= base_url("admin/whatsapp/qr") ?>')
            .then(res => res.json())
            .then(data => {
                if (data.success && data.qr) {
                    qrContainer.classList.remove('hidden');
                    qrImg.innerHTML = `<img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=${encodeURIComponent(data.qr)}" alt="QR Code">`;
                } else {
                    qrContainer.classList.add('hidden');
                }
            })
            .catch(() => {});
    }

    checkQr();
    setInterval(checkQr, 15000);
});
</script>
<?= $this->endSection() ?>
