<?php

namespace App\Models;

use CodeIgniter\Model;

class StoreSettingModel extends Model
{
    protected $table         = 'store_settings';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['key', 'value', 'updated_at'];
    protected $useTimestamps = false;

    // Key-value store helpers
    public function getVal(string $key, string $default = ''): string
    {
        $row = $this->where('key', $key)->first();
        return $row ? $row['value'] : $default;
    }

    public function setVal(string $key, string $value): void
    {
        $existing = $this->where('key', $key)->first();
        if ($existing) {
            $this->update($existing['id'], [
                'key'        => $key,
                'value'      => $value,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        } else {
            $this->insert([
                'key'        => $key,
                'value'      => $value,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function getAll(): array
    {
        $all   = $this->findAll();
        $items = [];
        foreach ($all as $row) {
            $items[$row['key']] = $row['value'];
        }
        return $items;
    }

    public function batchSave(array $keyValues): void
    {
        foreach ($keyValues as $key => $value) {
            $this->setVal($key, (string) $value);
        }
    }
}
