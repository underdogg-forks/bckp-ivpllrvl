<?php

namespace Modules\Invoices\Models;

if (!defined('BASEPATH')) {
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
class InvoiceSumex extends ResponseModel
{
    public $table = 'ip_invoice_sumex';
    public $primary_key = 'ip_invoice_sumex.sumex_id';
    /**
     * @originalName defaultSelect
     * @originalFile InvoiceSumex.php
     */
    public function defaultSelect()
    {
        $this->db->select('ip_invoice_sumex.*');
    }
    /**
     * @originalName save
     * @originalFile InvoiceSumex.php
     */
    public function save($id = null, $db_array = null)
    {
        $id = $this->where('sumex_invoice', $id)->get()->row()->sumex_id;
        parent::save($id, $db_array);
    }
    /**
     * @originalName validationRules
     * @originalFile InvoiceSumex.php
     */
    public function validationRules()
    {
        return ['sumex_invoice' => ['field' => 'sumex_invoice', 'label' => trans('invoice'), 'rules' => 'required'], 'sumex_reason' => ['field' => 'sumex_reason', 'label' => trans('reason'), 'rules' => 'required|greater_than_equal_to[0]|less_than_equal_to[5]'], 'sumex_diagnosis' => ['field' => 'sumex_diagnosis', 'label' => trans('diagnosis')], 'sumex_observations' => ['field' => 'sumex_observations', 'label' => trans('sumex_observations')], 'sumex_treatmentstart' => ['field' => 'sumex_treatmentstart', 'label' => trans('start'), 'rules' => 'required'], 'sumex_treatmentend' => ['field' => 'sumex_treatmentend', 'label' => trans('end'), 'rules' => 'required'], 'sumex_casedate' => ['field' => 'sumex_casedate', 'label' => trans('case_date'), 'rules' => 'required'], 'sumex_casenumber' => ['field' => 'sumex_casenumber', 'label' => trans('case_number')]];
    }
}
