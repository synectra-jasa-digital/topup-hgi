<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPaymentFieldsToOrders extends Migration
{
    public function up()
    {
        $this->forge->addColumn('orders', [
            'payment_channel_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'payment_proof_path'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'payment_proof_uploaded_at' => ['type' => 'DATETIME', 'null' => true],
            'payment_verified_by'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'payment_verified_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('orders', ['payment_channel_id', 'payment_proof_path', 'payment_proof_uploaded_at', 'payment_verified_by', 'payment_verified_at']);
    }
}
