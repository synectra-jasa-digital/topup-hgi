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
        $this->forge->dropColumn('product_categories', 'icon');
    }
}
