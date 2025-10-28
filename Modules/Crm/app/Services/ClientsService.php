<?php

namespace Modules\Crm\app\Services;

use AllowDynamicProperties;
use DB;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Modules\Clients\Models\tmpClient;
use Modules\Core\Services\BaseService;

#[AllowDynamicProperties]
class ClientsService extends BaseService
{
    public $table = 'ip_clients';

    public $primary_key = 'ip_clients.client_id';

    public $date_created_field = 'client_date_created';

    public $date_modified_field = 'client_date_modified';

    /**
     * @originalName defaultSelect
     *
     * @originalFile Client.php
     */
    public function defaultSelect(): \Illuminate\Database\Eloquent\Builder
    {
        return tmpClient::query()->select(['*', DB::raw('CONCAT(client_name, " ", client_surname) as client_fullname')]);
    }

    /**
     * @originalName defaultOrderBy
     *
     * @originalFile Client.php
     */
    public function defaultOrderBy(): \Illuminate\Database\Eloquent\Builder
    {
        return tmpClient::query()->orderBy('client_name');
    }

    /**
     * @originalName validationRules
     *
     * @originalFile Client.php
     */
    public function validationRules()
    {
        return [
            'client_title'              => ['field' => 'client_title', 'label' => trans('client_title')],
            'client_name'               => ['field' => 'client_name', 'label' => trans('client_name'), 'rules' => 'required'],
            'client_surname'            => ['field' => 'client_surname', 'label' => trans('client_surname')],
            'client_active'             => ['field' => 'client_active'],
            'client_language'           => ['field' => 'client_language', 'label' => trans('lang'), 'rules' => 'trim'],
            'client_address_1'          => ['field' => 'client_address_1'],
            'client_address_2'          => ['field' => 'client_address_2'],
            'client_city'               => ['field' => 'client_city'],
            'client_state'              => ['field' => 'client_state'],
            'client_zip'                => ['field' => 'client_zip'],
            'client_country'            => ['field' => 'client_country', 'rules' => 'trim'],
            'client_phone'              => ['field' => 'client_phone'],
            'client_fax'                => ['field' => 'client_fax'],
            'client_mobile'             => ['field' => 'client_mobile'],
            'client_email'              => ['field' => 'client_email'],
            'client_web'                => ['field' => 'client_web'],
            'client_company'            => ['field' => 'client_company'],
            'client_vat_id'             => ['field' => 'client_vat_id'],
            'client_tax_code'           => ['field' => 'client_tax_code'],
            'client_invoicing_contact'  => ['field' => 'client_invoicing_contact', 'rules' => 'trim'],
            'client_einvoicing_version' => ['field' => 'client_einvoicing_version'],
            'client_einvoicing_active'  => ['field' => 'client_einvoicing_active'],
            // SUMEX
            'client_birthdate'     => ['field' => 'client_birthdate', 'rules' => 'callback_convert_date'],
            'client_gender'        => ['field' => 'client_gender'],
            'client_avs'           => ['field' => 'client_avs', 'label' => trans('sumex_ssn'), 'rules' => 'callback_fix_avs'],
            'client_insurednumber' => ['field' => 'client_insurednumber', 'label' => trans('sumex_insurednumber')],
            'client_veka'          => ['field' => 'client_veka', 'label' => trans('sumex_veka')],
        ];
    }

    /**
     * @originalName getLatest
     *
     * @originalFile Client.php
     */
    public function getLatest(int $amount = 10)
    {
        return tmpClient::query()
            ->where('client_active', 1)
            ->orderByDesc('client_id')
            ->limit($amount)
            ->get();
    }

    /**
     * @originalName fixAvs
     *
     * @originalFile Client.php
     */
    public function fixAvs($input)
    {
        if ($input != '') {
            if (preg_match('/(\d{3})\.(\d{4})\.(\d{4})\.(\d{2})/', $input, $matches)) {
                return $matches[1] . $matches[2] . $matches[3] . $matches[4];
            }
            if (preg_match('/^\d{13}$/', $input)) {
                return $input;
            }
        }

        return '';
    }

