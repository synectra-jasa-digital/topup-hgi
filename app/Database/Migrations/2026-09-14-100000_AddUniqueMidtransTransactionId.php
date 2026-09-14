<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUniqueMidtransTransactionId extends Migration
{
    public function up()
    {
        $table = $this->db->DBPrefix . 'order_payments';
        $this->db->query("CREATE UNIQUE INDEX uq_order_payments_midtrans_transaction_id ON {$table} (midtrans_transaction_id)");
    }

    public function down()
    {
        $table = $this->db->DBPrefix . 'order_payments';
        if ($this->db->DBDriver === 'MySQLi') {
            $this->db->query("ALTER TABLE {$table} DROP INDEX uq_order_payments_midtrans_transaction_id");
            return;
        }
        $this->db->query('DROP INDEX uq_order_payments_midtrans_transaction_id');
    }
}
