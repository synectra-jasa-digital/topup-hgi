<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTelegramOutboxTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'chat_id'         => ['type' => 'BIGINT'],
            'order_id'        => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'type'            => ['type' => 'VARCHAR', 'constraint' => 32],
            'payload'         => ['type' => 'TEXT'],
            'attempts'        => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'sent_message_id' => ['type' => 'BIGINT', 'null' => true],
            'status'          => ['type' => 'ENUM', 'constraint' => ['pending', 'sent', 'failed'], 'default' => 'pending'],
            'error_message'   => ['type' => 'TEXT', 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('status');
        $this->forge->addKey('order_id');
        $this->forge->createTable('telegram_outbox');
    }

    public function down(): void
    {
        $this->forge->dropTable('telegram_outbox');
    }
}
