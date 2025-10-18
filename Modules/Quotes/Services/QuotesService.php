<?php

namespace Modules\Quotes\Services;

use AllowDynamicProperties;
use Illuminate\Http\Request;
use Modules\Core\Services\BaseService;
use Modules\Quotes\Models\Quote;

#[AllowDynamicProperties]
class QuotesService extends BaseService
{
    public $table = 'ip_quotes';

    public $primary_key = 'ip_quotes.quote_id';

    public $date_modified_field = 'quote_date_modified';

    /**
     * @originalName statuses
     *
     * @originalFile Quote.php
     */
    public function statuses()
    {
        return ['1' => ['label' => trans('draft'), 'class' => 'draft', 'href' => 'quotes/status/draft'], '2' => ['label' => trans('sent'), 'class' => 'sent', 'href' => 'quotes/status/sent'], '3' => ['label' => trans('viewed'), 'class' => 'viewed', 'href' => 'quotes/status/viewed'], '4' => ['label' => trans('approved'), 'class' => 'approved', 'href' => 'quotes/status/approved'], '5' => ['label' => trans('rejected'), 'class' => 'rejected', 'href' => 'quotes/status/rejected'], '6' => ['label' => trans('canceled'), 'class' => 'canceled', 'href' => 'quotes/status/canceled']];
    }

    /**
     * Get a base Quote query with relationships for select.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function defaultSelect(): \Illuminate\Database\Eloquent\Builder
    {
        return Quote::query()->with(['user', 'client', 'quoteAmount', 'invoice']);
    }

    /**
     * Get a Quote query ordered by creation date, number, and id.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function defaultOrderBy(): \Illuminate\Database\Eloquent\Builder
    {
        return Quote::query()->orderByDesc('quote_date_created')->orderByDesc('quote_number')->orderByDesc('quote_id');
    }

    /**
     * Get a Quote query with relationships (joins).
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function defaultJoin(): \Illuminate\Database\Eloquent\Builder
    {
        return Quote::query()->with(['user', 'client', 'quoteAmount', 'invoice']);
    }

    /**
     * @originalName validationRules
     *
     * @originalFile Quote.php
     */
    public function validationRules()
    {
        return ['client_id' => ['field' => 'client_id', 'label' => trans('client'), 'rules' => 'required'], 'quote_date_created' => ['field' => 'quote_date_created', 'label' => trans('quote_date'), 'rules' => 'required'], 'invoice_group_id' => ['field' => 'invoice_group_id', 'label' => trans('quote_group'), 'rules' => 'required'], 'quote_password' => ['field' => 'quote_password', 'label' => trans('quote_password')], 'user_id' => ['field' => 'user_id', 'label' => trans('user'), 'rule' => 'required']];
    }

    /**
     * @originalName validationRulesSaveQuote
     *
     * @originalFile Quote.php
     */
    public function validationRulesSaveQuote()
    {
        return ['quote_number' => ['field' => 'quote_number', 'label' => trans('quote') . ' #', 'rules' => 'is_unique[ip_quotes.quote_number' . ($this->id ? '.quote_id.' . $this->id : '') . ']'], 'quote_date_created' => ['field' => 'quote_date_created', 'label' => trans('date'), 'rules' => 'required'], 'quote_date_expires' => ['field' => 'quote_date_expires', 'label' => trans('due_date'), 'rules' => 'required'], 'quote_password' => ['field' => 'quote_password', 'label' => trans('quote_password')]];
    }

    /**
     * Create a quote record and its related amount and default tax records.
     *
     * @param array|null $db_array optional associative array of quote attributes to set before saving
     *
     * @return int the created quote's primary key (`quote_id`)
     */
    public function create(?array $db_array = null): int
    {
        $quote = new Quote();
        if ($db_array) {
            $quote->fill($db_array);
        }
        $quote->save();
        // Create related quote amount record
        $quote->quoteAmount()->create(['quote_id' => $quote->quote_id]);
        // Create default invoice tax record if applicable
        if (get_setting('default_invoice_tax_rate')) {
            $quote->quoteTaxRates()->create([
                'tax_rate_id'           => get_setting('default_invoice_tax_rate'),
                'include_item_tax'      => get_setting('default_include_item_tax'),
                'quote_tax_rate_amount' => 0,
            ]);
        }

        return $quote->quote_id;
    }

