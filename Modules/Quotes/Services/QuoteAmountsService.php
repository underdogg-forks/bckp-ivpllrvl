<?php

namespace Modules\Quotes\Services;

use AllowDynamicProperties;
use Modules\Core\Services\BaseService;
use Modules\Quotes\Models\QuoteAmount;
use Modules\Quotes\Models\QuoteItem;
use Modules\Quotes\Models\QuoteItemAmount;

#[AllowDynamicProperties]
class QuoteAmountsService extends BaseService
{
    /**
     * @var int
     */
    public $decimal_places = 2;

    public function __construct()
    {
        $this->decimal_places = (int) get_setting('tax_rate_decimal_places');
    }

    /**
     * @originalName calculate
     *
     * @originalFile QuoteAmount.php
     */
    public function calculate(int $quote_id, array $global_discount): void
    {
        $item_ids             = QuoteItem::query()->where('quote_id', $quote_id)->pluck('item_id');
        $amounts              = QuoteItemAmount::query()->whereIn('item_id', $item_ids)->get();
        $quote_item_subtotal  = $amounts->sum('item_subtotal');
        $quote_item_tax_total = $amounts->sum('item_tax_total');
        $quote_item_discount  = $amounts->sum('item_discount');
        if (config('legacy_calculation')) {
            $quote_item_subtotal_calc = $quote_item_subtotal - $quote_item_discount;
            $quote_subtotal           = $quote_item_subtotal_calc + $quote_item_tax_total;
            $quote_total              = $this->calculateDiscount($quote_id, $quote_subtotal);
        } else {
            $quote_item_subtotal_calc = $quote_item_subtotal - $quote_item_discount - ($global_discount['item'] ?? 0);
            $quote_total              = $quote_item_subtotal_calc + $quote_item_tax_total;
        }
        QuoteAmount::updateOrCreate(
            ['quote_id' => $quote_id],
            [
                'quote_item_subtotal'  => $quote_item_subtotal_calc,
                'quote_item_tax_total' => $quote_item_tax_total,
                'quote_total'          => $quote_total,
            ]
        );
    }

    /**
     * Calculate discount for a quote using Eloquent.
     *
     * @param int $quote_id
     * @param float $quote_total
     * @return float
     */
    public function calculateDiscount(int $quote_id, float $quote_total): float
    {
        $quote = \Modules\Quotes\Models\Quote::query()->find($quote_id);
        if (! $quote) {
            return $quote_total;
        }
        $total            = (float) number_format((float) $quote_total, $this->decimal_places, '.', '');
        $discount_amount  = (float) number_format((float) $quote->quote_discount_amount, $this->decimal_places, '.', '');
        $discount_percent = (float) number_format((float) $quote->quote_discount_percent, $this->decimal_places, '.', '');
        $total -= $discount_amount;

        return $total - round($total / 100 * $discount_percent, $this->decimal_places);
    }

    /**
     * Get global discount for a quote using Eloquent.
     *
     * @param int $quote_id
     * @return float
     */
    public function getGlobalDiscount(int $quote_id): float
    {
        $item_ids = QuoteItem::query()->where('quote_id', $quote_id)->pluck('item_id');
        $amounts = QuoteItemAmount::query()->whereIn('item_id', $item_ids)->get();
        $global_discount = $amounts->sum('item_subtotal') - ($amounts->sum('item_total') - $amounts->sum('item_tax_total') + $amounts->sum('item_discount'));

        return $global_discount;
    }

    /**
     * Calculate quote taxes using Eloquent.
     *
     * @param int $quote_id
     * @return float|null
     */
    public function calculateQuoteTaxes(int $quote_id): ?float
    {
        if (! config('legacy_calculation')) {
            return null;
        }
        $quoteTaxRates = \Modules\Quotes\Models\QuoteTaxRate::query()->where('quote_id', $quote_id)->get();
        // Implement tax calculation logic as needed, or log for now
        \Log::info('Quote taxes calculated', ['quote_id' => $quote_id, 'tax_rates_count' => $quoteTaxRates->count()]);

        return $quoteTaxRates->sum('tax_amount');
    }

    /**
     * Get total quoted amount for a period using Eloquent.
     *
     * @param string|null $period
     * @return float
     */
    public function getTotalQuoted(?string $period = null): float
    {
        $query = QuoteAmount::query()->join('ip_quotes', 'ip_quotes.quote_id', '=', 'ip_quote_amounts.quote_id');
        switch ($period) {
            case 'month':
                $query->whereMonth('ip_quotes.quote_date_created', now()->month)
                    ->whereYear('ip_quotes.quote_date_created', now()->year);
                break;
            case 'last_month':
                $lastMonth = now()->subMonth();
                $query->whereMonth('ip_quotes.quote_date_created', $lastMonth->month)
                    ->whereYear('ip_quotes.quote_date_created', $lastMonth->year);
                break;
            case 'year':
                $query->whereYear('ip_quotes.quote_date_created', now()->year);
                break;
            case 'last_year':
                $query->whereYear('ip_quotes.quote_date_created', now()->subYear()->year);
                break;
        }
        return (float) $query->sum('quote_total');
    }

    /**
     * Get status totals for quotes using Eloquent.
     *
     * @param string $period
     * @return array
     */
    public function getStatusTotals(string $period = 'this-month'): array
    {
        $query = QuoteAmount::query()->join('ip_quotes', 'ip_quotes.quote_id', '=', 'ip_quote_amounts.quote_id');
        switch ($period) {
            case 'last-month':
                $lastMonth = now()->subMonth();
                $query->whereMonth('ip_quotes.quote_date_created', $lastMonth->month)
                    ->whereYear('ip_quotes.quote_date_created', $lastMonth->year);
                break;
            case 'this-quarter':
                $query->whereRaw('QUARTER(ip_quotes.quote_date_created) = ?', [now()->quarter])
                    ->whereYear('ip_quotes.quote_date_created', now()->year);
                break;
            case 'last-quarter':
                $lastQuarter = now()->subQuarter();
                $query->whereRaw('QUARTER(ip_quotes.quote_date_created) = ?', [$lastQuarter->quarter])
                    ->whereYear('ip_quotes.quote_date_created', $lastQuarter->year);
                break;
            case 'this-year':
                $query->whereYear('ip_quotes.quote_date_created', now()->year);
                break;
            case 'last-year':
                $query->whereYear('ip_quotes.quote_date_created', now()->subYear()->year);
                break;
            default:
                $query->whereMonth('ip_quotes.quote_date_created', now()->month)
                    ->whereYear('ip_quotes.quote_date_created', now()->year);
                break;
        }
        $results = $query->select('ip_quotes.quote_status_id')
            ->selectRaw('SUM(quote_total) AS sum_total')
            ->selectRaw('COUNT(*) AS num_total')
            ->groupBy('ip_quotes.quote_status_id')
            ->get();
        $statuses = \Modules\Quotes\Models\Quote::statuses();
        $return = [];
        foreach ($statuses as $key => $status) {
            $return[$key] = [
                'quote_status_id' => $key,
                'class' => $status['class'],
                'label' => $status['label'],
                'href' => $status['href'],
                'sum_total' => 0,
                'num_total' => 0,
            ];
        }
        foreach ($results as $result) {
            $return[$result->quote_status_id] = array_merge($return[$result->quote_status_id], [
                'sum_total' => $result->sum_total,
                'num_total' => $result->num_total,
            ]);
        }
        return $return;
    }
}
