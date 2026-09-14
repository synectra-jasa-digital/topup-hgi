<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPublicAccessTokenToOrders extends Migration
{
    public function up()
    {
        $this->forge->addColumn('orders', [
            'public_access_token' => ['type' => 'CHAR', 'constraint' => 64, 'null' => true, 'after' => 'idempotency_token'],
        ]);
        $this->forge->addUniqueKey('orders', 'public_access_token');

        $orders = $this->db->table('orders')->where('public_access_token IS NULL', null, false)->get()->getResultArray();
        foreach ($orders as $order) {
            $this->db->table('orders')->where('id', $order['id'])->update(['public_access_token' => bin2hex(random_bytes(32))]);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('orders', 'public_access_token');
    }
}