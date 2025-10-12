<?php

namespace Modules\Quotes\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use AllowDynamicProperties;
use Modules\Core\Services\BaseService;
use Modules\Quotes\Models\QuoteTaxRate;

#[AllowDynamicProperties]
class QuoteTaxRatesService extends BaseService
{
    public $table = 'ip_quote_tax_rates';

    public $primary_key = 'ip_quote_tax_rates.quote_tax_rate_id';

    /**
     * @originalName defaultSelect
     *
     * @originalFile QuoteTaxRate.php
     */
    public function defaultSelect()
    {
        $this->db->select('ip_tax_rates.tax_rate_name AS quote_tax_rate_name');
        $this->db->select('ip_tax_rates.tax_rate_percent AS quote_tax_rate_percent');
        $this->db->select('ip_quote_tax_rates.*');
    }

    /**
     * @originalName defaultJoin
     *
     * @originalFile QuoteTaxRate.php
     */
    public function defaultJoin()
    {
        $this->db->join('ip_tax_rates', 'ip_tax_rates.tax_rate_id = ip_quote_tax_rates.tax_rate_id');
    }

    /**
     * @originalName save
     *
     * @originalFile QuoteTaxRate.php
     */
    public function save(Request $request, $id = null, $db_array = null) {
        // Only appliable in legacy calculation - since 1.6.3
        config('legacy_calculation') && parent::save($id, $db_array);
        $this->load->model('quotes/mdl_quote_amounts');
        $quote_id = $db_array['quote_id'] ?? $request->post('quote_id');
        if ($quote_id) {
            $global_discount['item'] = $this->mdl_quote_amounts->getGlobalDiscount($quote_id);
            // Recalculate quote amounts
            $this->mdl_quote_amounts->calculate($quote_id, $global_discount);
        }
    }

    /**
     * @originalName validationRules
     *
     * @originalFile QuoteTaxRate.php
     */
    public function validationRules()
    {
        return ['quote_id' => ['field' => 'quote_id', 'label' => trans('quote'), 'rules' => 'required'], 'tax_rate_id' => ['field' => 'tax_rate_id', 'label' => trans('tax_rate'), 'rules' => 'required'], 'include_item_tax' => ['field' => 'include_item_tax', 'label' => trans('tax_rate_placement'), 'rules' => 'required']];
    }

    /**
     * Retrieve all quote tax rate records with their associated tax rate relation.
     *
     * @return \Illuminate\Database\Eloquent\Collection collection of QuoteTaxRate models with the `taxRate` relationship loaded
     */
    public function getWithTaxRate(): \Illuminate\Database\Eloquent\Collection
    {
        return QuoteTaxRate::with('taxRate')->get();
    }

    /**
     * Creates or updates a QuoteTaxRate record and triggers quote amounts recalculation when a `quote_id` is provided.
     *
     * @param array $data Attributes for the QuoteTaxRate. Recognized keys include:
     *                    - `quote_tax_rate_id` (optional): ID of an existing record to update.
     *                    - `quote_id` (optional): when present, causes quote amounts to be recalculated.
     *                    - other QuoteTaxRate model attributes to be saved.
     *
     * @return QuoteTaxRate the created or updated QuoteTaxRate instance
     */
    public function saveTaxRate(array $data): QuoteTaxRate
    {
        $quoteTaxRate = QuoteTaxRate::updateOrCreate(
            ['quote_tax_rate_id' => $data['quote_tax_rate_id'] ?? null],
            $data
        );
        // Trigger recalculation if needed
        if (isset($data['quote_id'])) {
            app(QuoteAmountsService::class)->calculate($data['quote_id'], []);
        }

        return $quoteTaxRate;
    }

    /**
     * Retrieve all quote tax rate records associated with the specified quote.
     *
     * @param int $quote_id the quote ID to filter tax rates by
     *
     * @return \Illuminate\Database\Eloquent\Collection a collection of QuoteTaxRate models for the given quote
     */
    public function getByQuoteId($quote_id)
    {
        return \Modules\Quotes\Models\QuoteTaxRate::query()->where('quote_id', $quote_id)->get();
    }
}
