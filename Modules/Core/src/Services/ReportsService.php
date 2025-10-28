<?php

namespace Modules\Core\Services;

use AllowDynamicProperties;
use DB;
use Modules\Clients\Models\tmpClient;
use Modules\Payments\Models\Payment;

use function Modules\Reports\Services\CONCAT;

#[AllowDynamicProperties]
class ReportsService extends BaseService
{
    /**
     * @originalName salesByClient
     *
     * @originalFile Report.php
     */
    public function salesByClient(?string $from_date = null, ?string $to_date = null)
    {
        $query = tmpClient::query()
            ->select(['client_name', 'client_surname'])
            ->addSelect(DB::raw(CONCAT('client_name', ' ', 'client_surname') . ' AS client_namesurname'))
            ->with(['invoices' => function ($q) use ($from_date, $to_date) {
                if ($from_date && $to_date) {
                    $q->whereBetween('invoice_date_created', [date_to_mysql($from_date), date_to_mysql($to_date)]);
                }
            }]);

        $query->withCount(['invoices as invoice_count' => function ($q) use ($from_date, $to_date) {
            if ($from_date && $to_date) {
                $q->whereBetween('invoice_date_created', [date_to_mysql($from_date), date_to_mysql($to_date)]);
            }
        }]);
        $query->withSum(['invoices as sales' => function ($q) use ($from_date, $to_date) {
            if ($from_date && $to_date) {
                $q->whereBetween('invoice_date_created', [date_to_mysql($from_date), date_to_mysql($to_date)]);
            }
        }], 'invoice_item_subtotal');
        $query->withSum(['invoices as sales_with_tax' => function ($q) use ($from_date, $to_date) {
            if ($from_date && $to_date) {
                $q->whereBetween('invoice_date_created', [date_to_mysql($from_date), date_to_mysql($to_date)]);
            }
        }], 'invoice_total');

        if ($from_date && $to_date) {
            $query->whereHas('invoices', function ($q) use ($from_date, $to_date) {
                $q->whereBetween('invoice_date_created', [date_to_mysql($from_date), date_to_mysql($to_date)]);
            });
        } else {
            $query->whereHas('invoices');
        }

        $clients = $query->get();
        // Concatenate names in PHP
        foreach ($clients as $client) {
            $client->client_namesurname = $client->client_name . ' ' . $client->client_surname;
        }
        // Sort by concatenated name
        $clients = $clients->sortBy('client_namesurname')->values();

        return $clients;
    }

    /**
     * @originalName paymentHistory
     *
     * @originalFile Report.php
     */
    public function paymentHistory(?string $from_date = null, ?string $to_date = null)
    {
        $query = Payment::query();
        if ($from_date && $to_date) {
            $query->whereBetween('payment_date', [date_to_mysql($from_date), date_to_mysql($to_date)]);
        }

        return $query->get();
    }

    /**
     * Invoice aging report using Eloquent.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function invoiceAging(): \Illuminate\Database\Eloquent\Collection
    {
        $clients = tmpClient::query()->with(['invoices.invoiceAmount'])
            ->get();
        $result = collect();
        foreach ($clients as $client) {
            $range_1 = $client->invoices->filter(function ($invoice) {
                $due = \Carbon\Carbon::parse($invoice->invoice_date_due);

                return $due->lte(now()->subDays(1)) && $due->gte(now()->subDays(15));
            })->sum(fn ($invoice) => $invoice->invoiceAmount->invoice_balance ?? 0);
            $range_2 = $client->invoices->filter(function ($invoice) {
                $due = \Carbon\Carbon::parse($invoice->invoice_date_due);

                return $due->lte(now()->subDays(16)) && $due->gte(now()->subDays(30));
            })->sum(fn ($invoice) => $invoice->invoiceAmount->invoice_balance ?? 0);
            $range_3 = $client->invoices->filter(function ($invoice) {
                $due = \Carbon\Carbon::parse($invoice->invoice_date_due);

                return $due->lte(now()->subDays(31));
            })->sum(fn ($invoice) => $invoice->invoiceAmount->invoice_balance ?? 0);
            $total_balance = $client->invoices->filter(function ($invoice) {
                $due = \Carbon\Carbon::parse($invoice->invoice_date_due);

                return $due->lte(now()->subDays(1));
            })->sum(fn ($invoice) => $invoice->invoiceAmount->invoice_balance ?? 0);
            if ($range_1 > 0 || $range_2 > 0 || $range_3 > 0 || $total_balance > 0) {
                $result->push([
                    'client_name'    => $client->client_name,
                    'client_surname' => $client->client_surname,
                    'range_1'        => $range_1,
                    'range_2'        => $range_2,
                    'range_3'        => $range_3,
                    'total_balance'  => $total_balance,
                ]);
            }
        }

        return $result;
    }

    /**
     * Invoices per client using Eloquent.
     *
     * @param string|null $from_date
     * @param string|null $to_date
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function invoicesPerClient(?string $from_date = null, ?string $to_date = null): \Illuminate\Database\Eloquent\Collection
    {
        $from_date = $from_date ? \Carbon\Carbon::parse($from_date)->format('Y-m-d') : null;
        $to_date   = $to_date ? \Carbon\Carbon::parse($to_date)->format('Y-m-d') : null;
        $query     = tmpClient::query()->with(['invoices.invoiceAmount']);
        if ($from_date && $to_date) {
            $query->whereHas('invoices', function ($q) use ($from_date, $to_date) {
                $q->whereBetween('invoice_date_created', [$from_date, $to_date]);
            });
        }

        return $query->get();
    }

    /**
     * @originalName salesByYear
     *
     * @originalFile Report.php
     */
    public function salesByYear(?string $from_date = null, ?string $to_date = null, ?int $minQuantity = null, ?int $maxQuantity = null, bool $taxChecked = false): \Illuminate\Support\Collection
    {
        $from_date = $from_date ? \Carbon\Carbon::parse($from_date)->format('Y-m-d') : now()->format('Y-m-d');
        $to_date   = $to_date ? \Carbon\Carbon::parse($to_date)->format('Y-m-d') : now()->format('Y-m-d');
        $clients   = tmpClient::query()->with(['invoices.invoiceAmount'])->get();
        $result    = collect();
        foreach ($clients as $client) {
            $payments = collect();
            foreach ($client->invoices as $invoice) {
                $created = \Carbon\Carbon::parse($invoice->invoice_date_created);
                if ($created->between($from_date, $to_date)) {
                    $amount = $taxChecked ? ($invoice->invoiceAmount->invoice_total ?? 0) : ($invoice->invoiceAmount->invoice_item_subtotal ?? 0);
                    $payments->push([
                        'year'   => $created->year,
                        'month'  => $created->month,
                        'amount' => $amount,
                    ]);
                }
            }
            $grouped = $payments->groupBy('year')->map(function ($yearPayments) {
                return $yearPayments->groupBy('month')->map(function ($monthPayments) {
                    return $monthPayments->sum('amount');
                });
            });
            $total_payment = $payments->sum('amount');
            if ((null === $minQuantity || $total_payment >= $minQuantity) && (null === $maxQuantity || $total_payment <= $maxQuantity)) {
                $result->push([
                    'client_id'              => $client->client_id,
                    'client_name'            => $client->client_name,
                    'client_surname'         => $client->client_surname,
                    'VAT_ID'                 => $client->client_vat_id,
                    'total_payment'          => $total_payment,
                    'payments_by_year_month' => $grouped,
                ]);
            }
        }

        return $result->sortBy('client_name')->values();
    }
}
