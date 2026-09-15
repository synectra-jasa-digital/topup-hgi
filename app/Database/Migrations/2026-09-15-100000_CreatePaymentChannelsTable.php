<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaymentChannelsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'type'           => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => 'bank'],
            'name'           => ['type' => 'VARCHAR', 'constraint' => 100],
            'account_number' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'account_holder' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'qr_image_path'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'sort_order'     => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'is_active'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('payment_channels');
    }

    public function down()
    {
        $this->forge->dropTable('payment_channels');
    }
}
