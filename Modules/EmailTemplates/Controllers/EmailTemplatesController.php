<?php

namespace Modules\EmailTemplates\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;

#[AllowDynamicProperties]
class EmailTemplatesController extends AdminController
{
    /**
     * Email_Templates constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_email_templates');
    }

    /**
     * @originalName index
     *
     * @originalFile EmailTemplatesController.php
     */
    public function index($page = 0)
    {
        $this->mdl_email_templates->paginate(site_url('email_templates/index'), $page);
        $email_templates = $this->mdl_email_templates->result();
        $this->layout->set('email_templates', $email_templates);
        $this->layout->buffer('content', 'email_templates/index');
        $this->layout->render();
    }

    /**
     * @originalName form
     *
     * @originalFile EmailTemplatesController.php
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('email_templates');
        }
        $this->filterInput();
        // <<<--- filters _POST array for nastiness
        if ($this->input->post('is_update') == 0 && $this->input->post('email_template_title') != '') {
            $check = $this->db->get_where('ip_email_templates', ['email_template_title' => $this->input->post('email_template_title')])->result();
            if ( ! empty($check)) {
                $this->session->set_flashdata('alert_error', trans('email_template_already_exists'));
                redirect('email_templates/form');
            }
        }
        if ($this->mdl_email_templates->runValidation()) {
            $this->mdl_email_templates->save($id);
            redirect('email_templates');
        }
        if ($id && ! $this->input->post('btn_submit')) {
            if ( ! $this->mdl_email_templates->prepForm($id)) {
                show_404();
            }
            $this->mdl_email_templates->setFormValue('is_update', true);
        }
        $this->load->model(['custom_fields/mdl_custom_fields', 'invoices/mdl_templates']);
        foreach (array_keys($this->mdl_custom_fields->customTables()) as $table) {
            $custom_fields[$table] = $this->mdl_custom_fields->byTable($table)->get()->result();
        }
        $this->layout->set(['custom_fields' => $custom_fields, 'invoice_templates' => $this->mdl_templates->getInvoiceTemplates(), 'quote_templates' => $this->mdl_templates->getQuoteTemplates(), 'selected_pdf_template' => $this->mdl_email_templates->formValue('email_template_pdf_template')]);
        $this->layout->buffer('content', 'email_templates/form');
        $this->layout->render();
    }

    /**
     * @originalName delete
     *
     * @originalFile EmailTemplatesController.php
     */
    public function delete($id)
    {
        $this->mdl_email_templates->delete($id);
        redirect('email_templates');
    }
}
