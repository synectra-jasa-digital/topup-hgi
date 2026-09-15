<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddManualPaymentSnapshot extends Migration
{
    public function up()
    {
        $fields = [
            'public_access_token'    => ['type' => 'CHAR', 'constraint' => 64, 'null' => true],
            'payment_channel_type'   => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'payment_channel_name'   => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'payment_account_number' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'payment_account_holder' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'payment_qr_image_path'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'payment_rejection_reason' => ['type' => 'TEXT', 'null' => true],
        ];

        foreach ($fields as $name => $definition) {
            if (! $this->db->fieldExists($name, 'orders')) {
                $this->forge->addColumn('orders', [$name => $definition]);
            }
        }

        if ($this->db->fieldExists('public_access_token', 'orders')) {
            $orders = $this->db->table('orders')->where('public_access_token IS NULL', null, false)->get()->getResultArray();
            foreach ($orders as $order) {
                $this->db->table('orders')->where('id', $order['id'])->update([
                    'public_access_token' => bin2hex(random_bytes(32)),
                ]);
            }
        }

        if ($this->db->tableExists('payment_channels') && $this->db->fieldExists('payment_channel_id', 'orders')) {
            $orders = $this->db->table('orders o')
                ->select('o.id, pc.type, pc.name, pc.account_number, pc.account_holder, pc.qr_image_path')
                ->join('payment_channels pc', 'pc.id = o.payment_channel_id', 'inner')
                ->groupStart()
                    ->where('o.payment_channel_name IS NULL', null, false)
                    ->orWhere('o.payment_channel_type IS NULL', null, false)
                ->groupEnd()
                ->get()->getResultArray();

            foreach ($orders as $order) {
                $this->db->table('orders')->where('id', $order['id'])->update([
                    'payment_channel_type' => $order['type'],
                    'payment_channel_name' => $order['name'],
                    'payment_account_number' => $order['account_number'],
                    'payment_account_holder' => $order['account_holder'],
                    'payment_qr_image_path' => $order['qr_image_path'],
                ]);
            }
        }
    }

    public function down()
    {
        foreach (['public_access_token', 'payment_channel_type', 'payment_channel_name', 'payment_account_number', 'payment_account_holder', 'payment_qr_image_path', 'payment_rejection_reason'] as $field) {
            if ($this->db->fieldExists($field, 'orders')) {
                $this->forge->dropColumn('orders', $field);
            }
        }
    }
}
