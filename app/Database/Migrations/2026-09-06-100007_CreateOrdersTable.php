<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'invoice_number'        => ['type' => 'VARCHAR', 'constraint' => 30],
            'product_id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'product_name_snapshot' => ['type' => 'VARCHAR', 'constraint' => 150],
            'nominal_snapshot'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'price_snapshot'        => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'game_id'               => ['type' => 'VARCHAR', 'constraint' => 100],
            'whatsapp_number'       => ['type' => 'VARCHAR', 'constraint' => 20],
            'voucher_id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'discount_amount'       => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'total_amount'          => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'status'                => [
                'type'       => 'ENUM',
                'constraint' => ['menunggu_pembayaran', 'dibayar', 'diproses', 'selesai', 'gagal', 'dibatalkan'],
                'default'    => 'menunggu_pembayaran',
            ],
            'processed_by'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'completed_at'  => ['type' => 'DATETIME', 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('invoice_number');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('voucher_id', 'vouchers', 'id', 'SET NULL', 'RESTRICT');
        $this->forge->addForeignKey('processed_by', 'admins', 'id', 'SET NULL', 'RESTRICT');
        $this->forge->createTable('orders');
    }

    public function down()
    {
        $this->forge->dropTable('orders');
    }
}
