<?php

namespace Modules\Invoices\Controllers;

use AllowDynamicProperties;
use Illuminate\Support\Facades\Log;
use Modules\Core\Controllers\AdminController;
use Modules\Invoices\Services\InvoicesService;
use Modules\Invoices\Services\InvoiceSumexService;
use Modules\Invoices\Services\InvoiceTaxRatesService;
use Modules\Invoices\Services\ItemsService;
use Modules\Invoices\Services\InvoiceAmountsService;
use Modules\Invoices\Services\InvoiceCustomService;
use Modules\Tasks\Services\TasksService;
use Modules\Units\Services\UnitsService;
use Modules\Clients\Services\ClientsService;
use Modules\Users\Services\UsersService;
use Modules\InvoiceGroups\Services\InvoiceGroupsService;
use Modules\TaxRates\Services\TaxRatesService;
use Modules\Invoices\Services\InvoicesRecurringService;

#[AllowDynamicProperties]
class AjaxController extends AdminController
{
    public $ajax_controller = true;
    public function __construct(
        public InvoicesService $invoicesService,
        public InvoiceSumexService $invoiceSumexService,
        public InvoiceTaxRatesService $invoiceTaxRatesService,
        public ItemsService $itemsService,
        public InvoiceAmountsService $invoiceAmountsService,
        public InvoiceCustomService $invoiceCustomService,
        public TasksService $tasksService,
        public UnitsService $unitsService,
        public ClientsService $clientsService,
        public UsersService $usersService,
        public InvoiceGroupsService $invoiceGroupsService,
        public TaxRatesService $taxRatesService,
        public InvoicesRecurringService $invoicesRecurringService
    ) {
        parent::__construct();
    }

