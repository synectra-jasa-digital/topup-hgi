<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSnapTokenToOrders extends Migration
{
    public function up()
    {
        $this->forge->addColumn('orders', [
            'snap_token' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('orders', 'snap_token');
    }
}
