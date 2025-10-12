<?php

namespace Modules\EmailTemplates\Controllers;

use Illuminate\Contracts\View\View;

use Illuminate\Http\Request;
use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;

#[AllowDynamicProperties]
class EmailTemplatesController extends AdminController
{
    /**
     * Set up the EmailTemplatesController.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display a paginated list of email templates and render the index view.
     *
     * Paginates email templates for the given page, stores the resulting collection
     * in the layout under the key `email_templates`, buffers the `email_templates/index`
     * view as the content, and renders the layout.
     *
     * @param int $page page number to display (defaults to 0)
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
     * Display and handle the email template create/edit form.
     *
     * Handles cancel actions, validates uniqueness for new template titles, runs validation and saves
     * the template, prepares form values for editing, and collects custom fields and available PDF
     * templates for the view. May redirect to the email templates list or back to the form, or trigger
     * a 404 response when an edit ID cannot be prepared.
     *
     * @param int|null $id the ID of the email template to edit, or null to create a new template
     *
     * @return string the rendered form view HTML
     */
    public function form(Request $request, $id = null) {
        if ($request->post('btn_cancel')) {
            redirect()->route('email_templates');
        }
        $this->filterInput();
        // <<<--- filters _POST array for nastiness
        if ($request->post('is_update') == 0 && $request->post('email_template_title') != '') {
            $check = $this->db->get_where('ip_email_templates', ['email_template_title' => $request->post('email_template_title')])->result();
            if ( ! empty($check)) {
                $this->session->set_flashdata('alert_error', trans('email_template_already_exists'));
                redirect()->route('email_templates/form');
            }
        }
        if ((new EmailTemplatesService())->runValidation()) {
            (new EmailTemplatesService())->save($id);
            redirect()->route('email_templates');
        }
        if ($id && ! $request->post('btn_submit')) {
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
     * Delete an email template and redirect to the email templates list.
     *
     * @param int|string $id the identifier of the email template to remove
     *
     * @return void
     */
    public function delete($id)
    {
        (new EmailTemplatesService())->delete($id);
        redirect()->route('email_templates');
    }
}
