<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $email = trim((string) (getenv('SEED_OWNER_EMAIL') ?: ($_ENV['SEED_OWNER_EMAIL'] ?? '')));

        if ($email === '') {
            // Tidak ada email yang dikonfigurasi. Menjalankan seeder tanpa
            // kredensial yang eksplisit berarti membuat akun owner dengan
            // password yang diketahui publik, jadi lebih baik berhenti.
            throw new \RuntimeException(
                'Seed owner dibatalkan: set SEED_OWNER_EMAIL dan SEED_OWNER_PASSWORD di .env, '
                . 'contoh: SEED_OWNER_EMAIL=owner@domain-anda.test SEED_OWNER_PASSWORD=<minimal-16-karakter-acak>. '
                . 'Jangan pernah memakai kredensial bawaan di produksi.'
            );
        }

        $password = (string) (getenv('SEED_OWNER_PASSWORD') ?: ($_ENV['SEED_OWNER_PASSWORD'] ?? ''));

        if (mb_strlen($password) < 16) {
            throw new \RuntimeException(
                'Seed owner dibatalkan: SEED_OWNER_PASSWORD minimal 16 karakter. '
                . 'Buat dengan: php -r "echo bin2hex(random_bytes(16)) . PHP_EOL;"'
            );
        }

        $existing = $this->db->table('admins')->where('email', $email)->get()->getRowArray();

        if ($existing !== null) {
            // Idempoten: jalankan ulang seeder tidak boleh membuat owner kedua.
            return;
        }

        $now = date('Y-m-d H:i:s');

        $this->db->table('admins')->insert([
            'name'       => 'Owner Ayong Store',
            'email'      => $email,
            'password'   => password_hash($password, PASSWORD_DEFAULT),
            'role'       => 'owner',
            'is_active'  => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}