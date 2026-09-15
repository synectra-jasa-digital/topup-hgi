<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropPublicAccessTokenFromOrders extends Migration
{
    public function up()
    {
        // Retained for private customer access to invoices and proof uploads.
    }

    public function down()
    {
        // No-op: the access token remains part of the orders schema.
    }
}
