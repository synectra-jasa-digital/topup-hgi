<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPaymentFieldsToOrders extends Migration
{
    public function up()
    {
        $fields = [
            'public_access_token'       => ['type' => 'CHAR', 'constraint' => 64, 'null' => true],
            'payment_channel_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'payment_channel_type'      => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'payment_channel_name'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'payment_account_number'    => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'payment_account_holder'    => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'payment_qr_image_path'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'payment_proof_path'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'payment_proof_uploaded_at' => ['type' => 'DATETIME', 'null' => true],
            'payment_verified_by'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'payment_verified_at'       => ['type' => 'DATETIME', 'null' => true],
            'payment_rejection_reason'  => ['type' => 'TEXT', 'null' => true],
        ];

        foreach ($fields as $name => $definition) {
            if (! $this->db->fieldExists($name, 'orders')) {
                $this->forge->addColumn('orders', [$name => $definition]);
            }
        }
    }

    public function down()
    {
        foreach ([
            'public_access_token', 'payment_channel_id', 'payment_channel_type', 'payment_channel_name',
            'payment_account_number', 'payment_account_holder', 'payment_qr_image_path', 'payment_proof_path',
            'payment_proof_uploaded_at', 'payment_verified_by', 'payment_verified_at', 'payment_rejection_reason',
        ] as $field) {
            if ($this->db->fieldExists($field, 'orders')) {
                $this->forge->dropColumn('orders', $field);
            }
        }
    }
}
