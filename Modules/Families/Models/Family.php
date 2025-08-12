<?php

namespace Modules\Families\Models;

use AllowDynamicProperties;
use Modules\Core\Models\ResponseModel;

#[AllowDynamicProperties]
class Family extends ResponseModel
{
    public $table = 'ip_families';

    public $primary_key = 'ip_families.family_id';

    /**
     * @originalName defaultSelect
     *
     * @originalFile Family.php
     */
    public function defaultSelect()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', false);
    }

    /**
     * @originalName defaultOrderBy
     *
     * @originalFile Family.php
     */
    public function defaultOrderBy()
    {
        $this->db->orderBy('ip_families.family_name');
    }

    /**
     * @originalName validationRules
     *
     * @originalFile Family.php
     */
    public function validationRules()
    {
        return ['family_name' => ['field' => 'family_name', 'label' => trans('family_name'), 'rules' => 'required']];
    }
}
