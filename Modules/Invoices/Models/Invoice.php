<?php

namespace Modules\Invoices\Models;

use AllowDynamicProperties;
use Modules\Core\Models\ResponseModel;

#[AllowDynamicProperties]
class Invoice extends ResponseModel
{
    public $table = 'ip_invoices';

    public $primary_key = 'ip_invoices.invoice_id';

    public $date_modified_field = 'invoice_date_modified';

    /**
     * @originalName statuses
     *
     * @originalFile Invoice.php
     */
    public function statuses()
    {
        return ['1' => ['label' => trans('draft'), 'class' => 'draft', 'href' => 'invoices/status/draft'], '2' => ['label' => trans('sent'), 'class' => 'sent', 'href' => 'invoices/status/sent'], '3' => ['label' => trans('viewed'), 'class' => 'viewed', 'href' => 'invoices/status/viewed'], '4' => ['label' => trans('paid'), 'class' => 'paid', 'href' => 'invoices/status/paid']];
    }

    /**
     * @originalName defaultSelect
     *
     * @originalFile Invoice.php
     */
    public function defaultSelect()
    {
        $this->db->select("\n            SQL_CALC_FOUND_ROWS\n            ip_quotes.*,\n            ip_users.*,\n            ip_clients.*,\n            ip_invoice_sumex.*,\n            ip_invoice_amounts.invoice_amount_id,\n            IFNULL(ip_invoice_amounts.invoice_item_subtotal, '0.00') AS invoice_item_subtotal,\n            IFNULL(ip_invoice_amounts.invoice_item_tax_total, '0.00') AS invoice_item_tax_total,\n            IFNULL(ip_invoice_amounts.invoice_tax_total, '0.00') AS invoice_tax_total,\n            IFNULL(ip_invoice_amounts.invoice_total, '0.00') AS invoice_total,\n            IFNULL(ip_invoice_amounts.invoice_paid, '0.00') AS invoice_paid,\n            IFNULL(ip_invoice_amounts.invoice_balance, '0.00') AS invoice_balance,\n            ip_invoice_amounts.invoice_sign AS invoice_sign,\n            (CASE WHEN ip_invoices.invoice_status_id NOT IN (1,4) AND DATEDIFF(NOW(), invoice_date_due) > 0 THEN 1 ELSE 0 END) is_overdue,\n            DATEDIFF(NOW(), invoice_date_due) AS days_overdue,\n            (CASE (SELECT COUNT(*) FROM ip_invoices_recurring WHERE ip_invoices_recurring.invoice_id = ip_invoices.invoice_id and ip_invoices_recurring.recur_next_date IS NOT NULL) WHEN 0 THEN 0 ELSE 1 END) AS invoice_is_recurring,\n            ip_invoices.*", false);
    }

    /**
     * @originalName defaultOrderBy
     *
     * @originalFile Invoice.php
     */
    public function defaultOrderBy()
    {
        $this->db->orderBy('ip_invoices.invoice_date_created DESC, ip_invoices.invoice_number DESC, ip_invoices.invoice_id DESC');
    }

    /**
     * @originalName defaultJoin
     *
     * @originalFile Invoice.php
     */
    public function defaultJoin()
    {
        $this->db->join('ip_clients', 'ip_clients.client_id = ip_invoices.client_id');
        $this->db->join('ip_users', 'ip_users.user_id = ip_invoices.user_id');
        $this->db->join('ip_invoice_amounts', 'ip_invoice_amounts.invoice_id = ip_invoices.invoice_id', 'left');
        $this->db->join('ip_invoice_sumex', 'sumex_invoice = ip_invoices.invoice_id', 'left');
        $this->db->join('ip_quotes', 'ip_quotes.invoice_id = ip_invoices.invoice_id', 'left');
    }

    /**
     * @originalName validationRules
     *
     * @originalFile Invoice.php
     */
    public function validationRules()
    {
        return ['client_id' => ['field' => 'client_id', 'label' => trans('client'), 'rules' => 'required'], 'invoice_date_created' => ['field' => 'invoice_date_created', 'label' => trans('invoice_date'), 'rules' => 'required'], 'invoice_time_created' => ['rules' => 'required'], 'invoice_group_id' => ['field' => 'invoice_group_id', 'label' => trans('invoice_group'), 'rules' => 'required'], 'invoice_password' => ['field' => 'invoice_password', 'label' => trans('invoice_password')], 'user_id' => ['field' => 'user_id', 'label' => trans('user'), 'rule' => 'required'], 'payment_method' => ['field' => 'payment_method', 'label' => trans('payment_method')]];
    }

