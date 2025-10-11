<?php

namespace Modules\Invoices\Services;

use AllowDynamicProperties;
use Illuminate\Support\Str;
use Modules\Clients\Services\ClientsService;
use Modules\Core\Services\BaseService;
use Modules\CustomFields\Services\InvoiceCustomService;
use Modules\InvoiceGroups\Services\InvoiceGroupsService;
use Modules\Payments\Services\PaymentsService;

#[AllowDynamicProperties]
class InvoicesService extends BaseService
{
    public $table = 'ip_invoices';

    public $primary_key = 'ip_invoices.invoice_id';

    public $date_modified_field = 'invoice_date_modified';

    /**
     * Construct the InvoicesService with its required service dependencies and initialize the base service.
     *
     * @param InvoiceGroupsService $invoiceGroupsService Service for invoice group operations (e.g., generating invoice numbers).
     * @param ItemsService $itemsService Service for creating, copying, and managing invoice items.
     * @param InvoiceTaxRatesService $invoiceTaxRatesService Service for managing invoice tax rates.
     * @param InvoiceCustomService $invoiceCustomService Service for handling invoice custom field values.
     * @param ClientsService $clientsService Service for client-related operations.
     * @param PaymentsService $paymentsService Service for retrieving and managing payments.
     */
    public function __construct(
        public InvoiceGroupsService $invoiceGroupsService,
        public ItemsService $itemsService,
        public InvoiceTaxRatesService $invoiceTaxRatesService,
        public InvoiceCustomService $invoiceCustomService,
        public ClientsService $clientsService,
        public PaymentsService $paymentsService
    ) {
        parent::__construct();
    }

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
     * Create a new invoice and initialize related records (amounts, optional tax rate, and Sumex entry).
     *
     * If $include_invoice_tax_rates is true and a default invoice tax rate is configured, the default tax
     * rate row is created for the new invoice. If the invoice's group name contains "sumex" (case-insensitive),
     * a Sumex entry is created for the new invoice.
     *
     * @param array|null $db_array Associative array of invoice fields to save.
     * @param bool $include_invoice_tax_rates Whether to insert the configured default invoice tax rate for the new invoice.
     * @return int The ID of the newly created invoice.
     */
    public function create($db_array = null, $include_invoice_tax_rates = true)
    {
        $invoice_id    = parent::save(null, $db_array);
        $inv           = $this->where('ip_invoices.invoice_id', $invoice_id)->get()->row();
        $invoice_group = $inv->invoice_group_id;
        $db_array = ['invoice_id' => $invoice_id];
        $this->db->insert('ip_invoice_amounts', $db_array);
        if ($include_invoice_tax_rates && get_setting('default_invoice_tax_rate')) {
            $db_array = ['invoice_id' => $invoice_id, 'tax_rate_id' => get_setting('default_invoice_tax_rate'), 'include_item_tax' => get_setting('default_include_item_tax', 0), 'invoice_tax_rate_amount' => 0];
            $this->db->insert('ip_invoice_tax_rates', $db_array);
        }
        if ($invoice_group !== '0') {
            $invgroup = $this->invoiceGroupsService->where('invoice_group_id', $invoice_group)->get()->row();
            if (preg_match('/sumex/i', $invgroup->invoice_group_name)) {
                $db_array = ['sumex_invoice' => $invoice_id];
                $this->db->insert('ip_invoice_sumex', $db_array);
            }
        }
        return $invoice_id;
    }

