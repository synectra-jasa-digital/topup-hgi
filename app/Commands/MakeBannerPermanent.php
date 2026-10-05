<?php

namespace App\Commands;

use App\Models\BannerModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Jadikan semua banner permanen.
 *
 * listActiveForDisplay() menyaring banner lewat start_date/end_date, jadi
 * banner yang tanggalnya sudah lewat hilang dari beranda tanpa error dan
 * tanpa jejak — Gejalanya hero section kosong. Perintah ini mengosongkan
 * jadwal dan menyalakan kembali banner, sehingga tidak ada lagi banner yang
 * hilang sendiri karena tanggal.
 *
 * Aman dijalankan berulang: baris yang sudah permanen tidak ikut disentuh.
 */
class MakeBannerPermanent extends BaseCommand
{
    protected $group       = 'Maintenance';
    protected $name        = 'banner:permanen';
    protected $description = 'Jadikan semua banner permanen (hapus jadwal tayang, aktifkan).';
    protected $usage       = 'banner:permanen [--dry-run]';
    protected $arguments   = [
        '--dry-run' => 'Tampilkan banner yang akan diubah tanpa menyimpan.',
    ];

    public function run(array $params)
    {
        $dryRun = in_array('--dry-run', $params, true);

        $banners = new BannerModel();
        $total   = (int) $banners->countAllResults();

        if ($total === 0) {
            CLI::write('Tidak ada banner untuk diubah.', 'yellow');
            return;
        }

        $needsFix = $this->bannersNeedingFix($banners);

        if ($needsFix === []) {
            CLI::write("Semua {$total} banner sudah permanen. Tidak ada perubahan.", 'green');
            return;
        }

        foreach ($needsFix as $banner) {
            $reasons = $this->why($banner);
            CLI::write(
                sprintf(
                    '#%d %s — %s',
                    $banner['id'],
                    $banner['image_path'],
                    implode(', ', $reasons)
                ),
                'yellow'
            );
        }

        if ($dryRun) {
            CLI::write(count($needsFix) . ' banner akan diubah (dry-run, tidak disimpan).', 'yellow');
            return;
        }

        $changed = 0;
        foreach ($needsFix as $banner) {
            $updated = $banners->update((int) $banner['id'], [
                'is_active'  => 1,
                'start_date' => null,
                'end_date'   => null,
            ]);

            if ($updated === false) {
                CLI::error(sprintf('Gagal menyimpan banner #%d.', $banner['id']));
                continue;
            }

            $changed++;
        }

        CLI::write("Selesai: {$changed} dari {$total} banner sekarang permanen.", 'green');
        CLI::write('Jadwal tayang tidak lagi dipakai. Banner hanya tampil jika is_active = 1.', 'white');
    }

    /**
     * Banner yang perlu diperbaiki, beserta alasannya.
     */
    private function bannersNeedingFix(BannerModel $banners): array
    {
        $rows = [];
        foreach ($banners->findAll() as $banner) {
            if ($this->why($banner) === []) {
                continue;
            }
            $rows[] = $banner;
        }
        return $rows;
    }

    /** Alasan kenapa banner ini belum permanen. */
    private function why(array $banner): array
    {
        $reasons = [];
        $today   = date('Y-m-d');

        if ((int) ($banner['is_active'] ?? 0) !== 1) {
            $reasons[] = 'tidak aktif';
        }
        if (! empty($banner['start_date'])) {
            $reasons[] = 'punya start_date (' . $banner['start_date'] . ')';
        }
        if (! empty($banner['end_date'])) {
            $reasons[] = 'punya end_date (' . $banner['end_date'] . ')';
        }

        // Banner yang tanggalnya sudah lewat sebenarnya hilang dari beranda;
        // sebutkan eksplisit supaya penyebabnya jelas saat diagnosis.
        if (! empty($banner['end_date']) && $banner['end_date'] < $today) {
            $reasons[] = 'SUDAH KEDALUWARSA';
        }

        return $reasons;
    }
}