    /**
     * Converts a date string to MySQL format using Carbon.
     *
     * @param string|null $input
     *
     * @return string
     */
    public function convertDate(?string $input): string
    {
        if (empty($input)) {
            return '';
        }
        try {
            return Carbon::parse($input)->format('Y-m-d');
        } catch (Exception $e) {
            Log::warning('Invalid date format for client birthdate', ['input' => $input]);

            return '';
        }
    }

    /**
     * @originalName dbArray
     *
     * @originalFile Client.php
     */
    public function dbArray()
    {
        $db_array = parent::dbArray();
        if ( ! isset($db_array['client_active'])) {
            $db_array['client_active'] = 0;
        }

        return $db_array;
    }

    /**
     * Deletes a client and logs orphan handling.
     *
     * @param int $id
     *
     * @return void
     */
    public function delete($id): void
    {
        parent::delete($id);
        Log::info('Orphan handling triggered after client deletion', ['client_id' => $id]);
        // Implement orphan handling logic here if needed, or rely on database constraints.
    }

    /**
     * @originalName clientLookup
     *
     * @originalFile Client.php
     */
    public function clientLookup(string $client_name): int
    {
        $client = tmpClient::query()->where('client_name', $client_name)->first();
        if ($client) {
            return $client->client_id;
        }
        $newClient = tmpClient::create(['client_name' => $client_name]);

        return $newClient->client_id;
    }

    /**
     * @originalName withTotal
     *
     * @originalFile Client.php
     */
    public function withTotal(): \Illuminate\Database\Eloquent\Builder
    {
        return tmpClient::query()->withSum('invoices as client_invoice_total', 'invoice_total');
    }

    /**
     * @originalName withTotalPaid
     *
     * @originalFile Client.php
     */
    public function withTotalPaid(): \Illuminate\Database\Eloquent\Builder
    {
        return tmpClient::query()->withSum('invoices as client_invoice_paid', 'invoice_paid');
    }

    /**
     * @originalName withTotalBalance
     *
     * @originalFile Client.php
     */
    public function withTotalBalance(): \Illuminate\Database\Eloquent\Builder
    {
        return tmpClient::query()->withSum('invoices as client_invoice_balance', 'invoice_balance');
    }

    /**
     * @originalName isInactive
     *
     * @originalFile Client.php
     */
    public function isInactive(): \Illuminate\Database\Eloquent\Builder
    {
        return tmpClient::query()->where('client_active', 0);
    }

    /**
     * Get a query builder filtered to active clients.
     *
     * @return \Illuminate\Database\Eloquent\Builder a query builder scoped to records where `client_active` equals 1
     */
    public function isActive(): \Illuminate\Database\Eloquent\Builder
    {
        return tmpClient::query()->where('client_active', 1);
    }

    /**
     * Retrieve active clients that are not assigned to the specified user.
     *
     * @param int $user_id the ID of the user to exclude assigned clients for
     *
     * @return \Illuminate\Database\Eloquent\Collection collection of Client models not assigned to the user
     */
    public function getNotAssignedToUser(int $user_id)
    {
        // Assuming a UserClient model exists for the pivot table
        $assignedClientIds = \Modules\UserClients\Models\UserClient::query()
            ->where('user_id', $user_id)
            ->pluck('client_id')
            ->toArray();

        return tmpClient::query()
            ->whereNotIn('client_id', $assignedClientIds)
            ->where('client_active', 1)
            ->get();
    }

    /**
     * Retrieve all clients marked active (client_active = 1).
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Modules\Clients\Models\tmpClient[] collection of active Client models
     */
    public function getActive()
    {
        return \Modules\Clients\Models\tmpClient::query()->where('client_active', 1)->get();
    }
}
