<?php

namespace Modules\Invoices\Controllers;

use Illuminate\Support\Facades\DB;

use AllowDynamicProperties;
use Illuminate\Support\Facades\Log;
use Modules\Core\Controllers\AdminController;
use Modules\Crm\app\Services\ClientsService;
use Modules\Invoices\app\Services\InvoiceGroupsService;
use Modules\Invoices\Services\InvoiceAmountsService;
use Modules\Invoices\Services\InvoiceCustomService;
use Modules\Invoices\Services\InvoicesRecurringService;
use Modules\Invoices\Services\InvoicesService;
use Modules\Invoices\Services\InvoiceSumexService;
use Modules\Invoices\Services\InvoiceTaxRatesService;
use Modules\Invoices\Services\ItemsService;
use Modules\Projects\app\Services\TasksService;
use src\Services\TaxRatesService;
use src\Services\UnitsService;
use src\Services\UsersService;

#[AllowDynamicProperties]
class AjaxController extends AdminController
{
    public $ajax_controller = true;

    /**
     * Create the AjaxController with its required service dependencies and initialize the parent controller.
     *
     * @param InvoicesService          $invoicesService          handles invoice CRUD, validation, and related business logic
     * @param InvoiceSumexService      $invoiceSumexService      manages Sumex-specific invoice data and persistence
     * @param InvoiceTaxRatesService   $invoiceTaxRatesService   validates and saves invoice tax rate records
     * @param ItemsService             $itemsService             handles invoice item creation, updates, deletion, and retrieval
     * @param InvoiceAmountsService    $invoiceAmountsService    calculates and updates invoice totals and amounts
     * @param InvoiceCustomService     $invoiceCustomService     persists invoice custom field values
     * @param TasksService             $tasksService             manages task updates related to invoice items
     * @param UnitsService             $unitsService             provides unit lookups and normalization for invoice items
     * @param ClientsService           $clientsService           retrieves and manages client data for invoices
     * @param UsersService             $usersService             retrieves and manages user data for invoices
     * @param InvoiceGroupsService     $invoiceGroupsService     provides invoice group lookup and defaults
     * @param TaxRatesService          $taxRatesService          provides tax rate lookup and utilities
     * @param InvoicesRecurringService $invoicesRecurringService manages recurring invoice creation and schedules
     */
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
     * Save invoice and its related data and emit a JSON response.
     *
     * Validates posted invoice input, persists the invoice record and its items, applies global discounts,
     * updates task statuses, saves Sumex data when present, recalculates invoice amounts when required,
     * and saves custom invoice fields. Outputs a JSON response containing either success = 1 or success = 0
     * with validation errors.
     *
     * Side effects: modifies invoices, invoice items, related tasks, Sumex records, invoice amounts, and custom fields;
     * may adjust the legacy calculation configuration based on input.
     */
    public function save()
    {
        $invoice_id = e(request()->input('invoice_id', true));
        $this->invoicesService->setId($invoice_id);
        if ($this->invoicesService->runValidation('validation_rules_save_invoice')) {
            $items                    = json_decode(request()->input('items'));
            $invoice_discount_percent = (float) request()->input('invoice_discount_percent');
            $invoice_discount_amount  = (float) request()->input('invoice_discount_amount');
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
                config()->set('legacy_calculation', ! empty(request()->input('legacy_calculation')));
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
                        }
                        $this->tasksService->updateStatus(4, $item->item_task_id);
                    }
                    $this->itemsService->save($item_id, $item, $global_discount);
                } elseif (empty($item->item_name) && ( ! empty($item->item_quantity) || ! empty($item->item_price))) {
                    // Throw an error message and use the form validation for that (todo: where the translations of: The .* field is required.)
// TODO: Use Laravel services/facades - $this->load->library('form_validation');
                    // TODO: Move to Form Request - // TODO: Use Form Request - set_rules('item_name', trans('item'), 'required');
                    // TODO: Move to Form Request - // TODO: Use Form Request - run();
                    $response = ['success' => 0, 'validation_errors' => ['item_name' => form_error('item_name', '', '')]];
                    exit(json_encode($response));
                }
            }
            $invoice_status_id = request()->input('invoice_status_id');
            // Generate new invoice number if needed
            $invoice_number = request()->input('invoice_number');
            if (empty($invoice_number) && $invoice_status_id != 1) {
                $invoice_group_id = $this->invoicesService->getInvoiceGroupId($invoice_id);
                $invoice_number   = $this->invoicesService->getInvoiceNumber($invoice_group_id);
            }
            // Sometime global discount total value (round) need little adjust to be valid in ZugFerd2.3 standard
            if ( ! config_item('legacy_calculation') && $invoice_discount_amount && $invoice_discount_amount != $global_discount['item']) {
                // Adjust amount to reflect real calculation (cents)
                $invoice_discount_amount = $global_discount['item'];
            }
            $db_array = ['invoice_number' => $invoice_number, 'invoice_status_id' => $invoice_status_id, 'invoice_date_created' => date_to_mysql(request()->input('invoice_date_created')), 'invoice_date_due' => date_to_mysql(request()->input('invoice_date_due')), 'invoice_password' => e(request()->input('invoice_password')), 'invoice_terms' => e(request()->input('invoice_terms')), 'payment_method' => e(request()->input('payment_method')), 'invoice_discount_amount' => standardize_amount($invoice_discount_amount), 'invoice_discount_percent' => standardize_amount($invoice_discount_percent)];
            // check if status changed to sent, the feature is enabled and settings is set to sent
            if (config('disable_read_only') === false && $invoice_status_id == get_setting('read_only_toggle')) {
                $db_array['is_read_only'] = 1;
            }
            $this->invoicesService->save($invoice_id, $db_array);
            $sumexInvoice = $this->invoicesService->where('sumex_invoice', $invoice_id)->get()->numRows();
            if ($sumexInvoice >= 1) {
                $sumex_array = ['sumex_invoice' => $invoice_id, 'sumex_reason' => request()->input('invoice_sumex_reason'), 'sumex_diagnosis' => request()->input('invoice_sumex_diagnosis'), 'sumex_treatmentstart' => date_to_mysql(request()->input('invoice_sumex_treatmentstart')), 'sumex_treatmentend' => date_to_mysql(request()->input('invoice_sumex_treatmentend')), 'sumex_casedate' => date_to_mysql(request()->input('invoice_sumex_casedate')), 'sumex_casenumber' => request()->input('invoice_sumex_casenumber'), 'sumex_observations' => request()->input('invoice_sumex_observations')];
                $this->invoiceSumexService->save($invoice_id, $sumex_array);
            }
            if (config_item('legacy_calculation')) {
                // Recalculate for discounts
                $this->invoiceAmountsService->calculate($invoice_id, $global_discount);
            }
            $response = ['success' => 1];
        } else {
            Log::error('980: I wasnt able to run the validation validation_rules_save_invoice');
// TODO: Laravel autoloads helpers - $this->load->helper('json_error');
            $response = ['success' => 0, 'validation_errors' => json_errors()];
        }
        // Save all custom fields
        if (request()->input('custom')) {
            $db_array = [];
            $values   = [];
            foreach (request()->input('custom') as $custom) {
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
            $result = $this->invoiceCustomService->saveCustom($invoice_id, $db_array);
            if ($result !== true) {
                $response = ['success' => 0, 'validation_errors' => $result];
                exit(json_encode($response));
            }
        }
        exit(json_encode($response));
    }

    /**
     * Save invoice tax rates and output a JSON response indicating success or validation errors.
     *
     * Validates input via the InvoiceTaxRatesService; if validation passes, saves tax rates only when
     * the `legacy_calculation` configuration is enabled. Outputs a JSON object and terminates execution.
     *
     * Output JSON:
     * - `{"success":1}` on successful save (or when validation passes and save is skipped due to configuration).
     * - `{"success":0,"validation_errors": ...}` when validation fails.
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
     * Delete an item from an invoice and return a JSON success flag.
     *
     * Deletes the invoice item identified by the POST parameter `item_id` if the invoice with
     * the given $invoice_id exists (or when no item_id is provided). When deletion succeeds,
     * marks the linked task (if any) as completed (status 3). Sends a JSON response
     * containing `success` (1 on success, 0 on failure) and terminates execution.
     *
     * @param int|string $invoice_id invoice identifier used to verify the invoice exists
     */
    public function deleteItem($invoice_id)
    {
        $success = 0;
        $item_id = e(request()->input('item_id'));
        // Only continue if the invoice exists or no item id was provided
        if ($this->invoicesService->getById($invoice_id) || empty($item_id)) {
            // Delete invoice item
            $item = $this->itemsService->delete($item_id);
            // Check if deletion was successful
            if ($item) {
                $success = 1;
                // Mark task as complete from invoiced
                if (isset($item->item_task_id) && $item->item_task_id) {
                    $this->tasksService->updateStatus(3, $item->item_task_id);
                }
            }
        }
        // Return the response
        exit(json_encode(['success' => $success]));
    }

    /**
     * Fetches the invoice item identified by the POSTed `item_id` and outputs it as JSON.
     *
     * Reads `item_id` from POST input (sanitized), retrieves the corresponding item via the items service,
     * and echoes the item encoded as JSON.
     */
    public function getItem()
    {
        $item = $this->itemsService->getById(e(request()->input('item_id', true)));
        echo json_encode($item);
    }

    /**
     * Prepare data for and render the copy-invoice modal.
     *
     * Gathers invoice groups, tax rates, the specified invoice, and client based on POST inputs and returns the rendered modal view.
     *
     * @return string rendered HTML of the copy-invoice modal
     */
    public function modalCopyInvoice()
    {
        $data = [
            'invoice_groups' => $this->invoiceGroupsService->getAll(),
            'tax_rates'      => $this->taxRatesService->getAll(),
            'invoice_id'     => request()->input('invoice_id'),
            'invoice'        => $this->invoicesService->getById(request()->input('invoice_id')),
            'client'         => $this->clientsService->getById(request()->input('client_id')),
        ];

        return view('invoices.modal_copy_invoice', $data);
    }

    /**
     * Create a new invoice by copying an existing invoice and output a JSON response.
     *
     * Creates a new invoice from posted data, copies line items and related records from the specified source
     * invoice, and exits with a JSON object: on success {"success": 1, "invoice_id": newId}, on validation failure
     * {"success": 0, "validation_errors": ... }.
     *
     * If the "einvoicing" setting is enabled, may adjust the legacy_calculation configuration based on input.
     */
    public function copyInvoice()
    {
        if ($this->invoicesService->runValidation()) {
            // Automatic calculation mode
            if (get_setting('einvoicing')) {
                // Shift to false (by default). Need true? See Dev Note on ipconfig example
                config()->set('legacy_calculation', ! empty(request()->input('legacy_calculation')));
            }
            $target_id = $this->invoicesService->save();
            $source_id = e(request()->input('invoice_id'));
            $this->invoicesService->copyInvoice($source_id, $target_id);
            $response = ['success' => 1, 'invoice_id' => $target_id];
        } else {
// TODO: Laravel autoloads helpers - $this->load->helper('json_error');
            $response = ['success' => 0, 'validation_errors' => json_errors()];
        }
        exit(json_encode($response));
    }

    /**
     * Render the modal for changing the user associated with an invoice.
     *
     * @return string rendered HTML for the change-user modal
     */
    public function modalChangeUser()
    {
        $data = [
            'user_id'    => request()->input('user_id'),
            'invoice_id' => request()->input('invoice_id'),
            'users'      => $this->usersService->getLatest(),
        ];

        return view('layout.ajax.modal_change_user_client', $data);
    }

    /**
     * Change the user assigned to an invoice based on POSTed input.
     *
     * Reads `user_id` and `invoice_id` from POST, verifies the user exists, updates the invoice's `user_id`
     * in the database, and outputs a JSON response indicating success or validation errors.
     */
    public function changeUser()
    {
        // GetController the user ID
        $user_id = e(request()->input('user_id'));
        $user    = $this->usersService->getById($user_id);
        if ( ! empty($user)) {
            $invoice_id = e(request()->input('invoice_id'));
            $db_array   = ['user_id' => $user_id];
            DB::where('invoice_id', $invoice_id);
            DB::update('ip_invoices', $db_array);
            $response = ['success' => 1, 'invoice_id' => e($invoice_id)];
        } else {
// TODO: Laravel autoloads helpers - $this->load->helper('json_error');
            $response = ['success' => 0, 'validation_errors' => json_errors()];
        }
        exit(json_encode($response));
    }

    /**
     * Render the modal used to change the client for an invoice.
     *
     * Passes these variables to the view: `client_id`, `invoice_id`, and `clients` (a list of recent clients).
     *
     * @return string the rendered modal view HTML
     */
    public function modalChangeClient()
    {
        $data = [
            'client_id'  => request()->input('client_id'),
            'invoice_id' => request()->input('invoice_id'),
            'clients'    => $this->clientsService->getLatest(),
        ];

        return view('layout.ajax.modal_change_user_client', $data);
    }

    /**
     * Change the client associated with an invoice.
     *
     * If the POSTed `client_id` refers to an existing client, updates the invoice identified by POSTed
     * `invoice_id` to use that client and outputs JSON containing `success` and `invoice_id`. If the
     * client does not exist, outputs JSON containing `success` and `validation_errors`.
     */
    public function changeClient()
    {
        // GetController the client ID
        $client_id = e(request()->input('client_id'));
        $client    = $this->clientsService->getById($client_id);
        if ( ! empty($client)) {
            $invoice_id = e(request()->input('invoice_id'));
            $db_array   = ['client_id' => $client_id];
            DB::where('invoice_id', $invoice_id);
            DB::update('ip_invoices', $db_array);
            $response = ['success' => 1, 'invoice_id' => e($invoice_id)];
        } else {
// TODO: Laravel autoloads helpers - $this->load->helper('json_error');
            $response = ['success' => 0, 'validation_errors' => json_errors()];
        }
        exit(json_encode($response));
    }

    /**
     * Render the create-invoice modal populated with invoice groups, tax rates, the specified client, and recent clients.
     *
     * @return string the rendered modal HTML containing `invoice_groups`, `tax_rates`, `client`, and `clients` in the view context
     */
    public function modalCreateInvoice()
    {
        $data = [
            'invoice_groups' => $this->invoiceGroupsService->getAll(),
            'tax_rates'      => $this->taxRatesService->getAll(),
            'client'         => $this->clientsService->getById(request()->input('client_id')),
            'clients'        => $this->clientsService->getLatest(),
        ];

        return view('invoices.modal_create_invoice', $data);
    }

    /**
     * Create a new invoice from validated input and return a JSON result.
     *
     * On successful validation saves a new invoice and outputs {"success":1,"invoice_id":<id>}.
     * On validation failure outputs {"success":0,"validation_errors":<errors>}.
     *
     * The method sends the JSON response and terminates execution.
     */
    public function create()
    {
        if ($this->invoicesService->runValidation()) {
            $invoice_id = $this->invoicesService->create();
            $response   = ['success' => 1, 'invoice_id' => $invoice_id];
        } else {
// TODO: Laravel autoloads helpers - $this->load->helper('json_error');
            $response = ['success' => 0, 'validation_errors' => json_errors()];
        }
        exit(json_encode($response));
    }

    /**
     * Create or update a recurring invoice.
     *
     * Validates the request data and, if valid, saves the recurring invoice and outputs a JSON response.
     * The JSON response is {"success": 1} on success, or {"success": 0, "validation_errors": [...] } when validation fails.
     */
    public function createRecurring()
    {
        if ((new InvoicesRecurringService())->runValidation()) {
            (new InvoicesRecurringService())->save();
            $response = ['success' => 1];
        } else {
// TODO: Laravel autoloads helpers - $this->load->helper('json_error');
            $response = ['success' => 0, 'validation_errors' => json_errors()];
        }
        exit(json_encode($response));
    }

    /**
     * Prepare data and render the create-recurring-invoice modal.
     *
     * Loads the requested invoice ID and available recurrence frequencies and returns the rendered modal view.
     *
     * @return string rendered HTML of the create-recurring-invoice modal
     */
    public function modalCreateRecurring()
    {
        $data = [
            'invoice_id'        => request()->input('invoice_id'),
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
        $invoice_date    = request()->input('invoice_date');
        $recur_frequency = request()->input('recur_frequency');
        echo increment_user_date($invoice_date, $recur_frequency);
    }

    /**
     * Render the "create credit" modal populated with invoice groups, tax rates, and the source invoice.
     *
     * Provides the view with:
     * - `invoice_groups`: list of invoice groups,
     * - `tax_rates`: list of tax rates,
     * - `invoice_id`: ID from POST input,
     * - `invoice`: the source invoice row.
     *
     * @return string rendered HTML for the create-credit modal
     */
    public function modalCreateCredit()
    {
        $data = [
            'invoice_groups' => $this->invoiceGroupsService->getAll(),
            'tax_rates'      => $this->taxRatesService->getAll(),
            'invoice_id'     => request()->input('invoice_id'),
            'invoice'        => $this->invoicesService->getById(request()->input('invoice_id')),
        ];

        return view('invoices.modal_create_credit', $data);
    }

    /**
     * Create a credit invoice from an existing invoice.
     *
     * Validates input, creates a new invoice as a credit for the posted source invoice, and emits a JSON response.
     * On success the source invoice is optionally marked read-only (depending on configuration), the new invoice
     * is linked to the source via `creditinvoice_parent_id`, and the new invoice amount sign is set to negative.
     * If einvoicing is enabled, the method updates the legacy calculation mode from the posted `legacy_calculation` value.
     *
     * The method exits with a JSON object:
     * - On success: `{"success": 1, "invoice_id": <new_invoice_id>}`
     * - On validation failure: `{"success": 0, "validation_errors": ...}`
     */
    public function createCredit()
    {
        if ((new InvoicesService())->runValidation()) {
            // Automatic calculation mode
            if (get_setting('einvoicing')) {
                // Shift to false (by default). Need true? See Dev Note on ipconfig example
                config()->set('legacy_calculation', ! empty(request()->input('legacy_calculation')));
            }
            $target_id = (new InvoicesService())->save();
            $source_id = e(request()->input('invoice_id'));
            (new InvoicesService())->copyCreditInvoice($source_id, $target_id);
            // Set source invoice to read-only
            if (config('disable_read_only') == false) {
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
// TODO: Laravel autoloads helpers - $this->load->helper('json_error');
            $response = ['success' => 0, 'validation_errors' => json_errors()];
        }
        exit(json_encode($response));
    }
}
