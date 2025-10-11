<?php

namespace Modules\CustomFields\Services;

use AllowDynamicProperties;
use Modules\Core\Services\BaseService;

#[AllowDynamicProperties]
class QuoteCustomsService extends BaseService
{
    public static $positions = ['custom_fields', 'properties'];

    public $table = 'ip_quote_custom';

    public $primary_key = 'ip_quote_custom.quote_custom_id';

    /**
     * @originalName defaultSelect
     *
     * @originalFile QuoteCustom.php
     */
    public function defaultSelect()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS ip_quote_custom.*, ip_custom_fields.*', false);
    }

    /**
     * @originalName defaultJoin
     *
     * @originalFile QuoteCustom.php
     */
    public function defaultJoin()
    {
        $this->db->join('ip_custom_fields', 'ip_quote_custom.quote_custom_fieldid = ip_custom_fields.custom_field_id');
    }

    /**
     * @originalName defaultOrderBy
     *
     * @originalFile QuoteCustom.php
     */
    public function defaultOrderBy()
    {
        $this->db->orderBy('custom_field_table ASC, custom_field_order ASC, custom_field_label ASC');
    }

    /**
         * Persist custom field values for a quote.
         *
         * Validates the provided data and, if valid, upserts each entry from the instance's
         * form data into the quote custom fields table for the given quote ID.
         *
         * @param int $quote_id The ID of the quote to associate custom field values with.
         * @param array $db_array Data used for validation (the function reads actual values from $this->_formdata).
         * @return mixed `true` if values were persisted or there was no form data to process; otherwise the validation result.
         */
    public function saveCustom($quote_id, $db_array)
    {
        $result = $this->validate($db_array);
        if ($result === true) {
            $form_data = property_exists($this, '_formdata') && $this->_formdata !== null ? $this->_formdata : null;
            if (null === $form_data) {
                return true;
            }
            foreach ($form_data as $key => $value) {
                \Modules\Quotes\Models\QuoteCustom::query()->updateOrCreate(
                    [
                        'quote_id' => $quote_id,
                        'quote_custom_fieldid' => $key
                    ],
                    [
                        'quote_id' => $quote_id,
                        'quote_custom_fieldid' => $key,
                        'quote_custom_fieldvalue' => $value
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
     * @originalFile QuoteCustom.php
     */
    public function byId($quote_id)
    {
        $this->db->where('ip_quote_custom.quote_id', $quote_id);

        return $this;
    }
}