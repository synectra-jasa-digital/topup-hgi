<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBongkarPayoutMethodsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'code'       => ['type' => 'VARCHAR', 'constraint' => 50],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'category'   => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'bank'],
            'sort_order' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'is_active'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('code');
        $this->forge->createTable('bongkar_payout_methods');

        $now = date('Y-m-d H:i:s');
        $seed = [
            ['code' => 'BCA', 'name' => 'BCA', 'category' => 'bank', 'sort_order' => 1],
            ['code' => 'BRI', 'name' => 'BRI', 'category' => 'bank', 'sort_order' => 2],
            ['code' => 'Mandiri', 'name' => 'Mandiri', 'category' => 'bank', 'sort_order' => 3],
            ['code' => 'BNI', 'name' => 'BNI', 'category' => 'bank', 'sort_order' => 4],
            ['code' => 'DANA', 'name' => 'DANA', 'category' => 'ewallet', 'sort_order' => 5],
            ['code' => 'GoPay', 'name' => 'GoPay', 'category' => 'ewallet', 'sort_order' => 6],
            ['code' => 'OVO', 'name' => 'OVO', 'category' => 'ewallet', 'sort_order' => 7],
            ['code' => 'ShopeePay', 'name' => 'ShopeePay', 'category' => 'ewallet', 'sort_order' => 8],
            ['code' => 'Seabank', 'name' => 'Seabank', 'category' => 'bank', 'sort_order' => 9],
        ];
        foreach ($seed as &$row) {
            $row['is_active']  = 1;
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
        }
        unset($row);

        $this->db->table('bongkar_payout_methods')->insertBatch($seed);
    }

    public function down()
    {
        $this->forge->dropTable('bongkar_payout_methods');
    }
}
