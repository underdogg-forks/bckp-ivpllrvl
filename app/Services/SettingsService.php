<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class SettingsService
{
    /**
     * Save a setting value by key.
     */
    public function save(string $key, mixed $value): void
    {
        DB::table('ip_settings')->updateOrInsert(['key' => $key], ['value' => $value]);
    }

    /**
     * Get a setting value by key.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $row = DB::table('ip_settings')->where('key', $key)->first();

        return $row ? $row->value : $default;
    }
}
