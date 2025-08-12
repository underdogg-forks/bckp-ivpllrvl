<?php

namespace Modules\UserClients\Models;

use AllowDynamicProperties;
use Modules\Core\Models\MyModel;

#[AllowDynamicProperties]
class UserClient extends MyModel
{
    public $table = 'ip_user_clients';

    public $primary_key = 'ip_user_clients.user_client_id';

    /**
     * @originalName defaultSelect
     *
     * @originalFile UserClient.php
     */
    public function defaultSelect()
    {
        $this->db->select('ip_user_clients.*, ip_users.user_name, ip_clients.client_name, ip_clients.client_surname');
    }

    /**
     * @originalName defaultJoin
     *
     * @originalFile UserClient.php
     */
    public function defaultJoin()
    {
        $this->db->join('ip_users', 'ip_users.user_id = ip_user_clients.user_id');
        $this->db->join('ip_clients', 'ip_clients.client_id = ip_user_clients.client_id');
    }

    /**
     * @originalName defaultOrderBy
     *
     * @originalFile UserClient.php
     */
    public function defaultOrderBy()
    {
        $this->db->orderBy('ip_clients.client_name', 'ACS');
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
     * @originalName setAllClientsUser
     *
     * @originalFile UserClient.php
     */
    public function setAllClientsUser($users_id)
    {
        $this->load->model('clients/mdl_clients');
        $nbUsers = count($users_id);
        for ($x = 0; $x < $nbUsers; $x++) {
            $clients   = $this->mdl_clients->getNotAssignedToUser($users_id[$x]);
            $nbClients = count($clients);
            for ($i = 0; $i < $nbClients; $i++) {
                $user_client = ['user_id' => $users_id[$x], 'client_id' => $clients[$i]->client_id];
                $this->db->insert('ip_user_clients', $user_client);
            }
        }
    }

    /**
     * @originalName getUsersAllClients
     *
     * @originalFile UserClient.php
     */
    public function getUsersAllClients()
    {
        $this->load->model('users/mdl_users');
        $users     = $this->mdl_users->where('user_all_clients', 1)->get()->result();
        $new_users = [];
        $nbUsers   = count($users);
        for ($i = 0; $i < $nbUsers; $i++) {
            $new_users[] = $users[$i]->user_id;
        }
        $this->setAllClientsUser($new_users);
    }
}
