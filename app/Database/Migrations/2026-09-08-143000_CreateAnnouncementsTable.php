<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAnnouncementsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'message'    => ['type' => 'VARCHAR', 'constraint' => 255],
            'sort_order' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'is_active'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('announcements');

        $now = date('Y-m-d H:i:s');
        $this->db->table('announcements')->insertBatch([
            ['message' => '🔥 Kode Promo Hemat: Gunakan voucher AYONGHEMAT untuk potongan langsung Rp5.000!', 'sort_order' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['message' => '⚡ Kirim Kilat 1-3 Detik: Sistem integrasi server resmi otomatis tanpa login & tanpa sandi akun.', 'sort_order' => 2, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['message' => '🛡️ Legal & Anti Banned: Semua transaksi menggunakan jalur distribusi ID resmi terlisensi 100%.', 'sort_order' => 3, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['message' => '💬 Layanan Bantuan CS: Customer Service WhatsApp siap melayani 24 Jam Nonstop setiap hari.', 'sort_order' => 4, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('announcements');
    }
}
