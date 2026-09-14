<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdempotencyTokenToOrders extends Migration
{
    public function up()
    {
        $this->forge->addColumn("orders", [
            "idempotency_token" => ["type" => "VARCHAR", "constraint" => 255, "null" => true, "after" => "voucher_reserved_until"],
        ]);
        $this->forge->addUniqueKey("orders", "idempotency_token");
    }

    public function down()
    {
        $this->forge->dropColumn("orders", "idempotency_token");
    }
}