    /**
     * @originalName save
     *
     * @originalFile AjaxController.php
     */
    public function save()
    {
        $invoice_id = $this->security->xss_clean($this->input->post('invoice_id', true));
        $this->invoicesService->setId($invoice_id);
        if ($this->invoicesService->runValidation('validation_rules_save_invoice')) {
            $items                    = json_decode($this->input->post('items'));
            $invoice_discount_percent = (float) $this->input->post('invoice_discount_percent');
            $invoice_discount_amount  = (float) $this->input->post('invoice_discount_amount');
            // Percent by default. Only one allowed. Prevent set 2 global discounts by geeky client - since v1.6.3
            if ($invoice_discount_percent && $invoice_discount_amount) {
                $invoice_discount_amount = 0.0;
            }
            // New discounts (for legacy_calculation false) - since v1.6.3 Need if taxes applied after discounts
            $items_subtotal = 0.0;
            if ($invoice_discount_amount) {
                foreach ($items as $item) {
                    if ( ! empty($item->item_name)) {
                        $items_subtotal += standardize_amount($item->item_quantity) * standardize_amount($item->item_price);
                    }
                }
            }
            // New discounts (for legacy_calculation false) - since v1.6.3 Need if taxes applied after discounts
            $global_discount = [
                'amount'  => $invoice_discount_amount ? standardize_amount($invoice_discount_amount) : 0.0,
                'percent' => $invoice_discount_percent ? standardize_amount($invoice_discount_percent) : 0.0,
                'item'    => 0.0,
                // Updated by ref (Need for invoice_item_subtotal calculation in Mdl_invoice_amounts)
                'items_subtotal' => $items_subtotal,
            ];
            // Automatic calculation mode
            if (get_setting('einvoicing')) {
                // Shift to false (by default). Need true? See Dev Note on ipconfig example
                $this->config->set_item('legacy_calculation', ! empty($this->input->post('legacy_calculation')));
            }
            foreach ($items as $item) {
                // Check if an item has either a quantity + price or name or description
                if ( ! empty($item->item_name)) {
                    // Standardize item data
                    $item->item_quantity        = $item->item_quantity ? standardize_amount($item->item_quantity) : 0.0;
                    $item->item_price           = $item->item_price ? standardize_amount($item->item_price) : 0.0;
                    $item->item_discount_amount = $item->item_discount_amount ? standardize_amount($item->item_discount_amount) : null;
                    $item->item_product_id      = $item->item_product_id ? $item->item_product_id : null;
                    $item->item_product_unit_id = $item->item_product_unit_id ? $item->item_product_unit_id : null;
                    $item->item_product_unit    = $this->unitsService->getName($item->item_product_unit_id, $item->item_quantity);
                    if (property_exists($item, 'item_date')) {
                        $item->item_date = $item->item_date ? date_to_mysql($item->item_date) : null;
                    }
                    $item_id = $item->item_id ?: null;
                    unset($item->item_id);
                    if ( ! $item->item_task_id) {
                        unset($item->item_task_id);
                    } else {
                        if (empty($this->mdl_tasks)) {
                            $this->load->model('tasks/mdl_tasks');
                        }
                        $this->tasksService->updateStatus(4, $item->item_task_id);
                    }
                    $this->itemsService->save($item_id, $item, $global_discount);
                } elseif (empty($item->item_name) && ( ! empty($item->item_quantity) || ! empty($item->item_price))) {
                    // Throw an error message and use the form validation for that (todo: where the translations of: The .* field is required.)
                    $this->load->library('form_validation');
                    $this->form_validation->set_rules('item_name', trans('item'), 'required');
                    $this->form_validation->run();
                    $response = ['success' => 0, 'validation_errors' => ['item_name' => form_error('item_name', '', '')]];
                    exit(json_encode($response));
                }
            }
            $invoice_status_id = $this->input->post('invoice_status_id');
            // Generate new invoice number if needed
            $invoice_number = $this->input->post('invoice_number');
            if (empty($invoice_number) && $invoice_status_id != 1) {
                $invoice_group_id = $this->invoicesService->getInvoiceGroupId($invoice_id);
                $invoice_number   = $this->invoicesService->getInvoiceNumber($invoice_group_id);
            }
            // Sometime global discount total value (round) need little adjust to be valid in ZugFerd2.3 standard
            if ( ! config_item('legacy_calculation') && $invoice_discount_amount && $invoice_discount_amount != $global_discount['item']) {
                // Adjust amount to reflect real calculation (cents)
                $invoice_discount_amount = $global_discount['item'];
            }
            $db_array = ['invoice_number' => $invoice_number, 'invoice_status_id' => $invoice_status_id, 'invoice_date_created' => date_to_mysql($this->input->post('invoice_date_created')), 'invoice_date_due' => date_to_mysql($this->input->post('invoice_date_due')), 'invoice_password' => $this->security->xss_clean($this->input->post('invoice_password')), 'invoice_terms' => $this->security->xss_clean($this->input->post('invoice_terms')), 'payment_method' => $this->security->xss_clean($this->input->post('payment_method')), 'invoice_discount_amount' => standardize_amount($invoice_discount_amount), 'invoice_discount_percent' => standardize_amount($invoice_discount_percent)];
            // check if status changed to sent, the feature is enabled and settings is set to sent
            if ($this->config->item('disable_read_only') === false && $invoice_status_id == get_setting('read_only_toggle')) {
                $db_array['is_read_only'] = 1;
            }
            $this->invoicesService->save($invoice_id, $db_array);
            $sumexInvoice = $this->invoicesService->where('sumex_invoice', $invoice_id)->get()->numRows();
            if ($sumexInvoice >= 1) {
                $sumex_array = ['sumex_invoice' => $invoice_id, 'sumex_reason' => $this->input->post('invoice_sumex_reason'), 'sumex_diagnosis' => $this->input->post('invoice_sumex_diagnosis'), 'sumex_treatmentstart' => date_to_mysql($this->input->post('invoice_sumex_treatmentstart')), 'sumex_treatmentend' => date_to_mysql($this->input->post('invoice_sumex_treatmentend')), 'sumex_casedate' => date_to_mysql($this->input->post('invoice_sumex_casedate')), 'sumex_casenumber' => $this->input->post('invoice_sumex_casenumber'), 'sumex_observations' => $this->input->post('invoice_sumex_observations')];
                $this->invoiceSumexService->save($invoice_id, $sumex_array);
            }
            if (config_item('legacy_calculation')) {
                // Recalculate for discounts
                $this->load->model('invoices/mdl_invoice_amounts');
                $this->invoiceAmountsService->calculate($invoice_id, $global_discount);
            }
            $response = ['success' => 1];
        } else {
            Log::error('980: I wasnt able to run the validation validation_rules_save_invoice');
            $this->load->helper('json_error');
            $response = ['success' => 0, 'validation_errors' => json_errors()];
        }
        // Save all custom fields
        if ($this->input->post('custom')) {
            $db_array = [];
            $values   = [];
            foreach ($this->input->post('custom') as $custom) {
                if (preg_match('/^(.*)\[\]$/i', $custom['name'], $matches)) {
                    $values[$matches[1]][] = $custom['value'];
                } else {
                    $values[$custom['name']] = $custom['value'];
                }
            }
            foreach ($values as $key => $value) {
                preg_match('/^custom\[(.*?)\](?:\[\]|)$/', $key, $matches);
                if ($matches) {
                    $db_array[$matches[1]] = $value;
                }
            }
            $this->load->model('custom_fields/mdl_invoice_custom');
            $result = $this->invoiceCustomService->saveCustom($invoice_id, $db_array);
            if ($result !== true) {
                $response = ['success' => 0, 'validation_errors' => $result];
                exit(json_encode($response));
            }
        }
        exit(json_encode($response));
    }

