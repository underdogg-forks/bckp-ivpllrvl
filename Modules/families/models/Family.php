<?php

namespace Modules\Families\Models;

use Modules\Core\Models\ResponseModel;

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */
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
