<?php
use Modules\Core\Controllers\AdminController;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Controllers\GuestController;
use Modules\Core\Controllers\UserController;
use Modules\Core\Models\BaseModel;
use Modules\Core\Models\FormValidationModel;
use Modules\Core\Models\MyModel;
use Modules\Core\Models\ResponseModel;


namespace Modules\Families\Models;

use Modules\Core\Models\ResponseModel;
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
