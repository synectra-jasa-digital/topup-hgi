<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPaymentFieldsToOrders extends Migration
{
    public function up()
    {
        $this->forge->addColumn('orders', [
            'payment_channel_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'voucher_id'],
            'payment_proof_path'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'payment_channel_id'],
            'payment_proof_uploaded_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'payment_proof_path'],
            'payment_verified_by'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'payment_proof_uploaded_at'],
            'payment_verified_at'       => ['type' => 'DATETIME', 'null' => true, 'after' => 'payment_verified_by'],
        ]);
        $this->forge->dropColumn('orders', 'snap_token');
    }

    public function down()
    {
        $this->forge->addColumn('orders', [
            'snap_token' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        ]);
        $this->forge->dropColumn('orders', ['payment_channel_id', 'payment_proof_path', 'payment_proof_uploaded_at', 'payment_verified_by', 'payment_verified_at']);
    }
}
