<?php

namespace Modules\UserClients\Controllers;

use Illuminate\Contracts\View\View;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use AllowDynamicProperties;
use Modules\Clients\Services\ClientsService;
use Modules\Core\Controllers\AdminController;
use Modules\UserClients\Services\UserClientsService;
use Modules\Users\Services\UsersService;

#[AllowDynamicProperties]
class UserClientsController extends AdminController
{
    /**
     * Initialize the controller and perform the parent controller setup.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @originalName index
     *
     * @originalFile UserClientsController.php
     */
    public function index()
    {
        return redirect()->route('users');
    }

    /**
     * @originalName user
     *
     * @originalFile UserClientsController.php
     */
    public function user(Request $request, $id = null) {
        if ($request->post('btn_cancel')) {
            return redirect()->route('users');
        }
        $user = (new UsersService())->getById($id);
        if (empty($user)) {
            return redirect()->route('users');
        }
        $user_clients = (new UserClientsService())->assignedTo($id)->get()->result();

        return view('user_clients.new', ['user' => $user, 'user_clients' => $user_clients]);
    }

    /**
     * @originalName create
     *
     * @originalFile UserClientsController.php
     */
    public function create(Request $request, $user_id = null) {
        if ( ! $user_id) {
            return redirect()->route('custom_values');
        } elseif ($request->post('btn_cancel')) {
            return redirect('user_clients/field/' . $user_id);
        }
        if ((new UserClientsService())->runValidation()) {
            if ($request->post('user_all_clients')) {
                $users_id = [$user_id];
                (new UserClientsService())->setAllClientsUser($users_id);
                $user_update = ['user_all_clients' => 1];
            } else {
                $user_update = ['user_all_clients' => 0];
                (new UserClientsService())->save();
            }
            $this->db->where('user_id', $user_id);
            $this->db->update('ip_users', $user_update);
            return redirect('user_clients/user/' . $user_id);
        }
        $user    = (new UsersService())->getById($user_id);
        $clients = (new ClientsService())->getNotAssignedToUser($user_id);
        $this->layout->set(['id' => $user_id, 'user' => $user, 'clients' => $clients]);
    }

    /**
     * Delete a user-client relation and redirect to that user's client list.
     *
     * Deletes the user-client mapping identified by the given relation ID and redirects to
     * the 'user_clients/user/{user_id}' route for the associated user.
     *
     * @param int $user_client_id the ID of the user-client relation to remove
     */
    public function delete($user_client_id)
    {
        $ref = (new UserClientsService())->getById($user_client_id);
        (new UserClientsService())->delete($user_client_id);
        return redirect('user_clients/user/' . $ref->user_id);
    }
}
