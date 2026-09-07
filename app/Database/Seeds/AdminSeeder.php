<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('admins')->insert([
            'name'       => 'Owner Ayong Store',
            'email'      => 'owner@ayongstore.test',
            'password'   => password_hash('GantiPassword123!', PASSWORD_DEFAULT),
            'role'       => 'owner',
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