    /**
     * Copy invoice data from a source invoice to a target invoice, including global discount, items, tax rates, and custom field values.
     *
     * The target invoice will be updated with the source invoice's global discount and receive copies of the source's items, invoice tax rates, and custom field values.
     *
     * @param int  $source_id                 ID of the invoice to copy from.
     * @param int  $target_id                 ID of the invoice to copy to.
     * @param bool $copy_recurring_items_only If true, only items marked as recurring will be copied; otherwise all items are copied.
     */
    public function copyInvoice($source_id, $target_id, $copy_recurring_items_only = false): void
    {
        $invoice = $this->getById($source_id);
        $global_discount = [
            'amount'  => $invoice->invoice_discount_amount,
            'percent' => $invoice->invoice_discount_percent,
            'item'    => 0.0,
            'items_subtotal' => $this->itemsService->getItemsSubtotal($source_id),
        ];
        unset($invoice);
        $this->where('invoice_id', $target_id)->update('ip_invoices', ['invoice_discount_percent' => $global_discount['percent'], 'invoice_discount_amount' => $global_discount['amount']]);
        $invoice_items = $this->itemsService->where('invoice_id', $source_id)->get()->result();
        foreach ($invoice_items as $invoice_item) {
            $db_array = [
                'invoice_id' => $target_id,
                'item_tax_rate_id' => $invoice_item->item_tax_rate_id,
                'item_product_id' => $invoice_item->item_product_id,
                'item_task_id' => $invoice_item->item_task_id,
                'item_name' => $invoice_item->item_name,
                'item_description' => $invoice_item->item_description,
                'item_quantity' => $invoice_item->item_quantity,
                'item_price' => $invoice_item->item_price,
                'item_discount_amount' => $invoice_item->item_discount_amount,
                'item_order' => $invoice_item->item_order,
                'item_is_recurring' => $invoice_item->item_is_recurring,
                'item_product_unit' => $invoice_item->item_product_unit,
                'item_product_unit_id' => $invoice_item->item_product_unit_id,
            ];
            if ( ! $copy_recurring_items_only || $invoice_item->item_is_recurring) {
                $this->itemsService->save(null, $db_array, $global_discount);
            }
        }
        $invoice_tax_rates = $this->invoiceTaxRatesService->where('invoice_id', $source_id)->get()->result();
        foreach ($invoice_tax_rates as $invoice_tax_rate) {
            $db_array = [
                'invoice_id' => $target_id,
                'tax_rate_id' => $invoice_tax_rate->tax_rate_id,
                'include_item_tax' => $invoice_tax_rate->include_item_tax,
                'invoice_tax_rate_amount' => $invoice_tax_rate->invoice_tax_rate_amount,
            ];
            $this->invoiceTaxRatesService->save(null, $db_array);
        }
        $custom_fields = $this->invoiceCustomService->where('invoice_id', $source_id)->get()->result();
        $form_data     = [];
        foreach ($custom_fields as $field) {
            $form_data[$field->invoice_custom_fieldid] = $field->invoice_custom_fieldvalue;
        }
        $this->invoiceCustomService->saveCustom($target_id, $form_data);
    }

    /**
     * Create a credit copy of an existing invoice into a target invoice.
     *
     * Copies the source invoice's global discount to the target, duplicates each item with the quantity negated,
     * duplicates each invoice tax rate with the tax amount negated, and copies custom field values to the target.
     *
     * @param int $source_id The invoice ID to copy from.
     * @param int $target_id The invoice ID to copy into (credit invoice).
     */
    public function copyCreditInvoice($source_id, $target_id)
    {
        $invoice = $this->getById($source_id);
        $global_discount = [
            'amount'  => $invoice->invoice_discount_amount,
            'percent' => $invoice->invoice_discount_percent,
            'item'    => 0.0,
            'items_subtotal' => $this->itemsService->getItemsSubtotal($source_id),
        ];
        $this->where('invoice_id', $target_id)->update('ip_invoices', ['invoice_discount_percent' => $global_discount['percent'], 'invoice_discount_amount' => $global_discount['amount']]);
        unset($invoice);
        $invoice_items = $this->itemsService->where('invoice_id', $source_id)->get()->result();
        foreach ($invoice_items as $invoice_item) {
            $db_array = [
                'invoice_id' => $target_id,
                'item_tax_rate_id' => $invoice_item->item_tax_rate_id,
                'item_product_id' => $invoice_item->item_product_id,
                'item_task_id' => $invoice_item->item_task_id,
                'item_name' => $invoice_item->item_name,
                'item_description' => $invoice_item->item_description,
                'item_quantity' => $invoice_item->item_quantity * -1,
                'item_price' => $invoice_item->item_price,
                'item_discount_amount' => $invoice_item->item_discount_amount,
                'item_order' => $invoice_item->item_order,
                'item_is_recurring' => $invoice_item->item_is_recurring,
                'item_product_unit' => $invoice_item->item_product_unit,
                'item_product_unit_id' => $invoice_item->item_product_unit_id,
            ];
            $this->itemsService->save(null, $db_array, $global_discount);
        }
        $invoice_tax_rates = $this->invoiceTaxRatesService->where('invoice_id', $source_id)->get()->result();
        foreach ($invoice_tax_rates as $invoice_tax_rate) {
            $db_array = [
                'invoice_id' => $target_id,
                'tax_rate_id' => $invoice_tax_rate->tax_rate_id,
                'include_item_tax' => $invoice_tax_rate->include_item_tax,
                'invoice_tax_rate_amount' => -$invoice_tax_rate->invoice_tax_rate_amount,
            ];
            $this->invoiceTaxRatesService->save(null, $db_array);
        }
        $custom_fields = $this->invoiceCustomService->where('invoice_id', $source_id)->get()->result();
        $form_data     = [];
        foreach ($custom_fields as $field) {
            $form_data[$field->invoice_custom_fieldid] = $field->invoice_custom_fieldvalue;
        }
        $this->invoiceCustomService->saveCustom($target_id, $form_data);
    }

