<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVouchersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'code'          => ['type' => 'VARCHAR', 'constraint' => 50],
            'type'          => ['type' => 'ENUM', 'constraint' => ['nominal', 'percentage']],
            'value'         => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'min_purchase'  => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'max_discount'  => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => true],
            'quota'         => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'used_count'    => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'start_date'    => ['type' => 'DATE', 'null' => true],
            'end_date'      => ['type' => 'DATE', 'null' => true],
            'is_active'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('code');
        $this->forge->createTable('vouchers');
    }

    public function down()
    {
        $this->forge->dropTable('vouchers');
    }
}
