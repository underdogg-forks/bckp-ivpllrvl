<?php
use Modules\Core\Controllers\AdminController;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Controllers\GuestController;
use Modules\Core\Controllers\UserController;
use Modules\Core\Models\BaseModel;
use Modules\Core\Models\FormValidationModel;
use Modules\Core\Models\MyModel;
use Modules\Core\Models\ResponseModel;


namespace Modules\Taxrates\Models;

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */
#[AllowDynamicProperties]
class TaxRate extends ResponseModel
{
    public $table = 'ip_tax_rates';
    public $primary_key = 'ip_tax_rates.tax_rate_id';
    /**
     * @originalName defaultSelect
     *
     * @originalFile TaxRate.php
     */
    public function defaultSelect()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', false);
    }
    /**
     * @originalName defaultOrderBy
     *
     * @originalFile TaxRate.php
     */
    public function defaultOrderBy()
    {
        $this->db->orderBy('ip_tax_rates.tax_rate_percent');
    }
    /**
     * @originalName validationRules
     *
     * @originalFile TaxRate.php
     */
    public function validationRules()
    {
        return ['tax_rate_name' => ['field' => 'tax_rate_name', 'label' => trans('tax_rate_name'), 'rules' => 'required'], 'tax_rate_percent' => ['field' => 'tax_rate_percent', 'label' => trans('tax_rate_percent'), 'rules' => 'required']];
    }
}
