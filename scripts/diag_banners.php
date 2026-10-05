<?php
// Diagnosa: kenapa hero/banner tidak muncul?
// Hanya membaca, tidak mengubah apa pun.

$dsn = 'mysql:host=localhost;dbname=topup_hgi;charset=utf8mb4';

try {
    $pdo = new PDO($dsn, 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (Throwable $e) {
    echo "KONEKSI GAGAL: " . $e->getMessage() . PHP_EOL;
    exit(1);
}

echo "KONEKSI OK\n";
echo str_repeat('=', 78), "\n";

$total = (int) $pdo->query('SELECT COUNT(*) FROM banners')->fetchColumn();
echo "TOTAL BARIS DI TABEL banners: {$total}\n\n";

if ($total === 0) {
    echo ">>> PENYEBAB: tabel banners kosong. Hero memang tidak dirender karena\n";
    echo "    \$bannerTotal > 0 di hero_tabs.php:228 bernilai false.\n";
    echo "    Tambahkan banner lewat /admin/banner, atau cek apakah seed banner\n";
    echo "    pernah dijalankan di database ini.\n";
    exit(0);
}

echo "BARIS BANNER:\n";
$sql = 'SELECT id, banner_category_id, is_active, sort_order, start_date, end_date, image_path
        FROM banners ORDER BY sort_order, id';
foreach ($pdo->query($sql) as $r) {
    printf(
        "  id=%-4s cat=%-4s active=%-2s sort=%-4s start=%-12s end=%-12s %s\n",
        $r['id'],
        $r['banner_category_id'],
        $r['is_active'],
        $r['sort_order'],
        (string) ($r['start_date'] ?? 'NULL'),
        (string) ($r['end_date'] ?? 'NULL'),
        substr((string) $r['image_path'], -46)
    );
}

echo "\nFILTER YANG DIPAKAI BannerModel::listActiveForDisplay():\n";
echo "  is_active = 1\n";
echo "  (start_date IS NULL OR start_date <= CURDATE())\n";
echo "  (end_date   IS NULL OR end_date   >= CURDATE())\n";
echo '  CURDATE() = ' . (new DateTime())->format('Y-m-d') . "\n\n";

$rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
$visible = [];
foreach ($rows as $r) {
    $today = date('Y-m-d');
    $okActive  = (int) $r['is_active'] === 1;
    $okStart   = ($r['start_date'] === null || $r['start_date'] <= $today);
    $okEnd     = ($r['end_date']   === null || $r['end_date']   >= $today);
    $why = [];
    if (! $okActive) { $why[] = 'is_active bukan 1'; }
    if (! $okStart)  { $why[] = "start_date {$r['start_date']} belum mulai"; }
    if (! $okEnd)    { $why[] = "end_date {$r['end_date']} sudah lewat"; }
    if ($why === []) { $visible[] = $r; }
}

echo "TERLIHAT DI HALAMAN: " . count($visible) . "\n";
foreach ($visible as $r) {
    echo "  id={$r['id']} -> {$r['image_path']}\n";
}

$blocked = array_slice($rows, count($visible));
if ($blocked !== []) {
    echo "\nTERTAHAN FILTER:\n";
    $today = date('Y-m-d');
    foreach ($rows as $r) {
        if (in_array($r, $visible, true)) { continue; }
        $why = [];
        if ((int) $r['is_active'] !== 1) { $why[] = 'is_active=' . $r['is_active']; }
        if ($r['start_date'] !== null && $r['start_date'] > $today) {
            $why[] = "start_date={$r['start_date']} (hari ini {$today})";
        }
        if ($r['end_date'] !== null && $r['end_date'] < $today) {
            $why[] = "end_date={$r['end_date']} (hari ini {$today})";
        }
        echo "  id={$r['id']}: " . implode(', ', $why) . "\n";
    }
}

// Cek file banner benar-benar ada di disk?
echo "\nCEK FILE DI DISK:\n";
foreach ($visible as $r) {
    $p = $r['image_path'];
    if (str_starts_with($p, 'http')) {
        echo "  id={$r['id']}: URL eksternal ({$p})\n";
        continue;
    }
    $rel = str_starts_with($p, 'assets/') ? $p : ltrim($p, '/');
    $full = __DIR__ . '/../public/' . $rel;
    $full = realpath(__DIR__ . '/../public/' . $rel);
    echo "  id={$r['id']}: {$rel} -> " . ($full ? 'ADA (' . basename($full) . ')' : 'HILANG dari public/') . "\n";
}