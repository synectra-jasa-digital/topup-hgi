<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropOrderPayments extends Migration
{
    public function up()
    {
        $this->forge->dropTable('order_payments');
    }

    public function down()
    {
        $this->forge->addField([
            'id'                      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'order_id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'midtrans_order_id'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'midtrans_transaction_id' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'notification_key'       => ['type' => 'CHAR', 'constraint' => 64, 'null' => true],
            'payment_method'         => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'payment_channel'        => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'paid_at'                => ['type' => 'DATETIME', 'null' => true],
            'raw_notification'       => ['type' => 'TEXT', 'null' => true],
            'created_at'             => ['type' => 'DATETIME', 'null' => true],
            'updated_at'             => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('order_payments');
    }
}