    /**
     * @originalName validationRulesSaveInvoice
     *
     * @originalFile Invoice.php
     */
    public function validationRulesSaveInvoice()
    {
        return ['invoice_number' => ['field' => 'invoice_number', 'label' => trans('invoice') . ' #', 'rules' => 'is_unique[ip_invoices.invoice_number' . ($this->id ? '.invoice_id.' . $this->id : '') . ']'], 'invoice_date_created' => ['field' => 'invoice_date_created', 'label' => trans('date'), 'rules' => 'required'], 'invoice_date_due' => ['field' => 'invoice_date_due', 'label' => trans('due_date'), 'rules' => 'required'], 'invoice_time_created' => ['rules' => 'required'], 'invoice_password' => ['field' => 'invoice_password', 'label' => trans('invoice_password')]];
    }

    /**
     * @originalName create
     *
     * @originalFile Invoice.php
     */
    public function create($db_array = null, $include_invoice_tax_rates = true)
    {
        $invoice_id    = parent::save(null, $db_array);
        $inv           = $this->where('ip_invoices.invoice_id', $invoice_id)->get()->row();
        $invoice_group = $inv->invoice_group_id;
        // Create an invoice amount record
        $db_array = ['invoice_id' => $invoice_id];
        $this->db->insert('ip_invoice_amounts', $db_array);
        // Create the default invoice tax record if applicable
        if ($include_invoice_tax_rates && get_setting('default_invoice_tax_rate')) {
            $db_array = ['invoice_id' => $invoice_id, 'tax_rate_id' => get_setting('default_invoice_tax_rate'), 'include_item_tax' => get_setting('default_include_item_tax', 0), 'invoice_tax_rate_amount' => 0];
            $this->db->insert('ip_invoice_tax_rates', $db_array);
        }
        if ($invoice_group !== '0') {
            $this->load->model('invoice_groups/mdl_invoice_groups');
            $invgroup = $this->mdl_invoice_groups->where('invoice_group_id', $invoice_group)->get()->row();
            if (preg_match('/sumex/i', $invgroup->invoice_group_name)) {
                // If the Invoice Group includes "Modules\Core\Libraries\Sumex", make the invoice a Modules\Core\Libraries\Sumex one
                $db_array = ['sumex_invoice' => $invoice_id];
                $this->db->insert('ip_invoice_sumex', $db_array);
            }
        }

        return $invoice_id;
    }

    /**
     * @originalName copyInvoice
     *
     * @originalFile Invoice.php
     */
    public function copyInvoice($source_id, $target_id, $copy_recurring_items_only = false): void
    {
        $this->load->model('invoices/mdl_items');
        $this->load->model('invoices/mdl_invoice_tax_rates');
        // Discounts calculation - since v1.6.3 Need if taxes applied after discounts
        $invoice = $this->getById($source_id);
        // This is the original invoice
        $global_discount = [
            'amount'  => $invoice->invoice_discount_amount,
            'percent' => $invoice->invoice_discount_percent,
            'item'    => 0.0,
            // Updated by ref (Need for invoice_item_subtotal calculation in Mdl_invoice_amounts)
            'items_subtotal' => $this->mdl_items->getItemsSubtotal($source_id),
        ];
        unset($invoice);
        // Free memory
        // Update the discounts - since v1.6.3
        $this->where('invoice_id', $target_id)->update('ip_invoices', ['invoice_discount_percent' => $global_discount['percent'], 'invoice_discount_amount' => $global_discount['amount']]);
        // Copy the items
        $invoice_items = $this->mdl_items->where('invoice_id', $source_id)->get()->result();
        foreach ($invoice_items as $invoice_item) {
            $db_array = ['invoice_id' => $target_id, 'item_tax_rate_id' => $invoice_item->item_tax_rate_id, 'item_product_id' => $invoice_item->item_product_id, 'item_task_id' => $invoice_item->item_task_id, 'item_name' => $invoice_item->item_name, 'item_description' => $invoice_item->item_description, 'item_quantity' => $invoice_item->item_quantity, 'item_price' => $invoice_item->item_price, 'item_discount_amount' => $invoice_item->item_discount_amount, 'item_order' => $invoice_item->item_order, 'item_is_recurring' => $invoice_item->item_is_recurring, 'item_product_unit' => $invoice_item->item_product_unit, 'item_product_unit_id' => $invoice_item->item_product_unit_id];
            if ( ! $copy_recurring_items_only || $invoice_item->item_is_recurring) {
                $this->mdl_items->save(null, $db_array, $global_discount);
            }
        }
        // Copy the tax rates
        $invoice_tax_rates = $this->mdl_invoice_tax_rates->where('invoice_id', $source_id)->get()->result();
        foreach ($invoice_tax_rates as $invoice_tax_rate) {
            $db_array = ['invoice_id' => $target_id, 'tax_rate_id' => $invoice_tax_rate->tax_rate_id, 'include_item_tax' => $invoice_tax_rate->include_item_tax, 'invoice_tax_rate_amount' => $invoice_tax_rate->invoice_tax_rate_amount];
            $this->mdl_invoice_tax_rates->save(null, $db_array);
        }
        // Copy the custom fields
        $this->load->model('custom_fields/mdl_invoice_custom');
        $custom_fields = $this->mdl_invoice_custom->where('invoice_id', $source_id)->get()->result();
        $form_data     = [];
        foreach ($custom_fields as $field) {
            $form_data[$field->invoice_custom_fieldid] = $field->invoice_custom_fieldvalue;
        }
        $this->mdl_invoice_custom->saveCustom($target_id, $form_data);
    }

