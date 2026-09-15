<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class AuditUploads extends BaseCommand
{
    protected $group = 'Maintenance';
    protected $name = 'uploads:audit';
    protected $description = 'Audit upload yang tidak direferensikan dan membersihkan file kadaluarsa.';

    public function run(array $params)
    {
        helper('upload');
        $root = realpath(FCPATH . 'assets/uploads');
        if (! $root) {
            CLI::error('Direktori upload tidak ditemukan.');
            return;
        }

        $referenced = $this->referencedPaths();
        $retentionDays = 30;
        $orphaned = 0;
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS)) as $file) {
            if (! $file->isFile() || $file->getFilename() === '.htaccess') {
                continue;
            }
            $relative = str_replace('\\', '/', substr($file->getPathname(), strlen(FCPATH)));
            if (isset($referenced[$relative]) || $file->getMTime() > time() - ($retentionDays * 86400)) {
                continue;
            }
            $orphaned++;
            CLI::write($relative);
            if (in_array('--delete', $params, true)) {
                @unlink($file->getPathname());
            }
        }
        $action = in_array('--delete', $params, true) ? 'Dihapus' : 'Ditemukan';
        CLI::write("{$action}: {$orphaned} orphan upload.");    }

    private function referencedPaths(): array
    {
        $paths = [];
        foreach ([['banners', 'image_path'], ['product_categories', 'icon'], ['admins', 'photo'], ['store_settings', 'value']] as [$table, $column]) {
            try {
                foreach (db_connect()->table($table)->select($column)->get()->getResultArray() as $row) {
                    $value = $row[$column] ?? '';
                    if (is_upload_path_allowed($value)) {
                        $paths[$value] = true;
                    }
                }
            } catch (\Throwable $e) {
                log_message('warning', 'Upload audit skipped table ' . $table . ': ' . $e->getMessage());
            }
        }
        return $paths;
    }
}
