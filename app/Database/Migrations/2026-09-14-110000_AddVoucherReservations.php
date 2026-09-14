<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVoucherReservations extends Migration
{
    public function up()
    {
        $this->forge->addColumn('vouchers', ['reserved_count' => ['type' => 'INT', 'constraint' => 11, 'default' => 0, 'after' => 'used_count']]);
        $this->forge->addColumn('orders', [
            'voucher_reserved' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0, 'after' => 'voucher_id'],
            'voucher_committed' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0, 'after' => 'voucher_reserved'],
            'voucher_reserved_until' => ['type' => 'DATETIME', 'null' => true, 'after' => 'voucher_committed'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('vouchers', 'reserved_count');
        $this->forge->dropColumn('orders', ['voucher_reserved', 'voucher_committed', 'voucher_reserved_until']);
    }
}