    /**
     * Builds the database-ready array for an invoice.
     *
     * Prepares and normalizes invoice fields for persistence, including formatting
     * the creation date, computing the due date, applying default terms and status,
     * generating an invoice number when applicable, ensuring a payment method value,
     * and adding a URL key.
     *
     * @return array Associative array of invoice fields prepared for database storage. Contains at least:
     *              - `invoice_date_created` (MySQL-formatted date string)
     *              - `invoice_date_due` (due date as Y-m-d)
     *              - `invoice_terms`
     *              - `invoice_status_id`
     *              - `invoice_number`
     *              - `payment_method`
     *              - `invoice_url_key`
     */
    public function dbArray()
    {
        $db_array = parent::dbArray();
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
        $db_array['payment_method'] = empty($db_array['payment_method']) ? 0 : $db_array['payment_method'];
        $db_array['invoice_url_key'] = $this->getUrlKey();
        return $db_array;
    }

    /**
     * Attach payments matching the invoice's ID to the given invoice object.
     *
     * @param object $invoice Invoice object containing an `invoice_id` property.
     * @return object The same invoice object with a `payments` property set to an array of payment records when payments exist, or `null` when none are found.
     */
    public function getPayments($invoice)
    {
        $payments = Payment::query()->where('invoice_id', $invoice->invoice_id)->get();
        $invoice->payments = $payments->isNotEmpty() ? $payments : null;
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
     * Generate the next invoice number for the specified invoice group.
     *
     * @param int $invoice_group_id The ID of the invoice group to use when generating the number.
     * @return string The newly generated invoice number.
     */
    public function getInvoiceNumber($invoice_group_id)
    {
        return $this->invoiceGroupsService->generateInvoiceNumber($invoice_group_id);
    }

    /**
     * Generate a random 32-character URL key.
     *
     * @return string A 32-character random string suitable for use as a URL key.
     */
    public function getUrlKey()
    {
        return Str::random(32);
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
         * Retrieve custom field values for an invoice.
         *
         * @param int|string $id Invoice ID.
         * @return array The invoice's custom field values, keyed by custom field ID.
         */
    public function getCustomValues($id)
    {
        return $this->invoiceCustomService->get_by_invid($id);
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
     * Delete an invoice and remove any orphaned related records.
     *
     * @param int|string $invoice_id The ID of the invoice to delete.
     */
    public function delete($invoice_id)
    {
        parent::delete($invoice_id);
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
            $update_data = [];
            
            if ($invoice->invoice_status_id == 2) {
                $update_data['invoice_status_id'] = 3;
            }
            // Set the invoice to read-only if feature is not disabled and setting is view
            if ($this->config->item('disable_read_only') == false && get_setting('read_only_toggle') == 3) {
                $update_data['is_read_only'] = 1;
            }
            // Save?
            if (!empty($update_data)) {
                Invoice::query()->where('invoice_id', $invoice_id)->update($update_data);
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
            $update_data = [];
            
            if ($invoice->invoice_status_id == 1) {
                // Set new due date and save
                $this->updateInvoiceDueDate($invoice_id);
                $update_data['invoice_status_id'] = 2;
            }
            // Set the invoice to read-only if feature is not disabled and setting is sent
            if ($this->config->item('disable_read_only') == false && get_setting('read_only_toggle') == 2) {
                $update_data['is_read_only'] = 1;
            }
            // Save?
            if (!empty($update_data)) {
                Invoice::query()->where('invoice_id', $invoice_id)->update($update_data);
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
            Invoice::query()->where('invoice_id', $invoice_id)->update(['invoice_number' => $invoice_number]);
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
            Invoice::query()->where('invoice_id', $invoice_id)->update(['invoice_date_due' => $this->getDateDue($current_date)]);
        }
    }
}