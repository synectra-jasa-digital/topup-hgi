<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class BackupController extends BaseController
{
    private const BACKUP_PATH = WRITEPATH . 'backups/';
    private const FILENAME_PATTERN = '/^backup_[\w\-]+\.sql$/';
    private const MAX_BACKUPS = 10;
    private const MAX_DOWNLOAD_BYTES = 104857600;

    public function __construct()
    {
        helper('activity');
    }

    public function index()
    {
        $backups = [];

        if (is_dir(self::BACKUP_PATH)) {
            foreach (glob(self::BACKUP_PATH . 'backup_*.sql') as $path) {
                $backups[] = [
                    'filename' => basename($path),
                    'size'     => filesize($path),
                    'created'  => filemtime($path),
                ];
            }
            usort($backups, static fn (array $a, array $b) => $b['created'] <=> $a['created']);
        }

        return view('admin/backup/index', ['backups' => $backups]);
    }

    public function create()
    {
        if (! is_dir(self::BACKUP_PATH)) {
            mkdir(self::BACKUP_PATH, 0755, true);
        }

        $filename = 'backup_' . date('Y-m-d_His') . '.sql';

        try {
            $this->dumpDatabase(self::BACKUP_PATH . $filename);
            $this->pruneBackups();
        } catch (\Throwable $e) {
            log_message('error', 'Database backup failed: ' . $e->getMessage());

            return redirect()->to('/admin/backup-database')->with('error', 'Backup gagal dibuat: ' . $e->getMessage());
        }

        log_activity('backup_database', 'Membuat backup database: ' . $filename);

        return redirect()->to('/admin/backup-database')->with('success', 'Backup database berhasil dibuat: ' . $filename);
    }

    public function download(string $filename)
    {
        $filename = basename($filename);
        $path     = self::BACKUP_PATH . $filename;

        if (! preg_match(self::FILENAME_PATTERN, $filename) || ! is_file($path)) {
            return redirect()->to('/admin/backup-database')->with('error', 'File backup tidak ditemukan.');
        }

        if (filesize($path) > self::MAX_DOWNLOAD_BYTES) {
            return redirect()->to('/admin/backup-database')->with('error', 'Ukuran backup melebihi batas unduhan.');
        }

        log_activity('unduh_backup', 'Mengunduh backup database: ' . $filename);

        return $this->response->download($path, null);
    }

    public function delete(string $filename)
    {
        $filename = basename($filename);
        $path     = self::BACKUP_PATH . $filename;

        if (preg_match(self::FILENAME_PATTERN, $filename) && is_file($path)) {
            unlink($path);
            log_activity('hapus_backup', 'Menghapus file backup database: ' . $filename);
        }

        return redirect()->to('/admin/backup-database')->with('success', 'File backup berhasil dihapus.');
    }

    /**
     * Dump seluruh struktur & data database ke file .sql murni lewat koneksi
     * DB milik CodeIgniter sendiri - tidak bergantung pada binary mysqldump
     * eksternal yang path-nya spesifik per instalasi/versi/OS.
     */
    private function dumpDatabase(string $path): void
    {
        $db     = db_connect();
        $tables = $db->listTables();

        $handle = fopen($path, 'w');
        fwrite($handle, "-- Ayong Store Database Backup\n-- Generated: " . date('Y-m-d H:i:s') . "\n\nSET FOREIGN_KEY_CHECKS=0;\n\n");

        foreach ($tables as $table) {
            $createRow = $db->query("SHOW CREATE TABLE `{$table}`")->getRowArray();
            $createSql = $createRow['Create Table'] ?? '';

            fwrite($handle, "-- --------------------------------------------------------\n-- Table: {$table}\n-- --------------------------------------------------------\n");
            fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n{$createSql};\n\n");

            $query = $db->table($table)->get();
            $columns = $db->getFieldNames($table);
            if ($columns !== []) {
                $columnList = '`' . implode('`, `', $columns) . '`';

                while ($row = $query->getUnbufferedRow('array')) {
                    $escaped = array_map(static fn ($value) => $value === null ? 'NULL' : $db->escape($value), $row);
                    fwrite($handle, "INSERT INTO `{$table}` ({$columnList}) VALUES\n(" . implode(', ', $escaped) . ");\n");
                }
                fwrite($handle, "\n");
            }
        }

        fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($handle);
    }

    private function pruneBackups(): void
    {
        $paths = glob(self::BACKUP_PATH . 'backup_*.sql') ?: [];
        usort($paths, static fn ($a, $b) => filemtime($b) <=> filemtime($a));
        foreach (array_slice($paths, self::MAX_BACKUPS) as $path) {
            @unlink($path);
        }
    }
}
