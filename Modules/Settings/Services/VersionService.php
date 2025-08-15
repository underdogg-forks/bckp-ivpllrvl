<?php

namespace Modules\Settings\Services;

use App\Services\BaseService;

use AllowDynamicProperties;
use Modules\Core\Models\ResponseModel;

#[AllowDynamicProperties]
class VersionService extends BaseService
{
    public $table = 'ip_versions';

    public $primary_key = 'ip_versions.version_id';

    /**
     * @originalName defaultSelect
     *
     * @originalFile Version.php
     */
    public function defaultSelect()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', false);
    }

    /**
     * @originalName defaultOrderBy
     *
     * @originalFile Version.php
     */
    public function defaultOrderBy()
    {
        $this->db->orderBy('ip_versions.version_date_applied DESC, ip_versions.version_file DESC');
    }

    /**
     * @originalName getCurrentVersion
     *
     * @originalFile Version.php
     */
    public function getCurrentVersion()
    {
        $current_version = $this->mdl_versions->limit(1)->get()->row()->version_file;

        return str_replace('.sql', '', mb_substr($current_version, mb_strpos($current_version, '_') + 1));
    }
}
