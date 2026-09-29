<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTelegramUpdatesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'update_id'   => ['type' => 'BIGINT', 'unsigned' => true],
            'received_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('update_id');
        $this->forge->createTable('telegram_updates');
    }

    public function down(): void
    {
        $this->forge->dropTable('telegram_updates');
    }
}
