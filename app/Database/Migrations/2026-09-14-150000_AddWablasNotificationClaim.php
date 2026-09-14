<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWablasNotificationClaim extends Migration
{
    public function up()
    {
        $this->forge->addColumn('orders', [
            'wablas_notification_claimed' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0, 'after' => 'public_access_token'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('orders', 'wablas_notification_claimed');
    }
}