    /**
     * @originalName copyCreditInvoice
     *
     * @originalFile Invoice.php
     */
    public function copyCreditInvoice($source_id, $target_id)
    {
        $this->load->model('invoices/mdl_items');
        $this->load->model('invoices/mdl_invoice_tax_rates');
        // Discounts calculation - since v1.6.3 Need if taxes applied after discounts
        $invoice = $this->getById($source_id);
        // This is the original invoice
        $global_discount = [
            'amount'  => $invoice->invoice_discount_amount,
            'percent' => $invoice->invoice_discount_percent,
            'item'    => 0.0,
            // Updated by ref (Need for invoice_item_subtotal calculation in Mdl_invoice_amounts)
            'items_subtotal' => $this->mdl_items->getItemsSubtotal($source_id),
        ];
        // Update the discounts - since v1.6.3
        $this->where('invoice_id', $target_id)->update('ip_invoices', ['invoice_discount_percent' => $global_discount['percent'], 'invoice_discount_amount' => $global_discount['amount']]);
        unset($invoice);
        // Free memory
        $invoice_items = $this->mdl_items->where('invoice_id', $source_id)->get()->result();
        foreach ($invoice_items as $invoice_item) {
            $db_array = ['invoice_id' => $target_id, 'item_tax_rate_id' => $invoice_item->item_tax_rate_id, 'item_product_id' => $invoice_item->item_product_id, 'item_task_id' => $invoice_item->item_task_id, 'item_name' => $invoice_item->item_name, 'item_description' => $invoice_item->item_description, 'item_quantity' => $invoice_item->item_quantity * -1, 'item_price' => $invoice_item->item_price, 'item_discount_amount' => $invoice_item->item_discount_amount, 'item_order' => $invoice_item->item_order, 'item_is_recurring' => $invoice_item->item_is_recurring, 'item_product_unit' => $invoice_item->item_product_unit, 'item_product_unit_id' => $invoice_item->item_product_unit_id];
            $this->mdl_items->save(null, $db_array, $global_discount);
        }
        $invoice_tax_rates = $this->mdl_invoice_tax_rates->where('invoice_id', $source_id)->get()->result();
        foreach ($invoice_tax_rates as $invoice_tax_rate) {
            $db_array = ['invoice_id' => $target_id, 'tax_rate_id' => $invoice_tax_rate->tax_rate_id, 'include_item_tax' => $invoice_tax_rate->include_item_tax, 'invoice_tax_rate_amount' => -$invoice_tax_rate->invoice_tax_rate_amount];
            $this->mdl_invoice_tax_rates->save(null, $db_array);
        }
        // Copy the custom fields
        $this->load->model('custom_fields/mdl_invoice_custom');
        $custom_fields = $this->mdl_invoice_custom->where('invoice_id', $source_id)->get()->result();
        $form_data     = [];
        foreach ($custom_fields as $field) {
            $form_data[$field->invoice_custom_fieldid] = $field->invoice_custom_fieldvalue;
        }
        $this->mdl_invoice_custom->saveCustom($target_id, $form_data);
    }

