<?php

namespace Modules\Payments\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;
use Modules\PaymentMethods\Models\PaymentMethod;
use Modules\Payments\Services\PaymentLogsService;
use Illuminate\Http\Request;

#[AllowDynamicProperties]
class PaymentsController extends AdminController
{
    /**
     * PaymentsController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_payments');
    }

    /**
     * @originalName index
     *
     * @originalFile PaymentsController.php
     */
    public function index($page = 0)
    {
        (new PaymentsService())->paginate(site_url('payments/index'), $page);
        $payments = (new PaymentsService())->result();

        return view('payments.index', ['filter_display' => true, 'filter_placeholder' => trans('filter_payments'), 'filter_method' => 'filter_payments', 'payments' => $payments]);
    }

    /**
     * @originalName form
     *
     * @originalFile PaymentsController.php
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect()->route('payments');
        }
        $this->filterInput();
        // <<<--- filters _POST array for nastiness
        $this->load->model('custom_fields/mdl_payment_custom');
        if ((new PaymentsService())->runValidation()) {
            $id = (new PaymentsService())->save($id);
            (new PaymentCustomService())->saveCustom($id, $this->input->post('custom'));
            redirect()->route('payments');
        }
        if ( ! $this->input->post('btn_submit')) {
            $prep_form = (new PaymentsService())->prepForm($id);
            if ($id && ! $prep_form) {
                show_404();
            }
            $this->load->model('custom_values/mdl_custom_values');
            $payment_custom = (new PaymentCustomService())->where('payment_id', $id)->get();
            if ($payment_custom->numRows()) {
                $payment_custom = $payment_custom->row();
                unset($payment_custom->payment_id, $payment_custom->payment_custom_id);
                foreach ($payment_custom as $key => $val) {
                    (new PaymentsService())->setFormValue('custom[' . $key . ']', $val);
                }
            }
        } elseif ($this->input->post('custom')) {
            foreach ($this->input->post('custom') as $key => $val) {
                (new PaymentsService())->setFormValue('custom[' . $key . ']', $val);
            }
        }
        $this->load->helper('custom_values');
        $this->load->model(['invoices/mdl_invoices', 'payment_methods/mdl_payment_methods', 'custom_fields/mdl_custom_fields', 'custom_values/mdl_custom_values']);
        $open_invoices = (new InvoicesService())->isOpen()->get()->result();
        $custom_fields = (new CustomFieldsService())->byTable('ip_payment_custom')->get()->result();
        $custom_values = [];
        foreach ($custom_fields as $custom_field) {
            if (in_array($custom_field->custom_field_type, (new CustomValuesService())->customValueFields())) {
                $values                                        = (new CustomValuesService())->getByFid($custom_field->custom_field_id)->result();
                $custom_values[$custom_field->custom_field_id] = $values;
            }
        }
        $fields = (new PaymentCustomService())->getByPayid($id);
        foreach ($custom_fields as $cfield) {
            foreach ($fields as $fvalue) {
                if ($fvalue->payment_custom_fieldid == $cfield->custom_field_id) {
                    // TODO: Hackish, may need a better optimization
                    (new PaymentsService())->setFormValue('custom[' . $cfield->custom_field_id . ']', $fvalue->payment_custom_fieldvalue);
                    break;
                }
            }
        }
        $amounts                 = [];
        $invoice_payment_methods = [];
        foreach ($open_invoices as $open_invoice) {
            $amounts['invoice' . $open_invoice->invoice_id]                 = format_amount($open_invoice->invoice_balance);
            $invoice_payment_methods['invoice' . $open_invoice->invoice_id] = $open_invoice->payment_method;
        }

        return view('payments.online_logs', ['payment_id' => $id, 'payment_methods' => PaymentMethod::all(), 'open_invoices' => $open_invoices, 'custom_fields' => $custom_fields, 'custom_values' => $custom_values, 'amounts' => json_encode($amounts), 'invoice_payment_methods' => json_encode($invoice_payment_methods)]);
        if ($id) {
            $this->layout->set('payment', (new PaymentsService())->where('ip_payments.payment_id', $id)->get()->row());
        }
        $this->layout->buffer('content', 'payments/form');
        $this->layout->render();
    }

    /**
     * Display online payment logs with filtering.
     */
    public function onlineLogs(Request $request, int $page = 0): \Illuminate\View\View
    {
        $filters = $request->only(['search', 'date_from', 'date_to', 'status']);
        $service = app(PaymentLogsService::class);
        $service->paginate(route('payments.online_logs'), $page, $filters);
        $payment_logs = $service->result();

        return view('payments.online_logs', [
            'filter_display' => true,
            'filter_placeholder' => trans('filter_online_logs'),
            'filter_method' => 'filter_online_logs',
            'payment_logs' => $payment_logs,
            'filters' => $filters,
        ]);
    }

    /**
     * Delete a payment record.
     */
    public function delete(int $id): \Illuminate\Http\RedirectResponse
    {
        app(PaymentsService::class)->delete($id);
        return redirect()->route('payments');
    }
}
