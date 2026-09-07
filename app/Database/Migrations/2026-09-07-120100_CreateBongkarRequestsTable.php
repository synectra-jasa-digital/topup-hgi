<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBongkarRequestsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'request_number' => ['type' => 'VARCHAR', 'constraint' => 50],
            'bongkar_catalog_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'catalog_code_snapshot' => ['type' => 'VARCHAR', 'constraint' => 100],
            'catalog_name_snapshot' => ['type' => 'VARCHAR', 'constraint' => 150],
            'unit_label_snapshot' => ['type' => 'VARCHAR', 'constraint' => 50],
            'quantity' => ['type' => 'INT', 'constraint' => 11],
            'rate_snapshot' => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'estimated_amount' => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'customer_whatsapp' => ['type' => 'VARCHAR', 'constraint' => 20],
            'payout_method' => ['type' => 'VARCHAR', 'constraint' => 50],
            'customer_note' => ['type' => 'TEXT', 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('request_number');
        $this->forge->addForeignKey('bongkar_catalog_id', 'bongkar_catalogs', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('bongkar_requests');
    }

    public function down()
    {
        $this->forge->dropTable('bongkar_requests');
    }
}
