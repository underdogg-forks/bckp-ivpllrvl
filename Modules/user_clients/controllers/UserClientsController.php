<?php

namespace Modules\Userclients\Controllers;

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */
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
        redirect('users');
    }

    /**
     * @originalName user
     *
     * @originalFile UserClientsController.php
     */
    public function user($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('users');
        }
        $user = $this->mdl_users->getById($id);
        if (empty($user)) {
            redirect('users');
        }
        $user_clients = $this->mdl_user_clients->assignedTo($id)->get()->result();
        $this->layout->set(['user' => $user, 'user_clients' => $user_clients]);
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
            redirect('custom_values');
        } elseif ($this->input->post('btn_cancel')) {
            redirect('user_clients/field/' . $user_id);
        }
        if ($this->mdl_user_clients->run_validation()) {
            if ($this->input->post('user_all_clients')) {
                $users_id = [$user_id];
                $this->mdl_user_clients->setAllClientsUser($users_id);
                $user_update = ['user_all_clients' => 1];
            } else {
                $user_update = ['user_all_clients' => 0];
                $this->mdl_user_clients->save();
            }
            $this->db->where('user_id', $user_id);
            $this->db->update('ip_users', $user_update);
            redirect('user_clients/user/' . $user_id);
        }
        $user    = $this->mdl_users->getById($user_id);
        $clients = $this->mdl_clients->getNotAssignedToUser($user_id);
        $this->layout->set(['id' => $user_id, 'user' => $user, 'clients' => $clients]);
        $this->layout->buffer('content', 'user_clients/new');
        $this->layout->render();
    }

    /**
     * @originalName delete
     *
     * @originalFile UserClientsController.php
     */
    public function delete($user_client_id)
    {
        $ref = $this->mdl_user_clients->getById($user_client_id);
        $this->mdl_user_clients->delete($user_client_id);
        redirect('user_clients/user/' . $ref->user_id);
    }
}
