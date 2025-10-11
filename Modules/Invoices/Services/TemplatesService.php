<?php

namespace Modules\Invoices\Services;

use AllowDynamicProperties;
use Modules\Core\Services\BaseService;

#[AllowDynamicProperties]
class TemplatesService extends BaseService
{
    /**
     * Retrieve available invoice template names for the given template type.
     *
     * @param string $type Template category to load; supported values are `'pdf'` and `'public'`.
     * @return string[] Array of template basenames with the `.php` extension removed.
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
     * Retrieve available quote template names for the specified template type.
     *
     * @param string $type The template type to list: 'pdf' for PDF templates or 'public' for public templates.
     * @return string[] An array of template filenames with the '.php' extension removed.
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

    /**
     * Get all templates.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return \Modules\Invoices\Models\Template::query()->get();
    }
}