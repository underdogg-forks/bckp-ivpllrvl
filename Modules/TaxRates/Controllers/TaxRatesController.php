<?php

namespace Modules\TaxRates\Controllers;

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
        $this->mdl_tax_rates->paginate(site_url('tax_rates/index'), $page);
        $tax_rates = $this->mdl_tax_rates->result();
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
            redirect('tax_rates');
        }
        $this->filterInput();
        // <<<--- filters _POST array for nastiness
        if ($this->mdl_tax_rates->runValidation()) {
            $this->mdl_tax_rates->form_values['tax_rate_percent'] = standardize_amount($this->mdl_tax_rates->form_values['tax_rate_percent']);
            // We need to use the correct decimal point for sql IPT-310
            $db_array                     = $this->mdl_tax_rates->dbArray();
            $db_array['tax_rate_percent'] = standardize_amount($this->input->post('tax_rate_percent'));
            $this->mdl_tax_rates->save($id, $db_array);
            redirect('tax_rates');
        }
        if ($id && ! $this->input->post('btn_submit') && ! $this->mdl_tax_rates->prepForm($id)) {
            show_404();
        }
        $this->layout->buffer('content', 'tax_rates/form');
        $this->layout->render();
    }

    /**
     * @originalName delete
     *
     * @originalFile TaxRatesController.php
     */
    public function delete($id)
    {
        $this->mdl_tax_rates->delete($id);
        redirect('tax_rates');
    }
}