    /**
     * Copy line items, tax rates, global discount, and non-primary-key custom fields from one quote to another.
     *
     * Duplicates each item from the source quote into the target while preserving item name, description,
     * quantity, price, tax rate, product/unit references, and applies the source quote's global discount to the copied items.
     * Also copies the source quote's tax rate entries and any custom fields (excluding primary key) to the target.
     *
     * @param int $source_id the ID of the quote to copy from
     * @param int $target_id the ID of the quote to copy to
     */
    public function copyQuote($source_id, $target_id)
    {
        $this->load->model('quotes/mdl_quote_items');
        // Discounts calculation - since v1.6.3 Need if taxes applied after discounts
        $quote = $this->getById($source_id);
        // This is the original quote
        $global_discount = [
            'amount'  => $quote->quote_discount_amount,
            'percent' => $quote->quote_discount_percent,
            'item'    => 0.0,
            // Updated by ref (Need for quote_item_subtotal calculation in Mdl_quote_amounts)
            'items_subtotal' => $this->mdl_quote_items->getItemsSubtotal($source_id),
        ];
        unset($quote);
        // Free memory
        // Update the discounts - since v1.6.3
        $this->where('quote_id', $target_id)->update('ip_quotes', ['quote_discount_percent' => $global_discount['percent'], 'quote_discount_amount' => $global_discount['amount']]);
        $quote_items = (new QuoteItemsService())->getByQuoteId($source_id);
        foreach ($quote_items as $quote_item) {
            $db_array = ['quote_id' => $target_id, 'item_tax_rate_id' => $quote_item->item_tax_rate_id, 'item_product_id' => $quote_item?->item_product_id, 'item_name' => $quote_item->item_name, 'item_description' => $quote_item->item_description, 'item_quantity' => $quote_item->item_quantity, 'item_price' => $quote_item->item_price, 'item_discount_amount' => $quote_item?->item_discount_amount, 'item_order' => $quote_item->item_order, 'item_product_unit' => $quote_item?->item_product_unit, 'item_product_unit_id' => $quote_item?->item_product_unit_id];
            $this->mdl_quote_items->save(null, $db_array, $global_discount);
        }
        $quote_tax_rates = (new QuoteTaxRatesService())->getByQuoteId($source_id);
        foreach ($quote_tax_rates as $quote_tax_rate) {
            $db_array = ['quote_id' => $target_id, 'tax_rate_id' => $quote_tax_rate->tax_rate_id, 'include_item_tax' => $quote_tax_rate->include_item_tax, 'quote_tax_rate_amount' => $quote_tax_rate->quote_tax_rate_amount];
            $this->mdl_quote_tax_rates->save(null, $db_array);
        }
        // Copy the custom fields
        $this->load->model('custom_fields/mdl_quote_custom');
        $db_array = $this->mdl_quote_custom->where('quote_id', $source_id)->get()->rowArray() ?? [];
        if (count($db_array) > 2) {
            unset($db_array['quote_custom_id']);
            $db_array['quote_id'] = $target_id;
            $this->mdl_quote_custom->saveCustom($target_id, $db_array);
        }
    }

    /**
     * @originalName dbArray
     *
     * @originalFile Quote.php
     */
    public function dbArray(Request $request = null)
    {
        $db_array = parent::dbArray($request);
        // GetController the client id for the submitted quote
        $this->load->model('clients/mdl_clients');
        $cid                            = $this->mdl_clients->where('ip_clients.client_id', $db_array['client_id'])->get()->row()->client_id;
        $db_array['client_id']          = $cid;
        $db_array['quote_date_created'] = date_to_mysql($db_array['quote_date_created']);
        $db_array['quote_date_expires'] = $this->getDateDue($db_array['quote_date_created']);
        $db_array['notes']              = get_setting('default_quote_notes');
        if ( ! isset($db_array['quote_status_id'])) {
            $db_array['quote_status_id'] = 1;
        }
        $generate_quote_number = get_setting('generate_quote_number_for_draft');
        if ($db_array['quote_status_id'] === 1 && $generate_quote_number == 1) {
            $db_array['quote_number'] = $this->getQuoteNumber($db_array['invoice_group_id']);
        } elseif ($db_array['quote_status_id'] != 1) {
            $db_array['quote_number'] = $this->getQuoteNumber($db_array['invoice_group_id']);
        } else {
            $db_array['quote_number'] = '';
        }
        // Generate the unique url key
        $db_array['quote_url_key'] = $this->getUrlKey();

        return $db_array;
    }

