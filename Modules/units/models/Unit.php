<?php

namespace Modules\Units\Models;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */
#[AllowDynamicProperties]
class Unit extends ResponseModel
{
    public $table = 'ip_units';
    public $primary_key = 'ip_units.unit_id';
    /**
     * @originalName defaultSelect
     * @originalFile Unit.php
     */
    public function defaultSelect()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', false);
    }
    /**
     * @originalName defaultOrderBy
     * @originalFile Unit.php
     */
    public function defaultOrderBy()
    {
        $this->db->orderBy('ip_units.unit_name');
    }
    /**
     * @originalName getName
     * @originalFile Unit.php
     */
    public function getName($unit_id, $quantity)
    {
        if ($unit_id) {
            $units = $this->get()->result();
            foreach ($units as $unit) {
                if ($unit->unit_id == $unit_id) {
                    if ($quantity < -1 || $quantity > 1) {
                        // Fix 0
                        return $unit->unit_name_plrl;
                    }
                    return $unit->unit_name;
                }
            }
        }
    }
    /**
     * @originalName validationRules
     * @originalFile Unit.php
     */
    public function validationRules()
    {
        return ['unit_name' => ['field' => 'unit_name', 'label' => trans('unit_name'), 'rules' => 'required'], 'unit_name_plrl' => ['field' => 'unit_name_plrl', 'label' => trans('unit_name_plrl'), 'rules' => 'required']];
    }
}
