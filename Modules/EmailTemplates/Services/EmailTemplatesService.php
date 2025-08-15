<?php

namespace Modules\EmailTemplates\Services;

use AllowDynamicProperties;
use App\Services\BaseService;

#[AllowDynamicProperties]
class EmailTemplatesService extends BaseService
{
    public $table = 'ip_email_templates';

    public $primary_key = 'ip_email_templates.email_template_id';

    /**
     * @originalName defaultSelect
     *
     * @originalFile EmailTemplate.php
     */
    public function defaultSelect()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', false);
    }

    /**
     * @originalName defaultOrderBy
     *
     * @originalFile EmailTemplate.php
     */
    public function defaultOrderBy()
    {
        $this->db->orderBy('email_template_title');
    }

    /**
     * @originalName validationRules
     *
     * @originalFile EmailTemplate.php
     */
    public function validationRules()
    {
        return ['email_template_title' => ['field' => 'email_template_title', 'label' => trans('title'), 'rules' => 'required'], 'email_template_type' => ['field' => 'email_template_pdf_quote_template', 'label' => trans('type')], 'email_template_subject' => ['field' => 'email_template_subject', 'label' => trans('subject')], 'email_template_from_name' => ['field' => 'email_template_from_name', 'label' => trans('from_name'), 'rules' => 'trim'], 'email_template_from_email' => ['field' => 'email_template_from_email', 'label' => trans('from_email')], 'email_template_cc' => ['field' => 'email_template_cc', 'label' => trans('cc')], 'email_template_bcc' => ['field' => 'email_template_bcc', 'label' => trans('bcc')], 'email_template_pdf_template' => ['field' => 'email_template_pdf_template', 'label' => trans('default_pdf_template')], 'email_template_body' => ['field' => 'email_template_body', 'label' => trans('body')]];
    }
}
