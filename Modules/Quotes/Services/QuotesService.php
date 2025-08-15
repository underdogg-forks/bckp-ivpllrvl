<?php

namespace Modules\Quotes\Services;

use AllowDynamicProperties;
use App\Services\BaseService;

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
     * @originalName defaultSelect
     *
     * @originalFile Quote.php
     */
    public function defaultSelect()
    {
        $this->db->select("\n            SQL_CALC_FOUND_ROWS\n            ip_users.*,\n            ip_clients.*,\n            ip_quote_amounts.quote_amount_id,\n            IFNULL(ip_quote_amounts.quote_item_subtotal, '0.00') AS quote_item_subtotal,\n            IFNULL(ip_quote_amounts.quote_item_tax_total, '0.00') AS quote_item_tax_total,\n            IFNULL(ip_quote_amounts.quote_tax_total, '0.00') AS quote_tax_total,\n            IFNULL(ip_quote_amounts.quote_total, '0.00') AS quote_total,\n            ip_invoices.invoice_number,\n            ip_quotes.*", false);
    }

    /**
     * @originalName defaultOrderBy
     *
     * @originalFile Quote.php
     */
    public function defaultOrderBy()
    {
        $this->db->orderBy('ip_quotes.quote_date_created DESC, ip_quotes.quote_number DESC, ip_quotes.quote_id DESC');
    }

    /**
     * @originalName defaultJoin
     *
     * @originalFile Quote.php
     */
    public function defaultJoin()
    {
        $this->db->join('ip_clients', 'ip_clients.client_id = ip_quotes.client_id');
        $this->db->join('ip_users', 'ip_users.user_id = ip_quotes.user_id');
        $this->db->join('ip_quote_amounts', 'ip_quote_amounts.quote_id = ip_quotes.quote_id', 'left');
        $this->db->join('ip_invoices', 'ip_invoices.invoice_id = ip_quotes.invoice_id', 'left');
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
     * @originalName create
     *
     * @originalFile Quote.php
     */
    public function create($db_array = null)
    {
        $quote_id = parent::save(null, $db_array);
        // Create an quote amount record
        $db_array = ['quote_id' => $quote_id];
        $this->db->insert('ip_quote_amounts', $db_array);
        // Create the default invoice tax record if applicable
        if (get_setting('default_invoice_tax_rate')) {
            $db_array = ['quote_id' => $quote_id, 'tax_rate_id' => get_setting('default_invoice_tax_rate'), 'include_item_tax' => get_setting('default_include_item_tax'), 'quote_tax_rate_amount' => 0];
            $this->db->insert('ip_quote_tax_rates', $db_array);
        }

        return $quote_id;
    }

    /**
     * @originalName copyQuote
     *
     * @originalFile Quote.php
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
        $quote_items = $this->mdl_quote_items->where('quote_id', $source_id)->get()->result();
        foreach ($quote_items as $quote_item) {
            $db_array = ['quote_id' => $target_id, 'item_tax_rate_id' => $quote_item->item_tax_rate_id, 'item_product_id' => $quote_item?->item_product_id, 'item_name' => $quote_item->item_name, 'item_description' => $quote_item->item_description, 'item_quantity' => $quote_item->item_quantity, 'item_price' => $quote_item->item_price, 'item_discount_amount' => $quote_item?->item_discount_amount, 'item_order' => $quote_item->item_order, 'item_product_unit' => $quote_item?->item_product_unit, 'item_product_unit_id' => $quote_item?->item_product_unit_id];
            $this->mdl_quote_items->save(null, $db_array, $global_discount);
        }
        $quote_tax_rates = $this->mdl_quote_tax_rates->where('quote_id', $source_id)->get()->result();
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
    public function dbArray()
    {
        $db_array = parent::dbArray();
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
     * @originalName approveQuoteByKey
     *
     * @originalFile Quote.php
     */
    public function approveQuoteByKey($quote_url_key)
    {
        $this->db->where_in('quote_status_id', [2, 3]);
        $this->db->where('quote_url_key', $quote_url_key);
        $this->db->set('quote_status_id', 4);
        $this->db->update('ip_quotes');
    }

    /**
     * @originalName rejectQuoteByKey
     *
     * @originalFile Quote.php
     */
    public function rejectQuoteByKey($quote_url_key)
    {
        $this->db->where_in('quote_status_id', [2, 3]);
        $this->db->where('quote_url_key', $quote_url_key);
        $this->db->set('quote_status_id', 5);
        $this->db->update('ip_quotes');
    }

    /**
     * @originalName approveQuoteById
     *
     * @originalFile Quote.php
     */
    public function approveQuoteById($quote_id)
    {
        $this->db->where_in('quote_status_id', [2, 3]);
        $this->db->where('quote_id', $quote_id);
        $this->db->set('quote_status_id', 4);
        $this->db->update('ip_quotes');
    }

    /**
     * @originalName rejectQuoteById
     *
     * @originalFile Quote.php
     */
    public function rejectQuoteById($quote_id)
    {
        $this->db->where_in('quote_status_id', [2, 3]);
        $this->db->where('quote_id', $quote_id);
        $this->db->set('quote_status_id', 5);
        $this->db->update('ip_quotes');
    }

    /**
     * @originalName markViewed
     *
     * @originalFile Quote.php
     */
    public function markViewed($quote_id)
    {
        $this->db->select('quote_status_id');
        $this->db->where('quote_id', $quote_id);
        $quote = $this->db->get('ip_quotes');
        if ($quote->numRows() && $quote->row()->quote_status_id == 2) {
            $this->db->where('quote_id', $quote_id);
            $this->db->set('quote_status_id', 3);
            $this->db->update('ip_quotes');
        }
    }

    /**
     * @originalName markSent
     *
     * @originalFile Quote.php
     */
    public function markSent($quote_id)
    {
        $this->db->select('quote_status_id');
        $this->db->where('quote_id', $quote_id);
        $quote = $this->db->get('ip_quotes');
        if ($quote->numRows() && $quote->row()->quote_status_id == 1) {
            $this->db->where('quote_id', $quote_id);
            $this->db->set('quote_status_id', 2);
            $this->db->update('ip_quotes');
        }
    }

    /**
     * @originalName generateQuoteNumberIfApplicable
     *
     * @originalFile Quote.php
     */
    public function generateQuoteNumberIfApplicable($quote_id)
    {
        $quote = $this->mdl_quotes->getById($quote_id);
        // Generate new quote number if applicable
        if ( ! empty($quote) && ($quote->quote_status_id == 1 && $quote->quote_number == '') && get_setting('generate_quote_number_for_draft') == 0) {
            $quote_number = $this->mdl_quotes->getQuoteNumber($quote->invoice_group_id);
            // Set new quote number and save
            $this->db->where('quote_id', $quote_id);
            $this->db->set('quote_number', $quote_number);
            $this->db->update('ip_quotes');
        }
    }
}
