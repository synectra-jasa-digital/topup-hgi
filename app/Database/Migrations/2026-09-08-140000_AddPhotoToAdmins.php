<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPhotoToAdmins extends Migration
{
    public function up()
    {
        $this->forge->addColumn('admins', [
            'photo' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'password',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('admins', 'photo');
    }
}
