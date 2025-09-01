<?php

namespace Modules\InvoiceGroups\Services;

use AllowDynamicProperties;
use DB;
use Modules\Core\Services\BaseService;
use Modules\InvoiceGroups\Models\InvoiceGroup;

#[AllowDynamicProperties]
class InvoiceGroupsService extends BaseService
{
    public $table = 'ip_invoice_groups';

    public $primary_key = 'ip_invoice_groups.invoice_group_id';

    /**
     * @originalName defaultSelect
     *
     * @originalFile InvoiceGroup.php
     */
    public function defaultSelect(): \Illuminate\Database\Eloquent\Builder
    {
        return InvoiceGroup::query()->select('*');
    }

    /**
     * @originalName defaultOrderBy
     *
     * @originalFile InvoiceGroup.php
     */
    public function defaultOrderBy(): \Illuminate\Database\Eloquent\Builder
    {
        return InvoiceGroup::query()->orderBy('invoice_group_name');
    }

    /**
     * @originalName validationRules
     *
     * @originalFile InvoiceGroup.php
     */
    public function validationRules()
    {
        return ['invoice_group_name' => ['field' => 'invoice_group_name', 'label' => trans('name'), 'rules' => 'required'], 'invoice_group_identifier_format' => ['field' => 'invoice_group_identifier_format', 'label' => trans('identifier_format'), 'rules' => 'required'], 'invoice_group_next_id' => ['field' => 'invoice_group_next_id', 'label' => trans('next_id'), 'rules' => 'required'], 'invoice_group_left_pad' => ['field' => 'invoice_group_left_pad', 'label' => trans('left_pad'), 'rules' => 'required']];
    }

    /**
     * @originalName generateInvoiceNumber
     *
     * @originalFile InvoiceGroup.php
     */
    public function generateInvoiceNumber($invoice_group_id, $set_next = true)
    {
        $invoice_group      = $this->getById($invoice_group_id);
        $invoice_identifier = $this->parseIdentifierFormat($invoice_group->invoice_group_identifier_format, $invoice_group->invoice_group_next_id, $invoice_group->invoice_group_left_pad);
        if ($set_next) {
            $this->setNextInvoiceNumber($invoice_group_id);
        }

        return $invoice_identifier;
    }

    /**
     * Set the next invoice number using Eloquent.
     *
     * @param int $invoice_group_id
     * @return void
     */
    public function setNextInvoiceNumber(int $invoice_group_id): void
    {
        $invoiceGroup = InvoiceGroup::query()->find($invoice_group_id);
        if ($invoiceGroup) {
            $invoiceGroup->increment('invoice_group_next_id');
        }
    }

    /**
     * @originalName parseIdentifierFormat
     *
     * @originalFile InvoiceGroup.php
     */
    private function parseIdentifierFormat($identifier_format, string $next_id, int $left_pad)
    {
        if (preg_match_all('/{{{([^{|}]*)}}}/', $identifier_format, $template_vars)) {
            foreach ($template_vars[1] as $var) {
                switch ($var) {
                    case 'year':
                        $replace = date('Y');
                        break;
                    case 'yy':
                        $replace = date('y');
                        break;
                    case 'month':
                        $replace = date('m');
                        break;
                    case 'day':
                        $replace = date('d');
                        break;
                    case 'id':
                        $replace = mb_str_pad($next_id, $left_pad, '0', STR_PAD_LEFT);
                        break;
                    default:
                        $replace = '';
                }
                $identifier_format = str_replace('{{{' . $var . '}}}', $replace, $identifier_format);
            }
        }

        return $identifier_format;
    }
}