    /**
     * @originalName saveInvoiceTaxRate
     *
     * @originalFile AjaxController.php
     */
    public function saveInvoiceTaxRate()
    {
        if ($this->invoiceTaxRatesService->runValidation()) {
            // Only Legacy calculation have global taxes - since v1.6.3
            config_item('legacy_calculation') && $this->invoiceTaxRatesService->save();
            $response = ['success' => 1];
        } else {
            $response = ['success' => 0, 'validation_errors' => $this->invoiceTaxRatesService->validation_errors];
        }
        exit(json_encode($response));
    }

    /**
     * @originalName deleteItem
     *
     * @originalFile AjaxController.php
     */
    public function deleteItem($invoice_id)
    {
        $success = 0;
        $item_id = $this->security->xss_clean($this->input->post('item_id'));
        $this->load->model('mdl_invoices');
        // Only continue if the invoice exists or no item id was provided
        if ($this->invoicesService->getById($invoice_id) || empty($item_id)) {
            // Delete invoice item
            $this->load->model('mdl_items');
            $item = $this->itemsService->delete($item_id);
            // Check if deletion was successful
            if ($item) {
                $success = 1;
                // Mark task as complete from invoiced
                if (isset($item->item_task_id) && $item->item_task_id) {
                    $this->load->model('tasks/mdl_tasks');
                    $this->tasksService->updateStatus(3, $item->item_task_id);
                }
            }
        }
        // Return the response
        exit(json_encode(['success' => $success]));
    }

    /**
     * @originalName getItem
     *
     * @originalFile AjaxController.php
     */
    public function getItem()
    {
        $item = $this->itemsService->getById($this->security->xss_clean($this->input->post('item_id', true)));
        echo json_encode($item);
    }

    /**
     * @originalName modalCopyInvoice
     *
     * @originalFile AjaxController.php
     */
    public function modalCopyInvoice()
    {
        $data = [
            'invoice_groups' => $this->invoiceGroupsService->get()->result(),
            'tax_rates' => $this->taxRatesService->get()->result(),
            'invoice_id' => $this->input->post('invoice_id'),
            'invoice' => $this->invoicesService->where('ip_invoices.invoice_id', $this->input->post('invoice_id'))->get()->row(),
            'client' => $this->clientsService->getById($this->input->post('client_id')),
        ];
        return view('invoices.modal_copy_invoice', $data);
    }

