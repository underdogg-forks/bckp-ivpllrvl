<?php

namespace Modules\Quotes\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;
use Modules\CustomFields\Models\CustomField;
use Modules\CustomValues\Models\CustomValue;
use Modules\Quotes\Models\Quote;

#[AllowDynamicProperties]
class QuotesController extends AdminController
{
    /**
     * QuotesController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_quotes');
    }

    /**
     * @originalName index
     *
     * @originalFile QuotesController.php
     */
    public function index(): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('quotes.status', ['status' => 'all']);
    }

    /**
     * @originalName status
     *
     * @originalFile QuotesController.php
     */
    public function status(string $status = 'all', int $page = 0): \Illuminate\Contracts\View\View
    {
        $quotesQuery = Quote::query();
        switch ($status) {
            case 'draft':
                $quotesQuery->where('status', 'draft');
                break;
            case 'sent':
                $quotesQuery->where('status', 'sent');
                break;
            case 'viewed':
                $quotesQuery->where('status', 'viewed');
                break;
            case 'approved':
                $quotesQuery->where('status', 'approved');
                break;
            case 'rejected':
                $quotesQuery->where('status', 'rejected');
                break;
            case 'canceled':
                $quotesQuery->where('status', 'canceled');
                break;
        }
        $quotes         = $quotesQuery->paginate(20);
        $quote_statuses = ['draft', 'sent', 'viewed', 'approved', 'rejected', 'canceled'];

        return view('quotes.index', [
            'quotes'             => $quotes,
            'status'             => $status,
            'filter_display'     => true,
            'filter_placeholder' => trans('filter_quotes'),
            'filter_method'      => 'filter_quotes',
            'quote_statuses'     => $quote_statuses,
        ]);
    }

    /**
     * @originalName view
     *
     * @originalFile QuotesController.php
     */
    public function view(int $quote_id): \Illuminate\Contracts\View\View
    {
        $quote = Quote::with(['items', 'taxRates', 'units', 'customFields', 'customValues', 'uploads'])->find($quote_id);
        if ( ! $quote) {
            abort(404);
        }
        $custom_fields = CustomField::query()->where('table', 'ip_quote_custom')->get();
        $custom_values = [];
        foreach ($custom_fields as $custom_field) {
            if (in_array($custom_field->custom_field_type, CustomValue::customValueFields())) {
                $values                                        = CustomValue::query()->where('custom_field_id', $custom_field->custom_field_id)->get();
                $custom_values[$custom_field->custom_field_id] = $values;
            }
        }

        return view('quotes.view', [
            'quote'         => $quote,
            'custom_fields' => $custom_fields,
            'custom_values' => $custom_values,
        ]);
    }

    /**
     * @originalName delete
     *
     * @originalFile QuotesController.php
     */
    public function delete($quote_id): \Illuminate\Http\RedirectResponse
    {
        Quote::destroy($quote_id);

        return redirect()->route('quotes.index');
    }

    /**
     * @originalName generatePdf
     *
     * @originalFile QuotesController.php
     */
    public function generatePdf($quote_id, $stream = true, $quote_template = null): void
    {
        // PDF generation logic should use a service or helper registered in Laravel
        // Skipping direct helper loading
        // If mark_quotes_sent_pdf is enabled, mark as sent
        if (config('settings.mark_quotes_sent_pdf') == 1) {
            // Implement service logic here
        }
        // Implement PDF generation logic here
    }

    /**
     * @originalName deleteQuoteTax
     *
     * @originalFile QuotesController.php
     */
    public function deleteQuoteTax(string $quote_id, $quote_tax_rate_id): \Illuminate\Http\RedirectResponse
    {
        // Use Eloquent models/services for tax rate deletion and recalculation
        // Skipping direct model loading
        // Implement service logic here
        return redirect()->route('quotes.view', ['quote_id' => $quote_id]);
    }

    /**
     * @originalName recalculateAllQuotes
     *
     * @originalFile QuotesController.php
     */
    public function recalculateAllQuotes(): void
    {
        $quote_ids = Quote::pluck('id');
        foreach ($quote_ids as $quote_id) {
            // Implement recalculation logic here using services
        }
    }
}
