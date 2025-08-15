<?php

namespace Modules\UserClients\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;

#[AllowDynamicProperties]
class UserClientsController extends AdminController
{
    /**
     * Custom_Values constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('users/mdl_users');
        $this->load->model('clients/mdl_clients');
        $this->load->model('user_clients/mdl_user_clients');
    }

    /**
     * @originalName index
     *
     * @originalFile UserClientsController.php
     */
    public function index()
    {
        redirect()->route('users');
    }

    /**
     * @originalName user
     *
     * @originalFile UserClientsController.php
     */
    public function user($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect()->route('users');
        }
        $user = (new UsersService())->getById($id);
        if (empty($user)) {
            redirect()->route('users');
        }
        $user_clients = (new UserClientsService())->assignedTo($id)->get()->result();

        return view('user_clients.new', ['user' => $user, 'user_clients' => $user_clients]);
        $this->layout->set('id', $id);
        $this->layout->buffer('content', 'user_clients/field');
        $this->layout->render();
    }

    /**
     * @originalName create
     *
     * @originalFile UserClientsController.php
     */
    public function create($user_id = null)
    {
        if ( ! $user_id) {
            redirect()->route('custom_values');
        } elseif ($this->input->post('btn_cancel')) {
            redirect('user_clients/field/' . $user_id);
        }
        if ((new UserClientsService())->runValidation()) {
            if ($this->input->post('user_all_clients')) {
                $users_id = [$user_id];
                (new UserClientsService())->setAllClientsUser($users_id);
                $user_update = ['user_all_clients' => 1];
            } else {
                $user_update = ['user_all_clients' => 0];
                (new UserClientsService())->save();
            }
            $this->db->where('user_id', $user_id);
            $this->db->update('ip_users', $user_update);
            redirect('user_clients/user/' . $user_id);
        }
        $user    = (new UsersService())->getById($user_id);
        $clients = (new ClientsService())->getNotAssignedToUser($user_id);
        $this->layout->set(['id' => $user_id, 'user' => $user, 'clients' => $clients]);
    }

    /**
     * @originalName delete
     *
     * @originalFile UserClientsController.php
     */
    public function delete($user_client_id)
    {
        $ref = (new UserClientsService())->getById($user_client_id);
        (new UserClientsService())->delete($user_client_id);
        redirect('user_clients/user/' . $ref->user_id);
    }
}
