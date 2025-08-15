<?php

namespace Modules\TaxRates\Controllers;

use Illuminate\Support\Facades\Log;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;

#[AllowDynamicProperties]
class TaxRatesController extends AdminController
{
    /**
     * Tax_Rates constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_tax_rates');
    }

    /**
     * @originalName index
     *
     * @originalFile TaxRatesController.php
     */
    public function index($page = 0)
    {
        (new TaxRatesService())->paginate(site_url('tax_rates/index'), $page);
        $tax_rates = (new TaxRatesService())->result();
        $this->layout->set('tax_rates', $tax_rates);
        $this->layout->buffer('content', 'tax_rates/index');
        $this->layout->render();
    }

    /**
     * @originalName form
     *
     * @originalFile TaxRatesController.php
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect()->route('tax_rates');
        }
        $this->filterInput();
        // <<<--- filters _POST array for nastiness
        if ((new TaxRatesService())->runValidation()) {
            (new TaxRatesService())->form_values['tax_rate_percent'] = standardize_amount((new TaxRatesService())->form_values['tax_rate_percent']);
            // We need to use the correct decimal point for sql IPT-310
            $db_array                     = (new TaxRatesService())->dbArray();
            $db_array['tax_rate_percent'] = standardize_amount($this->input->post('tax_rate_percent'));
            (new TaxRatesService())->save($id, $db_array);
            redirect()->route('tax_rates');
        }
        if ($id && ! $this->input->post('btn_submit') && ! (new TaxRatesService())->prepForm($id)) {
            show_404();
        }
        $this->layout->buffer('content', 'tax_rates/form');
        $this->layout->render();
    }

    /**
     * @originalName form
     *
     * @originalFile TaxRatesController.php
     */
    public function formStore()
    {
        if ($this->input->post('btn_cancel')) {
            redirect()->route('tax_rates');
        }
        $this->filterInput();
        // <<<--- filters _POST array for nastiness
        if ((new TaxRatesService())->runValidation()) {
            (new TaxRatesService())->form_values['tax_rate_percent'] = standardize_amount((new TaxRatesService())->form_values['tax_rate_percent']);
            // We need to use the correct decimal point for sql IPT-310
            $db_array                     = (new TaxRatesService())->dbArray();
            $db_array['tax_rate_percent'] = standardize_amount($this->input->post('tax_rate_percent'));
            (new TaxRatesService())->save($id, $db_array);
            redirect()->route('tax_rates');
        }
        if ($id && ! $this->input->post('btn_submit') && ! (new TaxRatesService())->prepForm($id)) {
            show_404();
        }
    }

    /**
     * @originalName delete
     *
     * @originalFile TaxRatesController.php
     */
    public function delete($id)
    {
        (new TaxRatesService())->delete($id);
        redirect()->route('tax_rates');
    }
}
