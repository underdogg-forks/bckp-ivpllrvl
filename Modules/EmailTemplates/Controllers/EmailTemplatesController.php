<?php

namespace Modules\EmailTemplates\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;

#[AllowDynamicProperties]
class EmailTemplatesController extends AdminController
{
    /**
     * Initialize the EmailTemplatesController.
     *
     * Delegates initialization to the parent AdminController.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @originalName index
     *
     * @originalFile EmailTemplatesController.php
     */
    public function index($page = 0)
    {
        (new EmailTemplatesService())->paginate(site_url('email_templates/index'), $page);
        $email_templates = (new EmailTemplatesService())->result();
        $this->layout->set('email_templates', $email_templates);
        $this->layout->buffer('content', 'email_templates/index');
        $this->layout->render();
    }

    /**
     * Display and process the email template creation/edit form.
     *
     * Processes form submissions (save, cancel), validates uniqueness of a new title,
     * runs service validation and save, prepares form data for editing, and assembles
     * custom fields and available PDF templates for the form view.
     *
     * @param int|null $id The ID of the email template to edit, or `null` to create a new template.
     * @return string The rendered form view content.
     * @note This method may redirect to the email templates list or form, or trigger a 404 response when an edit ID cannot be prepared.
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect()->route('email_templates');
        }
        $this->filterInput();
        // <<<--- filters _POST array for nastiness
        if ($this->input->post('is_update') == 0 && $this->input->post('email_template_title') != '') {
            $check = $this->db->get_where('ip_email_templates', ['email_template_title' => $this->input->post('email_template_title')])->result();
            if ( ! empty($check)) {
                $this->session->set_flashdata('alert_error', trans('email_template_already_exists'));
                redirect()->route('email_templates/form');
            }
        }
        if ((new EmailTemplatesService())->runValidation()) {
            (new EmailTemplatesService())->save($id);
            redirect()->route('email_templates');
        }
        if ($id && ! $this->input->post('btn_submit')) {
            if ( ! (new EmailTemplatesService())->prepForm($id)) {
                show_404();
            }
            (new EmailTemplatesService())->setFormValue('is_update', true);
        }
        foreach (array_keys((new CustomFieldsService())->customTables()) as $table) {
            $custom_fields[$table] = (new CustomFieldsService())->byTable($table)->get()->result();
        }

        return view('email_templates.form', ['custom_fields' => $custom_fields, 'invoice_templates' => (new TemplatesService())->getInvoiceTemplates(), 'quote_templates' => (new TemplatesService())->getQuoteTemplates(), 'selected_pdf_template' => (new EmailTemplatesService())->formValue('email_template_pdf_template')]);
    }

    /**
     * @originalName delete
     *
     * @originalFile EmailTemplatesController.php
     */
    public function delete($id)
    {
        (new EmailTemplatesService())->delete($id);
        redirect()->route('email_templates');
    }
}