<?php

namespace Modules\Invoices\Services;

use Illuminate\Support\Facades\DB;
use AllowDynamicProperties;
use Modules\Core\Services\BaseService;
use Modules\Invoices\Models\InvoiceSumex;

#[AllowDynamicProperties]
class InvoiceSumexService extends BaseService
{
    public $table = 'ip_invoice_sumex';

    public $primary_key = 'ip_invoice_sumex.sumex_id';

    /**
     * @originalName defaultSelect
     *
     * @originalFile InvoiceSumex.php
     */
    public function defaultSelect()
    {
        $this->db->select('ip_invoice_sumex.*');
    }

    /**
     * Save or update the Sumex record associated with the given invoice.
     *
     * Resolves the internal Sumex primary key for the provided invoice id (if any)
     * and delegates the save operation to the parent service.
     *
     * @param int|null   $id       invoice id used to locate an existing Sumex record
     * @param array|null $db_array associative array of fields to save; forwarded to the parent save method
     */
    public function save($id = null, $db_array = null)
    {
        $sumex    = InvoiceSumex::query()->where('sumex_invoice', $id)->first();
        $sumex_id = $sumex ? $sumex->sumex_id : null;
        parent::save($sumex_id, $db_array);
    }

    /**
     * @originalName validationRules
     *
     * @originalFile InvoiceSumex.php
     */
    public function validationRules()
    {
        return ['sumex_invoice' => ['field' => 'sumex_invoice', 'label' => trans('invoice'), 'rules' => 'required'], 'sumex_reason' => ['field' => 'sumex_reason', 'label' => trans('reason'), 'rules' => 'required|greater_than_equal_to[0]|less_than_equal_to[5]'], 'sumex_diagnosis' => ['field' => 'sumex_diagnosis', 'label' => trans('diagnosis')], 'sumex_observations' => ['field' => 'sumex_observations', 'label' => trans('sumex_observations')], 'sumex_treatmentstart' => ['field' => 'sumex_treatmentstart', 'label' => trans('start'), 'rules' => 'required'], 'sumex_treatmentend' => ['field' => 'sumex_treatmentend', 'label' => trans('end'), 'rules' => 'required'], 'sumex_casedate' => ['field' => 'sumex_casedate', 'label' => trans('case_date'), 'rules' => 'required'], 'sumex_casenumber' => ['field' => 'sumex_casenumber', 'label' => trans('case_number')]];
    }
}
