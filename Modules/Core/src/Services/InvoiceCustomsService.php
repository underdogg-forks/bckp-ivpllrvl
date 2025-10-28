<?php

namespace Modules\Core\Services;

use AllowDynamicProperties;
use Modules\Core\Services\BaseService;

#[AllowDynamicProperties]
class InvoiceCustomsService extends BaseService
{
    public static $positions = ['custom_fields', 'properties'];

    public $table = 'ip_invoice_custom';

    public $primary_key = 'ip_invoice_custom.invoice_custom_id';

    /**
     * @originalName defaultSelect
     *
     * @originalFile InvoiceCustom.php
     */
    public function defaultSelect()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS ip_invoice_custom.*, ip_custom_fields.*', false);
    }

    /**
     * @originalName defaultJoin
     *
     * @originalFile InvoiceCustom.php
     */
    public function defaultJoin()
    {
        $this->db->join('ip_custom_fields', 'ip_invoice_custom.invoice_custom_fieldid = ip_custom_fields.custom_field_id');
    }

    /**
     * @originalName defaultOrderBy
     *
     * @originalFile InvoiceCustom.php
     */
    public function defaultOrderBy()
    {
        $this->db->orderBy('custom_field_table ASC, custom_field_order ASC, custom_field_label ASC');
    }

    /**
     * Persist invoice custom field values from the service's form data after validating the provided input.
     *
     * @param int|string $invoice_id the identifier of the invoice to which custom fields belong
     * @param array      $db_array   data used for validation of the custom field input
     *
     * @return true|mixed `true` if validation passed and values were processed (or no form data was present); otherwise the validation result returned by validate()
     */
    public function saveCustom($invoice_id, $db_array)
    {
        $result = $this->validate($db_array);
        if ($result === true) {
            $form_data = property_exists($this, '_formdata') && $this->_formdata !== null ? $this->_formdata : null;
            if (null === $form_data) {
                return true;
            }
            foreach ($form_data as $key => $value) {
                \Modules\Invoices\Models\InvoiceCustom::query()->updateOrCreate(
                    [
                        'invoice_id'             => $invoice_id,
                        'invoice_custom_fieldid' => $key,
                    ],
                    [
                        'invoice_id'                => $invoice_id,
                        'invoice_custom_fieldid'    => $key,
                        'invoice_custom_fieldvalue' => $value,
                    ]
                );
            }

            return true;
        }

        return $result;
    }

    /**
     * @originalName byId
     *
     * @originalFile InvoiceCustom.php
     */
    public function byId($invoice_id)
    {
        $this->db->where('ip_invoice_custom.invoice_id', $invoice_id);

        return $this;
    }
}
