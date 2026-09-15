<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateOrdersStatusEnum extends Migration
{
    public function up()
    {
        // Widen first so both the old and new status values are valid while we backfill.
        $this->forge->modifyColumn('orders', [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['menunggu_pembayaran', 'dibayar', 'menunggu_verifikasi', 'diproses', 'selesai', 'gagal', 'dibatalkan'],
                'default'    => 'menunggu_pembayaran',
            ],
        ]);

        $this->db->table('orders')->where('status', 'dibayar')->update(['status' => 'menunggu_verifikasi']);

        // Now narrow to the final list.
        $this->forge->modifyColumn('orders', [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['menunggu_pembayaran', 'menunggu_verifikasi', 'diproses', 'selesai', 'gagal', 'dibatalkan'],
                'default'    => 'menunggu_pembayaran',
            ],
        ]);
    }

    public function down()
    {
        // Widen first so both status values are valid while we backfill.
        $this->forge->modifyColumn('orders', [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['menunggu_pembayaran', 'dibayar', 'menunggu_verifikasi', 'diproses', 'selesai', 'gagal', 'dibatalkan'],
                'default'    => 'menunggu_pembayaran',
            ],
        ]);

        $this->db->table('orders')->where('status', 'menunggu_verifikasi')->update(['status' => 'dibayar']);

        // Now narrow back to the original list.
        $this->forge->modifyColumn('orders', [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['menunggu_pembayaran', 'dibayar', 'diproses', 'selesai', 'gagal', 'dibatalkan'],
                'default'    => 'menunggu_pembayaran',
            ],
        ]);
    }
}
