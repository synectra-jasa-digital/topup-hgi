<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWaOutboxTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'                 => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'recipient'          => ['type' => 'VARCHAR', 'constraint' => 32],
            'type'               => ['type' => 'VARCHAR', 'constraint' => 32],
            'message'            => ['type' => 'TEXT'],
            'ref_type'           => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
            'ref_id'             => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'dedupe_key'         => ['type' => 'VARCHAR', 'constraint' => 64],
            'status'             => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'processing', 'sent', 'failed', 'invalid_number'],
                'default'    => 'pending',
            ],
            'attempts'           => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'next_retry_at'      => ['type' => 'DATETIME', 'null' => true],
            'gateway_message_id' => ['type' => 'VARCHAR', 'constraint' => 128, 'null' => true],
            'error_message'      => ['type' => 'TEXT', 'null' => true],
            'created_at'         => ['type' => 'DATETIME', 'null' => true],
            'sent_at'            => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('dedupe_key');
        $this->forge->addKey('status');
        $this->forge->addKey('next_retry_at');
        $this->forge->createTable('wa_outbox');
    }

    public function down(): void
    {
        $this->forge->dropTable('wa_outbox');
    }
}