    /**
     * @originalName getDateDue
     *
     * @originalFile Quote.php
     */
    public function getDateDue($quote_date_created)
    {
        $quote_date_expires = new DateTime($quote_date_created);
        $quote_date_expires->add(new DateInterval('P' . get_setting('quotes_expire_after') . 'D'));

        return $quote_date_expires->format('Y-m-d');
    }

    /**
     * @originalName getQuoteNumber
     *
     * @originalFile Quote.php
     */
    public function getQuoteNumber($invoice_group_id)
    {
        $this->load->model('invoice_groups/mdl_invoice_groups');

        return $this->mdl_invoice_groups->generateInvoiceNumber($invoice_group_id);
    }

    /**
     * @originalName getUrlKey
     *
     * @originalFile Quote.php
     */
    public function getUrlKey()
    {
        $this->load->helper('string');

        return random_string('alnum', 32);
    }

    /**
     * @originalName getInvoiceGroupId
     *
     * @originalFile Quote.php
     */
    public function getInvoiceGroupId($invoice_id)
    {
        $invoice = $this->getById($invoice_id);

        return $invoice->invoice_group_id;
    }

    /**
     * @originalName delete
     *
     * @originalFile Quote.php
     */
    public function delete($quote_id)
    {
        parent::delete($quote_id);
        $this->load->helper('orphan');
        delete_orphans();
    }

    /**
     * @originalName isDraft
     *
     * @originalFile Quote.php
     */
    public function isDraft()
    {
        $this->filter_where('quote_status_id', 1);

        return $this;
    }

    /**
     * @originalName isSent
     *
     * @originalFile Quote.php
     */
    public function isSent()
    {
        $this->filter_where('quote_status_id', 2);

        return $this;
    }

    /**
     * @originalName isViewed
     *
     * @originalFile Quote.php
     */
    public function isViewed()
    {
        $this->filter_where('quote_status_id', 3);

        return $this;
    }

    /**
     * @originalName isApproved
     *
     * @originalFile Quote.php
     */
    public function isApproved()
    {
        $this->filter_where('quote_status_id', 4);

        return $this;
    }

    /**
     * @originalName isRejected
     *
     * @originalFile Quote.php
     */
    public function isRejected()
    {
        $this->filter_where('quote_status_id', 5);

        return $this;
    }

    /**
     * @originalName isCanceled
     *
     * @originalFile Quote.php
     */
    public function isCanceled()
    {
        $this->filter_where('quote_status_id', 6);

        return $this;
    }

    /**
     * @originalName isOpen
     *
     * @originalFile Quote.php
     */
    public function isOpen()
    {
        $this->filter_where_in('quote_status_id', [2, 3]);

        return $this;
    }

    /**
     * @originalName guestVisible
     *
     * @originalFile Quote.php
     */
    public function guestVisible()
    {
        $this->filter_where_in('quote_status_id', [2, 3, 4, 5]);

        return $this;
    }

    /**
     * @originalName byClient
     *
     * @originalFile Quote.php
     */
    public function byClient($client_id)
    {
        $this->filter_where('ip_quotes.client_id', $client_id);

        return $this;
    }

    /**
     * Approve a quote identified by its URL key.
     *
     * Updates the quote's status to approved (4) for any quote matching the given URL key that is currently in sent (2) or viewed (3) status.
     *
     * @param string $quote_url_key the quote's public URL key
     */
    public function approveQuoteByKey($quote_url_key)
    {
        Quote::query()
            ->whereIn('quote_status_id', [2, 3])
            ->where('quote_url_key', $quote_url_key)
            ->update(['quote_status_id' => 4]);
    }

