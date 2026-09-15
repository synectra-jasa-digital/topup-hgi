<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPayoutAccountFieldsToBongkarRequests extends Migration
{
    public function up()
    {
        $this->forge->addColumn('bongkar_requests', [
            'payout_account_number' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'after' => 'payout_method'],
            'payout_account_name'   => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true, 'after' => 'payout_account_number'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('bongkar_requests', ['payout_account_number', 'payout_account_name']);
    }
}
