<?php
// Verifikasi: apakah banner sekarang lolos filter listActiveForDisplay()?
$dsn = 'mysql:host=localhost;dbname=topup_hgi;charset=utf8mb4';

try {
    $pdo = new PDO($dsn, 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (Throwable $e) {
    echo "KONEKSI GAGAL: " . $e->getMessage() . PHP_EOL;
    exit(1);
}

$today = date('Y-m-d');

// SQL yang sama persis dengan BannerModel::listActiveForDisplay()
$visible = $pdo->prepare(
    'SELECT id, image_path, sort_order FROM banners
      WHERE is_active = 1
        AND (start_date IS NULL OR start_date <= :today)
        AND (end_date   IS NULL OR end_date   >= :today)
      ORDER BY sort_order'
);
$visible->execute(['today' => $today]);
$rows = $visible->fetchAll(PDO::FETCH_ASSOC);

echo "CURDATE() = {$today}\n";
echo 'Banner yang akan tampil di beranda: ' . count($rows) . "\n\n";

foreach ($rows as $r) {
    printf("  #%s sort=%s  %s\n", $r['id'], $r['sort_order'], $r['image_path']);

    // Cek file gambarnya benar-benar ada di public/
    $file = __DIR__ . '/../public/' . $r['image_path'];
    echo '       file: ' . (is_file($file) ? 'ADA' : 'HILANG dari public/') . "\n";

    // Cek varian webp untuk srcset
    $webp = str_replace('.png', '.webp', $file);
    echo '       webp: ' . (is_file($webp) ? 'ADA' : 'tidak ada (srcset dilewati)') . "\n";
}

echo "\n";
if (count($rows) > 0) {
    echo "HASIL: hero carousel AKAN dirender (" . count($rows) . " slide)\n";
} else {
    echo "HASIL: hero carousel MASIH kosong. Periksa is_active.\n";
}