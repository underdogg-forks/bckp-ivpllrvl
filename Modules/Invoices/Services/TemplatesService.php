<?php

namespace Modules\Invoices\Services;

use AllowDynamicProperties;
use Modules\Core\Services\BaseService;

#[AllowDynamicProperties]
class TemplatesService extends BaseService
{
    /**
     * @originalName getInvoiceTemplates
     *
     * @originalFile Template.php
     */
    public function getInvoiceTemplates($type = 'pdf')
    {
        if ($type == 'pdf') {
            $templates = array_map('basename', glob(resource_path('views/invoice_templates/pdf/*')));
        } elseif ($type == 'public') {
            $templates = array_map('basename', glob(resource_path('views/invoice_templates/public/*')));
        } else {
            $templates = [];
        }
        return $this->removeExtension($templates);
    }

    /**
     * @originalName getQuoteTemplates
     *
     * @originalFile Template.php
     */
    public function getQuoteTemplates($type = 'pdf')
    {
        if ($type == 'pdf') {
            $templates = array_map('basename', glob(resource_path('views/quote_templates/pdf/*')));
        } elseif ($type == 'public') {
            $templates = array_map('basename', glob(resource_path('views/quote_templates/public/*')));
        } else {
            $templates = [];
        }
        return $this->removeExtension($templates);
    }

    /**
     * @originalName removeExtension
     *
     * @originalFile Template.php
     */
    private function removeExtension(array $files): array
    {
        foreach ($files as $key => $file) {
            $files[$key] = str_replace('.php', '', $file);
        }

        return $files;
    }
}
