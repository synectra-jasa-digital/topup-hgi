<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBongkarCatalogsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'code'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 150],
            'unit_label'  => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'kartu'],
            'base_rate'  => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'sort_order'  => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'is_active'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('code');
        $this->forge->createTable('bongkar_catalogs');
    }

    public function down()
    {
        $this->forge->dropTable('bongkar_catalogs');
    }
}