    /**
     * @originalName dbArray
     *
     * @originalFile Invoice.php
     */
    public function dbArray()
    {
        $db_array = parent::dbArray();
        // GetController the client id for the submitted invoice
        $this->load->model('clients/mdl_clients');
        // Check if is SUMEX
        $this->load->model('invoice_groups/mdl_invoice_groups');
        $db_array['invoice_date_created'] = date_to_mysql($db_array['invoice_date_created']);
        $db_array['invoice_date_due']     = $this->getDateDue($db_array['invoice_date_created']);
        $db_array['invoice_terms']        = get_setting('default_invoice_terms');
        if ( ! isset($db_array['invoice_status_id'])) {
            $db_array['invoice_status_id'] = 1;
        }
        $generate_invoice_number = get_setting('generate_invoice_number_for_draft');
        if ($db_array['invoice_status_id'] === 1 && $generate_invoice_number == 1) {
            $db_array['invoice_number'] = $this->getInvoiceNumber($db_array['invoice_group_id']);
        } elseif ($db_array['invoice_status_id'] != 1) {
            $db_array['invoice_number'] = $this->getInvoiceNumber($db_array['invoice_group_id']);
        } else {
            $db_array['invoice_number'] = '';
        }
        // Set default values
        $db_array['payment_method'] = empty($db_array['payment_method']) ? 0 : $db_array['payment_method'];
        // Generate the unique url key
        $db_array['invoice_url_key'] = $this->getUrlKey();

        return $db_array;
    }

    /**
     * @originalName getPayments
     *
     * @originalFile Invoice.php
     */
    public function getPayments($invoice)
    {
        $this->load->model('payments/mdl_payments');
        $this->db->where('invoice_id', $invoice->invoice_id);
        $payment_results   = $this->db->get('ip_payments');
        $invoice->payments = $payment_results->numRows() > 0 ? $payment_results->result() : null;

        return $invoice;
    }

    /**
     * @originalName getDateDue
     *
     * @originalFile Invoice.php
     */
    public function getDateDue($invoice_date_created)
    {
        $invoice_date_due = new DateTime($invoice_date_created);
        $invoice_date_due->add(new DateInterval('P' . get_setting('invoices_due_after') . 'D'));

        return $invoice_date_due->format('Y-m-d');
    }

    /**
     * @originalName getInvoiceNumber
     *
     * @originalFile Invoice.php
     */
    public function getInvoiceNumber($invoice_group_id)
    {
        $this->load->model('invoice_groups/mdl_invoice_groups');

        return $this->mdl_invoice_groups->generateInvoiceNumber($invoice_group_id);
    }

    /**
     * @originalName getUrlKey
     *
     * @originalFile Invoice.php
     */
    public function getUrlKey()
    {
        $this->load->helper('string');

        return random_string('alnum', 32);
    }

    /**
     * @originalName getInvoiceGroupId
     *
     * @originalFile Invoice.php
     */
    public function getInvoiceGroupId($invoice_id)
    {
        $invoice = $this->getById($invoice_id);

        return $invoice->invoice_group_id;
    }

    /**
     * @originalName getParentInvoiceNumber
     *
     * @originalFile Invoice.php
     */
    public function getParentInvoiceNumber($parent_invoice_id)
    {
        $parent_invoice = $this->getById($parent_invoice_id);

        return $parent_invoice->invoice_number;
    }

    /**
     * @originalName getCustomValues
     *
     * @originalFile Invoice.php
     */
    public function getCustomValues($id)
    {
        $this->load->module('custom_fields/Mdl_invoice_custom');

        return $this->invoice_custom->get_by_invid($id);
    }

    /**
     * @originalName getArchives
     *
     * @originalFile Invoice.php
     */
    public function getArchives($invoice_number): array
    {
        $invoice_array = [];
        if ( ! empty($invoice_number)) {
            $invoice_array = glob(UPLOADS_ARCHIVE_FOLDER . '*_*' . $invoice_number . '*.pdf');
        } else {
            foreach (glob(UPLOADS_ARCHIVE_FOLDER . '*.pdf') as $file) {
                $invoice_array[] = $file;
            }
            rsort($invoice_array);
        }

        return $invoice_array;
    }

    /**
     * @originalName delete
     *
     * @originalFile Invoice.php
     */
    public function delete($invoice_id)
    {
        parent::delete($invoice_id);
        $this->load->helper('orphan');
        delete_orphans();
    }

    // Excludes draft and paid invoices, i.e. keeps unpaid invoices.
    /**
     * @originalName isOpen
     *
     * @originalFile Invoice.php
     */
    public function isOpen()
    {
        $this->filter_where_in('invoice_status_id', [2, 3]);
        $this->filter_where('invoice_balance <> "0.00"');

        return $this;
    }

    // Used to check if the invoice is Modules\Core\Libraries\Sumex
    /**
     * @originalName isSumex
     *
     * @originalFile Invoice.php
     */
    public function isSumex()
    {
        $this->where('sumex_id is NOT NULL', null, false);

        return $this;
    }

