<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropSnapTokenFromOrders extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('orders', 'snap_token');
    }

    public function down()
    {
        $this->forge->addColumn('orders', [
            'snap_token' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        ]);
    }
}
