<?php

namespace Modules\Invoices\Models;

use Illuminate\Database\Eloquent\Model;

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */
#[AllowDynamicProperties]
class Template extends Model
{
    /**
     * @originalName getInvoiceTemplates
     *
     * @originalFile Template.php
     */
    public function getInvoiceTemplates($type = 'pdf')
    {
        $this->load->helper('directory');
        if ($type == 'pdf') {
            $templates = directory_map(APPPATH . '/views/invoice_templates/pdf', true);
        } elseif ($type == 'public') {
            $templates = directory_map(APPPATH . '/views/invoice_templates/public', true);
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
        $this->load->helper('directory');
        if ($type == 'pdf') {
            $templates = directory_map(APPPATH . '/views/quote_templates/pdf', true);
        } elseif ($type == 'public') {
            $templates = directory_map(APPPATH . '/views/quote_templates/public', true);
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
