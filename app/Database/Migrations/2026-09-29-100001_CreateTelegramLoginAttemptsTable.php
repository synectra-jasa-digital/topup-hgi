<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTelegramLoginAttemptsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'telegram_user_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'attempted_at'     => ['type' => 'DATETIME'],
            'success'          => ['type' => 'TINYINT', 'unsigned' => true, 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['telegram_user_id', 'attempted_at']);
        $this->forge->createTable('telegram_login_attempts');
    }

    public function down(): void
    {
        $this->forge->dropTable('telegram_login_attempts');
    }
}
