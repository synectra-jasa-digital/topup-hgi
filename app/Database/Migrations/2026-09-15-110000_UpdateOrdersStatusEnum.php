<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateOrdersStatusEnum extends Migration
{
    public function up()
    {
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
        $this->forge->modifyColumn('orders', [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['menunggu_pembayaran', 'dibayar', 'diproses', 'selesai', 'gagal', 'dibatalkan'],
                'default'    => 'menunggu_pembayaran',
            ],
        ]);
    }
}