    /**
     * @originalName copyInvoice
     *
     * @originalFile AjaxController.php
     */
    public function copyInvoice()
    {
        if ($this->invoicesService->runValidation()) {
            // Automatic calculation mode
            if (get_setting('einvoicing')) {
                // Shift to false (by default). Need true? See Dev Note on ipconfig example
                $this->config->set_item('legacy_calculation', ! empty($this->input->post('legacy_calculation')));
            }
            $target_id = $this->invoicesService->save();
            $source_id = $this->security->xss_clean($this->input->post('invoice_id'));
            $this->invoicesService->copyInvoice($source_id, $target_id);
            $response = ['success' => 1, 'invoice_id' => $target_id];
        } else {
            $this->load->helper('json_error');
            $response = ['success' => 0, 'validation_errors' => json_errors()];
        }
        exit(json_encode($response));
    }

    /**
     * @originalName modalChangeUser
     *
     * @originalFile AjaxController.php
     */
    public function modalChangeUser()
    {
        $data = [
            'user_id' => $this->input->post('user_id'),
            'invoice_id' => $this->input->post('invoice_id'),
            'users' => $this->usersService->getLatest(),
        ];
        return view('layout.ajax.modal_change_user_client', $data);
    }

    /**
     * @originalName changeUser
     *
     * @originalFile AjaxController.php
     */
    public function changeUser()
    {
        // GetController the user ID
        $user_id = $this->security->xss_clean($this->input->post('user_id'));
        $user    = $this->usersService->where('ip_users.user_id', $user_id)->get()->row();
        if ( ! empty($user)) {
            $invoice_id = $this->security->xss_clean($this->input->post('invoice_id'));
            $db_array   = ['user_id' => $user_id];
            $this->db->where('invoice_id', $invoice_id);
            $this->db->update('ip_invoices', $db_array);
            $response = ['success' => 1, 'invoice_id' => $this->security->xss_clean($invoice_id)];
        } else {
            $this->load->helper('json_error');
            $response = ['success' => 0, 'validation_errors' => json_errors()];
        }
        exit(json_encode($response));
    }

    /**
     * @originalName modalChangeClient
     *
     * @originalFile AjaxController.php
     */
    public function modalChangeClient()
    {
        $data = [
            'client_id' => $this->input->post('client_id'),
            'invoice_id' => $this->input->post('invoice_id'),
            'clients' => $this->clientsService->getLatest(),
        ];
        return view('layout.ajax.modal_change_user_client', $data);
    }

    /**
     * @originalName changeClient
     *
     * @originalFile AjaxController.php
     */
    public function changeClient()
    {
        // GetController the client ID
        $client_id = $this->security->xss_clean($this->input->post('client_id'));
        $client    = $this->clientsService->where('ip_clients.client_id', $client_id)->get()->row();
        if ( ! empty($client)) {
            $invoice_id = $this->security->xss_clean($this->input->post('invoice_id'));
            $db_array   = ['client_id' => $client_id];
            $this->db->where('invoice_id', $invoice_id);
            $this->db->update('ip_invoices', $db_array);
            $response = ['success' => 1, 'invoice_id' => $this->security->xss_clean($invoice_id)];
        } else {
            $this->load->helper('json_error');
            $response = ['success' => 0, 'validation_errors' => json_errors()];
        }
        exit(json_encode($response));
    }

    /**
     * @originalName modalCreateInvoice
     *
     * @originalFile AjaxController.php
     */
    public function modalCreateInvoice()
    {
        $data = [
            'invoice_groups' => $this->invoiceGroupsService->get()->result(),
            'tax_rates' => $this->taxRatesService->get()->result(),
            'client' => $this->clientsService->getById($this->input->post('client_id')),
            'clients' => $this->clientsService->getLatest(),
        ];
        return view('invoices.modal_create_invoice', $data);
    }

