<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBongkarAuditColumns extends Migration
{
    public function up()
    {
        $this->forge->addColumn('bongkar_requests', [
            'actor_admin_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'status'],
            'previous_status' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true, 'after' => 'actor_admin_id'],
            'status_changed_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'previous_status'],
            'notification_status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending', 'after' => 'status_changed_at'],
            'notification_retry_count' => ['type' => 'INT', 'constraint' => 2, 'default' => 0, 'after' => 'notification_status'],
            'next_retry_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'notification_retry_count'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('bongkar_requests', ['actor_admin_id', 'previous_status', 'status_changed_at', 'notification_status', 'notification_retry_count', 'next_retry_at']);
    }
}