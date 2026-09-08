<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIconToProductCategories extends Migration
{
    public function up()
    {
        $this->forge->addColumn('product_categories', [
            'icon' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'slug',
            ],
        ]);
    }

    public function down()
    {
        // Plain ALTER TABLE ... DROP COLUMN is valid on both MySQL and SQLite
        // 3.35+. Forge::dropColumn() rebuilds the whole table on SQLite
        // (rename -> recreate -> copy -> drop temp), which fails here because
        // products.category_id holds a foreign key into this table across
        // that rebuild. A direct DROP COLUMN needs no rebuild, so it sidesteps
        // the issue entirely.
        $table = $this->db->DBPrefix . 'product_categories';
        $this->db->query("ALTER TABLE {$table} DROP COLUMN icon");
    }
}
