<?php

namespace Modules\Filter\Controllers;

use Illuminate\Contracts\View\View;

use AllowDynamicProperties;
use Illuminate\Http\Request;
use Modules\Clients\Models\Client;
use Modules\Core\Controllers\AdminController;
use Modules\Invoices\Models\Invoice;
use Modules\Quotes\Models\Quote;

#[AllowDynamicProperties]
class AjaxController extends AdminController
{
    public $ajax_controller = true;

    /**
     * @originalName filterInvoices
     *
     * @originalFile AjaxController.php
     */
    public function filterInvoices(Request $request): \Illuminate\Contracts\View\View
    {
        $query    = $request->input('filter_query');
        $keywords = array_filter(explode(' ', $query));
        $invoices = Invoice::query();
        foreach ($keywords as $keyword) {
            $invoices->where(function ($q) use ($keyword) {
                $q->whereRaw('LOWER(invoice_number) LIKE ?', ["%{$keyword}%"])
                    ->orWhereRaw('LOWER(client_title) LIKE ?', ["%{$keyword}%"]);
            });
        }
        $data = [
            'invoices' => $invoices->get(),
        ];

        return view('settings.index', $data); // Use an existing view for demonstration
    }

    /**
     * @originalName filterQuotes
     *
     * @originalFile AjaxController.php
     */
    public function filterQuotes(Request $request): \Illuminate\Contracts\View\View
    {
        $query    = $request->input('filter_query');
        $keywords = array_filter(explode(' ', $query));
        $quotes   = Quote::query();
        foreach ($keywords as $keyword) {
            $quotes->where(function ($q) use ($keyword) {
                $q->whereRaw('LOWER(quote_number) LIKE ?', ["%{$keyword}%"])
                    ->orWhereRaw('LOWER(client_title) LIKE ?', ["%{$keyword}%"]);
            });
        }
        $data = [
            'quotes' => $quotes->get(),
        ];

        return view('settings.index', $data); // Use an existing view for demonstration
    }

    /**
     * @originalName filterClients
     *
     * @originalFile AjaxController.php
     */
    public function filterClients(Request $request): \Illuminate\Contracts\View\View
    {
        $query    = $request->input('filter_query');
        $keywords = array_filter(explode(' ', $query));
        $clients  = Client::query();
        foreach ($keywords as $keyword) {
            $clients->where(function ($q) use ($keyword) {
                $q->whereRaw('LOWER(client_name) LIKE ?', ["%{$keyword}%"])
                    ->orWhereRaw('LOWER(client_email) LIKE ?', ["%{$keyword}%"]);
            });
        }
        $data = [
            'clients' => $clients->get(),
        ];

        return view('settings.index', $data); // Use an existing view for demonstration
    }

