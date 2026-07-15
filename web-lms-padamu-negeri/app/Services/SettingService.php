<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SettingService
{
    private const CACHE_KEY    = 'app_settings_all';
    private const CACHE_TTL    = 3600; // 1 hour

    private const DEFAULTS = [
        'modul_materi_guru_aktif'  => true,
        'modul_materi_pd_aktif'    => true,
        'modul_tugas_guru_aktif'   => true,
        'modul_tugas_pd_aktif'     => true,
        'modul_absensi_guru_aktif' => true,
        'modul_absensi_pd_aktif'   => true,
        'modul_cbt_guru_aktif'     => true,
        'modul_cbt_pd_aktif'       => true,
        'maintenance_mode'         => false,
        'max_upload_materi_mb'     => 10,
        'max_upload_tugas_mb'      => 10,
    ];

    private function all(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $rows = DB::table('settings')->get(['key', 'value', 'type']);
            $result = [];
            foreach ($rows as $row) {
                $result[$row->key] = $this->cast($row->value, $row->type);
            }
            return $result;
        });
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $all = $this->all();
        if (array_key_exists($key, $all)) {
            return $all[$key];
        }
        if (array_key_exists($key, self::DEFAULTS)) {
            return self::DEFAULTS[$key];
        }
        return $default;
    }

    public function set(string $key, mixed $value): void
    {
        $type = $this->detectType($value);

        DB::table('settings')->updateOrInsert(
            ['key' => $key],
            ['value' => $this->serialize($value, $type), 'type' => $type, 'updated_at' => now()]
        );

        Cache::forget(self::CACHE_KEY);
    }

    public function invalidate(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    private function cast(mixed $value, string $type): mixed
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'json'    => json_decode($value, true),
            default   => (string) $value,
        };
    }

    private function serialize(mixed $value, string $type): string
    {
        return match ($type) {
            'boolean' => $value ? '1' : '0',
            'json'    => json_encode($value),
            default   => (string) $value,
        };
    }

    private function detectType(mixed $value): string
    {
        if (is_bool($value)) return 'boolean';
        if (is_int($value))  return 'integer';
        if (is_array($value)) return 'json';
        return 'string';
    }
}
