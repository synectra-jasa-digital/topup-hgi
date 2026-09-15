<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddReportIndexes extends Migration
{
    public function up()
    {
        $this->forge->addKey('status', false, false, 'idx_orders_status');
        $this->forge->addKey('created_at', false, false, 'idx_orders_created_at');
        $this->forge->addKey(['status', 'created_at'], false, false, 'idx_orders_status_created');
        $this->forge->processIndexes('orders');
    }

    public function down()
    {
        foreach (['idx_orders_status', 'idx_orders_created_at', 'idx_orders_status_created'] as $index) {
            $this->db->query('DROP INDEX IF EXISTS `' . $index . '`');
        }
    }
}
