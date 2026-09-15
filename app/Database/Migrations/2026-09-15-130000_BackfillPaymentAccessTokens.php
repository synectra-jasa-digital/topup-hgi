<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BackfillPaymentAccessTokens extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('public_access_token', 'orders')) {
            return;
        }

        $orders = $this->db->table('orders')
            ->groupStart()
                ->where('public_access_token IS NULL', null, false)
                ->orWhere('public_access_token', '')
            ->groupEnd()
            ->get()->getResultArray();

        foreach ($orders as $order) {
            $this->db->table('orders')->where('id', $order['id'])->update([
                'public_access_token' => bin2hex(random_bytes(32)),
            ]);
        }
    }

    public function down()
    {
    }
}