    /**
     * @originalName guestVisible
     *
     * @originalFile Invoice.php
     */
    public function guestVisible()
    {
        $this->filter_where_in('invoice_status_id', [2, 3, 4]);

        return $this;
    }

    /**
     * @originalName isDraft
     *
     * @originalFile Invoice.php
     */
    public function isDraft()
    {
        $this->filter_where('invoice_status_id', 1);

        return $this;
    }

    /**
     * @originalName isSent
     *
     * @originalFile Invoice.php
     */
    public function isSent()
    {
        $this->filter_where('invoice_status_id', 2);

        return $this;
    }

    /**
     * @originalName isViewed
     *
     * @originalFile Invoice.php
     */
    public function isViewed()
    {
        $this->filter_where('invoice_status_id', 3);

        return $this;
    }

    /**
     * @originalName isPaid
     *
     * @originalFile Invoice.php
     */
    public function isPaid()
    {
        $this->filter_where('invoice_status_id', 4);
        $this->filter_or_where('invoice_balance', '0.00');

        return $this;
    }

    /**
     * @originalName isOverdue
     *
     * @originalFile Invoice.php
     */
    public function isOverdue()
    {
        $this->filter_having('is_overdue', 1);

        return $this;
    }

    /**
     * @originalName byClient
     *
     * @originalFile Invoice.php
     */
    public function byClient($client_id)
    {
        $this->filter_where('ip_invoices.client_id', $client_id);

        return $this;
    }

    /**
     * @originalName markViewed
     *
     * @originalFile Invoice.php
     */
    public function markViewed($invoice_id)
    {
        $invoice = $this->getById($invoice_id);
        if ( ! empty($invoice)) {
            $up = false;
            if ($invoice->invoice_status_id == 2) {
                $up = true;
                $this->db->set('invoice_status_id', 3);
            }
            // Set the invoice to read-only if feature is not disabled and setting is view
            if ($this->config->item('disable_read_only') == false && get_setting('read_only_toggle') == 3) {
                $up = true;
                $this->db->set('is_read_only', 1);
            }
            // Save?
            if ($up) {
                $this->db->where('invoice_id', $invoice_id);
                $this->db->update('ip_invoices');
            }
        }
    }

    /**
     * @originalName markSent
     *
     * @originalFile Invoice.php
     */
    public function markSent($invoice_id)
    {
        $invoice = $this->getById($invoice_id);
        if ( ! empty($invoice)) {
            $up = false;
            if ($invoice->invoice_status_id == 1) {
                // Set new due date and save
                $this->updateInvoiceDueDate($invoice_id);
                $up = true;
                $this->db->set('invoice_status_id', 2);
            }
            // Set the invoice to read-only if feature is not disabled and setting is sent
            if ($this->config->item('disable_read_only') == false && get_setting('read_only_toggle') == 2) {
                $up = true;
                $this->db->set('is_read_only', 1);
            }
            // Save?
            if ($up) {
                $this->db->where('invoice_id', $invoice_id);
                $this->db->update('ip_invoices');
            }
        }
    }

    /**
     * @originalName generateInvoiceNumberIfApplicable
     *
     * @originalFile Invoice.php
     */
    public function generateInvoiceNumberIfApplicable($invoice_id)
    {
        $invoice = $this->mdl_invoices->getById($invoice_id);
        // Generate new invoice number if applicable
        if ( ! empty($invoice) && ($invoice->invoice_status_id == 1 && $invoice->invoice_number == '') && get_setting('generate_invoice_number_for_draft') == 0) {
            $invoice_number = $this->getInvoiceNumber($invoice->invoice_group_id);
            // Set new invoice number and save
            $this->db->where('invoice_id', $invoice_id);
            $this->db->set('invoice_number', $invoice_number);
            $this->db->update('ip_invoices');
        }
    }

    /**
     * @originalName updateInvoiceDueDate
     *
     * @originalFile Invoice.php
     */
    public function updateInvoiceDueDate($invoice_id)
    {
        $invoice = $this->getById($invoice_id);
        if ( ! empty($invoice) && $invoice->is_read_only != 1 && get_setting('no_update_invoice_due_date_mail') == 0) {
            $current_date = date_to_mysql(date(date_format_setting()));
            $this->db->where('invoice_id', $invoice_id);
            $this->db->set('invoice_date_due', $this->getDateDue($current_date));
            $this->db->update('ip_invoices');
        }
    }
}
