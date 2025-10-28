<?php

namespace Modules\Core\Services;

use AllowDynamicProperties;
use Modules\Core\Models\Version;

#[AllowDynamicProperties]
class VersionsService extends BaseService
{
    public $table = 'ip_versions';

    public $primary_key = 'ip_versions.version_id';

    /**
     * Get a base Version query for select.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function defaultSelect(): \Illuminate\Database\Eloquent\Builder
    {
        return Version::query();
    }

    /**
     * Get a Version query ordered by date applied and file descending.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function defaultOrderBy(): \Illuminate\Database\Eloquent\Builder
    {
        return Version::query()->orderByDesc('version_date_applied')->orderByDesc('version_file');
    }

    /**
     * Get the current version using Eloquent.
     *
     * @return string|null
     */
    public function getCurrentVersion(): ?string
    {
        $current_version = Version::query()->orderByDesc('version_date_applied')->orderByDesc('version_file')->first();
        if ( ! $current_version) {
            return null;
        }

        return str_replace('.sql', '', mb_substr($current_version->version_file, mb_strpos($current_version->version_file, '_') + 1));
    }
}
