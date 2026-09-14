<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPaymentNotificationKey extends Migration
{
    public function up()
    {
        $this->forge->addColumn('order_payments', [
            'notification_key' => ['type' => 'CHAR', 'constraint' => 64, 'null' => true, 'after' => 'midtrans_transaction_id'],
        ]);
        $this->forge->addUniqueKey('order_payments', 'notification_key');
    }

    public function down()
    {
        $this->forge->dropColumn('order_payments', 'notification_key');
    }
}
