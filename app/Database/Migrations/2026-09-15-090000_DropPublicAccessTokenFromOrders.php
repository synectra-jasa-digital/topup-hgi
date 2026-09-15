<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropPublicAccessTokenFromOrders extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('orders', 'public_access_token');
    }

    public function down()
    {
        $this->forge->addColumn('orders', [
            'public_access_token' => ['type' => 'CHAR', 'constraint' => 64, 'null' => true, 'after' => 'idempotency_token'],
        ]);
        $this->forge->addUniqueKey('orders', 'public_access_token');
    }
}
