<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBannersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                 => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'banner_category_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'image_path'         => ['type' => 'VARCHAR', 'constraint' => 255],
            'link_url'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'start_date'         => ['type' => 'DATE', 'null' => true],
            'end_date'           => ['type' => 'DATE', 'null' => true],
            'sort_order'         => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'is_active'          => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'         => ['type' => 'DATETIME', 'null' => true],
            'updated_at'         => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('banner_category_id', 'banner_categories', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('banners');
    }

    public function down()
    {
        $this->forge->dropTable('banners');
    }
}
