<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStaticPagesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'slug'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'title'      => ['type' => 'VARCHAR', 'constraint' => 150],
            'content'    => ['type' => 'TEXT'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('static_pages');
    }

    public function down()
    {
        $this->forge->dropTable('static_pages');
    }
}