    /**
     * @originalName filterCustomFields
     *
     * @originalFile AjaxController.php
     */
    public function filterCustomFields(Request $request): \Illuminate\Contracts\View\View
    {
        $name     = $request->headers->get('referer') ? basename($request->headers->get('referer')) : 'all';
        $query    = $request->input('filter_query');
        $keywords = explode(' ', $query);
        $service  = new CustomFieldsService();
        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = mb_strtolower($keyword);
                $service->like("CONCAT_WS('^',custom_field_id, LOWER(custom_field_table), LOWER(custom_field_label), LOWER(custom_field_type))", $keyword);
            }
        }
        $custom_tables = $service->customTables();
        if ($name !== 'all' && in_array($name, $custom_tables)) {
            $service->byTableName($name);
        }
        $custom_fields       = $service->get()->result();
        $custom_value_fields = (new CustomValuesService())->customValueFields();
        $positions           = $service->getPositions(true);
        $data                = [
            'custom_fields'       => $custom_fields,
            'custom_tables'       => $custom_tables,
            'custom_value_fields' => $custom_value_fields,
            'positions'           => $positions,
        ];

        return view('custom_fields.partial_custom_field_table', $data);
    }

    /**
     * @originalName filterCustomValues
     *
     * @originalFile AjaxController.php
     */
    public function filterCustomValues(Request $request): \Illuminate\Contracts\View\View
    {
        // custom values id Normaly always here (it's ajax). Old school but work.
        $id = empty($_SERVER['HTTP_REFERER']) ? 0 : basename($_SERVER['HTTP_REFERER']);
        // Todo: With CI?
        $query    = $request->input('filter_query');
        $keywords = explode(' ', $query);
        $service  = new CustomValuesService();
        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = mb_strtolower($keyword);
                $service->like("CONCAT_WS('^',LOWER(custom_values_value), LOWER(custom_field_table), LOWER(custom_field_label), LOWER(custom_field_type))", $keyword);
            }
        }
        $service->grouped();
        $custom_values = $service->get()->result();
        $data          = [
            'id'            => $id,
            'custom_values' => $custom_values,
            'custom_tables' => (new CustomFieldsService())->customTables(),
            'positions'     => (new CustomFieldsService())->getPositions(true),
        ];

        return view('custom_values.partial_custom_values_table', $data);
    }

    /**
     * @originalName filterCustomValuesField
     *
     * @originalFile AjaxController.php
     */
    public function filterCustomValuesField(Request $request): \Illuminate\Contracts\View\View
    {
        // custom values id Normaly always here (it's ajax). Old school but work.
        $id       = empty($_SERVER['HTTP_REFERER']) ? 0 : basename($_SERVER['HTTP_REFERER']);
        $query    = $request->input('filter_query');
        $keywords = explode(' ', $query);
        $service  = new CustomValuesService();
        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = mb_strtolower($keyword);
                $service->like("CONCAT_WS('^',custom_values_id,LOWER(custom_values_value))", $keyword);
            }
        }
        $elements = $service->getByFid($id)->result();
        $data     = [
            'id'       => $id,
            'elements' => $elements,
        ];

        return view('custom_values.partial_custom_values_field', $data);
    }

    /**
     * @originalName filterProjects
     *
     * @originalFile AjaxController.php
     */
    public function filterProjects(Request $request): \Illuminate\Contracts\View\View
    {
        $query    = $request->input('filter_query');
        $keywords = explode(' ', $query);
        // client_id : Column 'client_id' in where clause is ambiguous (ip_clients.client_id or ip_project.client_id
        // Not showed in frontend table
        // project_id,client_id,
        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = mb_strtolower($keyword);
                (new ProjectsService())->like("CONCAT_WS('^',LOWER(client_title),LOWER(client_name),LOWER(client_surname),LOWER(project_name))", $keyword);
            }
        }
        $data = ['projects' => (new ProjectsService())->get()->result()];
        echo view('projects/partial_projects_table', $data)->render();
    }

    /**
     * @originalName filterTasks
     *
     * @originalFile AjaxController.php
     */
    public function filterTasks(Request $request): \Illuminate\Contracts\View\View
    {
        $query    = $request->input('filter_query');
        $keywords = explode(' ', $query);
        // Column 'project_id' in where clause is ambiguous
        // Not showed in frontend table:
        // task_id,ip_tasks.project_id,LOWER(task_description),
        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = mb_strtolower($keyword);
                (new TasksService())->like("CONCAT_WS('^',LOWER(task_name),LOWER(project_name),LOWER(task_price),task_finish_date,LOWER(task_status),LOWER(tax_rate_id))", $keyword);
            }
        }
        $data = ['tasks' => (new TasksService())->get()->result(), 'task_statuses' => (new TasksService())->statuses()];
        echo view('tasks/partial_tasks_table', $data)->render();
    }

    /**
     * @originalName filterProducts
     *
     * @originalFile AjaxController.php
     */
    public function filterProducts(Request $request): \Illuminate\Contracts\View\View
    {
        $query    = $request->input('filter_query');
        $keywords = explode(' ', $query);
        // Columns 'tax_rate_id' & 'unit_id' in where clause is ambiguous
        // Not showed in frontend table:
        // product_id,LOWER(family_name),purchase_price,LOWER(provider_name),LOWER(tax_rate_name),LOWER(unit_name_plrl),
        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = mb_strtolower($keyword);
                (new ProductsService())->like("CONCAT_WS('^',product_sku,LOWER(family_name),LOWER(product_name),LOWER(product_description),product_price,product_tariff)", $keyword);
            }
        }
        $data = ['products' => (new ProductsService())->get()->result()];
        echo view('products/partial_products_table', $data)->render();
    }

    /**
     * @originalName filterUsers
     *
     * @originalFile AjaxController.php
     */
    public function filterUsers(Request $request): \Illuminate\Contracts\View\View
    {
        $query    = $request->input('filter_query');
        $keywords = explode(' ', $query);
        // Not used: user_id    user_type   user_active user_date_modified  user_language   user_password   user_psalt  user_passwordreset_token
        // Not showed in frontend table:
        // user_date_created,LOWER(user_company),LOWER(user_address_1),LOWER(user_address_2),LOWER(user_city),LOWER(user_state),LOWER(user_zip),LOWER(user_country),
        // LOWER(user_invoicing_contact),LOWER(user_phone),LOWER(user_fax),LOWER(user_mobile),LOWER(user_web),
        // LOWER(user_vat_id),LOWER(user_tax_code),LOWER(user_all_clients),LOWER(user_subscribernumber),LOWER(user_bank),LOWER(user_iban),LOWER(user_bic),LOWER(user_remittance_text),LOWER(user_gln),LOWER(user_rcc)
        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = mb_strtolower($keyword);
                (new UsersService())->like("CONCAT_WS('^', LOWER(user_name), LOWER(user_email))", $keyword);
            }
        }
        $data = ['users' => (new UsersService())->get()->result(), 'user_types' => (new UsersService())->userTypes()];
        echo view('users/partial_users_table', $data)->render();
    }

    /**
     * @originalName filterFamilies
     *
     * @originalFile AjaxController.php
     */
    public function filterFamilies(Request $request): \Illuminate\Contracts\View\View
    {
        $query    = $request->input('filter_query');
        $keywords = explode(' ', $query);
        // Not showed in frontend table:
        // family_id,
        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = mb_strtolower($keyword);
                (new FamiliesService())->like("CONCAT_WS('^',LOWER(family_name))", $keyword);
            }
        }
        $data = ['families' => (new FamiliesService())->get()->result()];
        echo view('families/partial_families_table', $data)->render();
    }

    /**
     * @originalName filterInvoicesRecuring
     *
     * @originalFile AjaxController.php
     */
    public function filterInvoicesRecuring(Request $request): \Illuminate\Contracts\View\View
    {
        $query    = $request->input('filter_query');
        $keywords = explode(' ', $query);
        // invoice_recurring_id invoice_id
        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = mb_strtolower($keyword);
                (new InvoicesRecurringService())->like("CONCAT_WS('^',recur_start_date,recur_end_date,recur_next_date,recur_frequency,LOWER(invoice_number),LOWER(client_title),LOWER(client_name),LOWER(client_surname))", $keyword);
            }
        }
        $data = ['recur_frequencies' => (new InvoicesRecurringService())->recur_frequencies, 'recurring_invoices' => (new InvoicesRecurringService())->get()->result()];
        echo view('invoices/partial_invoices_recurring_table', $data)->render();
    }

    /**
     * @originalName filterOnlineLogs
     *
     * @originalFile AjaxController.php
     */
    public function filterOnlineLogs(Request $request): \Illuminate\Contracts\View\View
    {
        $query    = $request->input('filter_query');
        $keywords = explode(' ', $query);
        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = mb_strtolower($keyword);
                (new PaymentLogsService())->like("CONCAT_WS('^',merchant_response_id,LOWER(invoice_number),merchant_response_successful,merchant_response_date,LOWER(merchant_response_driver),LOWER(merchant_response),LOWER(merchant_response_reference))", $keyword);
            }
        }
        $data = ['payment_logs' => (new PaymentLogsService())->get()->result()];
        echo view('payments/partial_online_logs_table', $data)->render();
    }

    /**
     * @originalName filterArchives
     *
     * @originalFile AjaxController.php
     */
    public function filterArchives(Request $request): \Illuminate\Contracts\View\View
    {
        $data = ['invoices_archive' => (new InvoicesService())->getArchives($request->input('filter_query'))];
        echo view('invoices/partial_invoice_archive', $data)->render();
    }

    /**
     * @originalName filterPayments
     *
     * @originalFile AjaxController.php
     */
    public function filterPayments(Request $request): \Illuminate\Contracts\View\View
    {
        $query    = $request->input('filter_query');
        $keywords = explode(' ', $query);
        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = mb_strtolower($keyword);
                (new PaymentsService())->like("CONCAT_WS('^',payment_date,LOWER(invoice_number),LOWER(client_title),LOWER(client_name),LOWER(client_surname),payment_amount,LOWER(payment_method_name),LOWER(payment_note))", $keyword);
            }
        }
        $data = ['payments' => (new PaymentsService())->get()->result()];
        echo view('payments/partial_payments_table', $data)->render();
    }
}