    /**
     * Mark the quote identified by the given URL key as rejected when its current status is "sent" or "viewed".
     *
     * Updates matching quotes' status to the rejected status (5) for records whose `quote_url_key` equals
     * the provided key and whose current `quote_status_id` is 2 (sent) or 3 (viewed).
     *
     * @param string $quote_url_key the public URL key of the quote
     */
    public function rejectQuoteByKey($quote_url_key)
    {
        Quote::query()
            ->whereIn('quote_status_id', [2, 3])
            ->where('quote_url_key', $quote_url_key)
            ->update(['quote_status_id' => 5]);
    }

    /**
     * Mark a quote as approved if its current status is "sent" or "viewed".
     *
     * Updates the quote's status to approved for the given quote id only when the quote is in status 2 (sent) or 3 (viewed).
     *
     * @param int $quote_id the ID of the quote to approve
     */
    public function approveQuoteById($quote_id)
    {
        Quote::query()
            ->whereIn('quote_status_id', [2, 3])
            ->where('quote_id', $quote_id)
            ->update(['quote_status_id' => 4]);
    }

    /**
     * Mark the specified quote as rejected when its current status is "sent" or "viewed".
     *
     * @param int $quote_id the ID of the quote to reject
     */
    public function rejectQuoteById($quote_id)
    {
        Quote::query()
            ->whereIn('quote_status_id', [2, 3])
            ->where('quote_id', $quote_id)
            ->update(['quote_status_id' => 5]);
    }

    /**
     * Change a quote's status from "sent" to "viewed" when the quote is currently sent.
     *
     * If the quote exists and its status is "sent", this updates the status to "viewed".
     *
     * @param int $quote_id the ID of the quote to mark as viewed
     */
    public function markViewed($quote_id)
    {
        $quote = Quote::query()
            ->select('quote_status_id')
            ->where('quote_id', $quote_id)
            ->first();

        if ($quote && $quote->quote_status_id == 2) {
            Quote::query()->where('quote_id', $quote_id)->update(['quote_status_id' => 3]);
        }
    }

    /**
     * Set a quote's status to "sent" when the quote is currently a draft.
     *
     * Updates the quote's `quote_status_id` to 2 if the quote exists and its current `quote_status_id` is 1.
     *
     * @param int $quote_id the ID of the quote to mark as sent
     */
    public function markSent($quote_id)
    {
        $quote = Quote::query()
            ->select('quote_status_id')
            ->where('quote_id', $quote_id)
            ->first();

        if ($quote && $quote->quote_status_id == 1) {
            Quote::query()->where('quote_id', $quote_id)->update(['quote_status_id' => 2]);
        }
    }

    /**
     * Assigns a generated quote number to a draft quote when application settings require it.
     *
     * Checks the specified quote; if it exists, has status 1 (draft), has an empty quote_number,
     * and the `generate_quote_number_for_draft` setting is 0, generates a new quote number for the
     * quote's invoice group and updates the quote record with that number.
     *
     * @param int $quote_id the ID of the quote to evaluate and potentially update
     */
    public function generateQuoteNumberIfApplicable($quote_id)
    {
        $quote = $this->mdl_quotes->getById($quote_id);
        // Generate new quote number if applicable
        if ( ! empty($quote) && ($quote->quote_status_id == 1 && $quote->quote_number == '') && get_setting('generate_quote_number_for_draft') == 0) {
            $quote_number = $this->mdl_quotes->getQuoteNumber($quote->invoice_group_id);
            // Set new quote number and save
            Quote::query()->where('quote_id', $quote_id)->update(['quote_number' => $quote_number]);
        }
    }

    /**
     * Retrieve all quotes with their related user, client, quoteAmount, and invoice models, ordered by creation date, quote number, and quote id in descending order.
     *
     * @return \Illuminate\Database\Eloquent\Collection collection of Quote models with the specified relations loaded
     */
    public function getQuotesWithRelations(): \Illuminate\Database\Eloquent\Collection
    {
        return Quote::with(['user', 'client', 'quoteAmount', 'invoice'])
            ->orderByDesc('quote_date_created')
            ->orderByDesc('quote_number')
            ->orderByDesc('quote_id')
            ->get();
    }
}
