<?php

namespace Modules\UserClients\Services;

use AllowDynamicProperties;
use Modules\Clients\Models\Client;
use Modules\Core\Services\BaseService;
use Modules\UserClients\Models\UserClient;
use Modules\Users\Models\User;

#[AllowDynamicProperties]
class UserClientsService extends BaseService
{
    public $table = 'ip_user_clients';

    public $primary_key = 'ip_user_clients.user_client_id';

    /**
     * Get a base UserClient query with relationships for select.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function defaultSelect(): \Illuminate\Database\Eloquent\Builder
    {
        return UserClient::query()->with(['user', 'client']);
    }

    /**
     * Get a UserClient query with relationships (joins).
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function defaultJoin(): \Illuminate\Database\Eloquent\Builder
    {
        return UserClient::query()->with(['user', 'client']);
    }

    /**
     * Get a UserClient query ordered by client name ascending.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function defaultOrderBy(): \Illuminate\Database\Eloquent\Builder
    {
        return UserClient::query()->join('ip_clients', 'ip_clients.client_id', '=', 'ip_user_clients.client_id')->orderBy('ip_clients.client_name', 'ASC');
    }

    /**
     * @originalName validationRules
     *
     * @originalFile UserClient.php
     */
    public function validationRules()
    {
        return ['user_id' => ['field' => 'user_id', 'label' => trans('user'), 'rules' => 'required'], 'client_id' => ['field' => 'client_id', 'label' => trans('client'), 'rules' => 'required']];
    }

    /**
     * @originalName assignedTo
     *
     * @originalFile UserClient.php
     */
    public function assignedTo($user_id)
    {
        $this->filter_where('ip_user_clients.user_id', $user_id);

        return $this;
    }

    /**
     * Assign all unassigned clients to users using Eloquent.
     *
     * @param array $users_id
     *
     * @return void
     */
    public function setAllClientsUser(array $users_id): void
    {
        foreach ($users_id as $user_id) {
            $assignedClientIds = UserClient::query()->where('user_id', $user_id)->pluck('client_id')->toArray();
            $clients           = Client::query()->whereNotIn('client_id', $assignedClientIds)->get();
            foreach ($clients as $client) {
                UserClient::query()->create(['user_id' => $user_id, 'client_id' => $client->client_id]);
            }
        }
    }

    /**
     * Get all users with user_all_clients flag using Eloquent.
     *
     * @return array
     */
    public function getUsersAllClients(): array
    {
        return User::query()->where('user_all_clients', 1)->pluck('user_id')->toArray();
    }
}
