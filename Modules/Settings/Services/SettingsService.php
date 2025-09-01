<?php

namespace Modules\Settings\Services;

use AllowDynamicProperties;
use Modules\Core\Services\BaseService;
use Modules\Settings\Models\Setting;
use Modules\Settings\Models\Version;

#[AllowDynamicProperties]
class SettingsService extends BaseService
{
    public $settings = [];

    /**
     * Save a setting using Eloquent.
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    public function save(string $key, string $value): void
    {
        Setting::query()->updateOrCreate(
            ['setting_key' => $key],
            ['setting_value' => $value]
        );
    }

    /**
     * Get a setting value using Eloquent.
     *
     * @param string $key
     * @return string|null
     */
    public function get(string $key): ?string
    {
        $setting = Setting::query()->where('setting_key', $key)->first();
        return $setting?->setting_value;
    }

    /**
     * Delete a setting using Eloquent.
     *
     * @param string $key
     * @return void
     */
    public function delete(string $key): void
    {
        Setting::query()->where('setting_key', $key)->delete();
    }

    /**
     * Load all settings from the database using Eloquent.
     *
     * @return void
     */
    public function loadSettings(): void
    {
        $ip_settings = Setting::query()->get();
        foreach ($ip_settings as $data) {
            $this->settings[$data->setting_key] = $data->setting_value;
        }
        $this->settings['current_version'] = Version::query()->latest('id')->first()?->version;
    }

    /**
     * @originalName setting
     *
     * @originalFile Setting.php
     */
    public function setting($key, $default = '')
    {
        return isset($this->settings[$key]) && $this->settings[$key] !== '' ? $this->settings[$key] : $default;
    }

    /**
     * Get gateway settings using Eloquent.
     *
     * @param string $key
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function gatewaySettings(string $key)
    {
        return Setting::query()->where('setting_key', 'like', 'gateway_' . mb_strtolower($key) . '%')->get();
    }

    /**
     * @originalName setSetting
     *
     * @originalFile Setting.php
     */
    public function setSetting($key, $value)
    {
        $this->settings[$key] = $value;
    }

    /**
     * @originalName getThemes
     *
     * @originalFile Setting.php
     */
    public function getThemes()
    {
        $this->load->helper('directory');
        $found_folders = directory_map(THEME_FOLDER, 1);
        $themes        = [];
        foreach ($found_folders as $theme) {
            if ($theme == 'core') {
                continue;
            }
            // GetController the theme info file
            $theme     = str_replace(DIRECTORY_SEPARATOR, '', $theme);
            $info_path = THEME_FOLDER . $theme . '/';
            $info_file = $theme . '.theme';
            if (file_exists($info_path . $info_file)) {
                $theme_info = Dotenv\Dotenv::createMutable($info_path, $info_file);
                $theme_info->load();
                $themes[$theme] = env('TITLE');
            }
        }

        return $themes;
    }
}
