<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTelegramSessionsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'telegram_user_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'chat_id'          => ['type' => 'BIGINT'],
            'admin_id'         => ['type' => 'INT', 'unsigned' => true],
            'notify'           => ['type' => 'TINYINT', 'unsigned' => true, 'default' => 1],
            'last_active_at'   => ['type' => 'DATETIME'],
            'expires_at'       => ['type' => 'DATETIME'],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('telegram_user_id');
        $this->forge->addKey('admin_id');
        $this->forge->createTable('telegram_sessions');
    }

    public function down(): void
    {
        $this->forge->dropTable('telegram_sessions');
    }
}