    /**
     * @originalName create
     *
     * @originalFile AjaxController.php
     */
    public function create()
    {
        if ($this->invoicesService->runValidation()) {
            $invoice_id = $this->invoicesService->create();
            $response   = ['success' => 1, 'invoice_id' => $invoice_id];
        } else {
            $this->load->helper('json_error');
            $response = ['success' => 0, 'validation_errors' => json_errors()];
        }
        exit(json_encode($response));
    }

    /**
     * @originalName createRecurring
     *
     * @originalFile AjaxController.php
     */
    public function createRecurring()
    {
        $this->load->model('invoices/mdl_invoices_recurring');
        if ((new InvoicesRecurringService())->runValidation()) {
            (new InvoicesRecurringService())->save();
            $response = ['success' => 1];
        } else {
            $this->load->helper('json_error');
            $response = ['success' => 0, 'validation_errors' => json_errors()];
        }
        exit(json_encode($response));
    }

    /**
     * @originalName modalCreateRecurring
     *
     * @originalFile AjaxController.php
     */
    public function modalCreateRecurring()
    {
        $data = [
            'invoice_id' => $this->input->post('invoice_id'),
            'recur_frequencies' => $this->invoicesRecurringService->recur_frequencies,
        ];
        return view('invoices.modal_create_recurring', $data);
    }

    /**
     * @originalName getRecurStartDate
     *
     * @originalFile AjaxController.php
     */
    public function getRecurStartDate()
    {
        $invoice_date    = $this->input->post('invoice_date');
        $recur_frequency = $this->input->post('recur_frequency');
        echo increment_user_date($invoice_date, $recur_frequency);
    }

    /**
     * @originalName modalCreateCredit
     *
     * @originalFile AjaxController.php
     */
    public function modalCreateCredit()
    {
        $data = [
            'invoice_groups' => $this->invoiceGroupsService->get()->result(),
            'tax_rates' => $this->taxRatesService->get()->result(),
            'invoice_id' => $this->input->post('invoice_id'),
            'invoice' => $this->invoicesService->where('ip_invoices.invoice_id', $this->input->post('invoice_id'))->get()->row(),
        ];
        return view('invoices.modal_create_credit', $data);
    }

    /**
     * @originalName createCredit
     *
     * @originalFile AjaxController.php
     */
    public function createCredit()
    {
        $this->load->model(['invoices/mdl_invoices', 'invoices/mdl_items', 'invoices/mdl_invoice_tax_rates']);
        if ((new InvoicesService())->runValidation()) {
            // Automatic calculation mode
            if (get_setting('einvoicing')) {
                // Shift to false (by default). Need true? See Dev Note on ipconfig example
                $this->config->set_item('legacy_calculation', ! empty($this->input->post('legacy_calculation')));
            }
            $target_id = (new InvoicesService())->save();
            $source_id = $this->security->xss_clean($this->input->post('invoice_id'));
            (new InvoicesService())->copyCreditInvoice($source_id, $target_id);
            // Set source invoice to read-only
            if ($this->config->item('disable_read_only') == false) {
                (new InvoicesService())->where('invoice_id', $source_id);
                (new InvoicesService())->update('ip_invoices', ['is_read_only' => '1']);
            }
            // Set target invoice to credit invoice
            (new InvoicesService())->where('invoice_id', $target_id);
            (new InvoicesService())->update('ip_invoices', ['creditinvoice_parent_id' => $source_id]);
            (new InvoicesService())->where('invoice_id', $target_id);
            (new InvoicesService())->update('ip_invoice_amounts', ['invoice_sign' => '-1']);
            $response = ['success' => 1, 'invoice_id' => $target_id];
        } else {
            $this->load->helper('json_error');
            $response = ['success' => 0, 'validation_errors' => json_errors()];
        }
        exit(json_encode($response));
    }
}
