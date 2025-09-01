<?php

namespace Modules\Clients\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;

#[AllowDynamicProperties]
class AjaxController extends AdminController
{
    public $ajax_controller = true;

    /**
     * @originalName nameQuery
     *
     * @originalFile AjaxController.php
     */
    public function nameQuery()
    {
        // Load the model & helper
        $this->load->model('clients/mdl_clients');
        $response = [];
        // GetController the post input
        $query                   = $this->input->get('query');
        $permissiveSearchClients = $this->input->get('permissive_search_clients');
        if (empty($query)) {
            echo json_encode($response);
            exit;
        }
        // Search for chars "in the middle" of clients names
        $moreClientsQuery = $permissiveSearchClients ? '%' : '';
        // Search for clients
        $escapedQuery = $this->db->escape_str($query);
        $escapedQuery = str_replace('%', '', $escapedQuery);
        $clients      = (new ClientsService())->where('client_active', 1)->having("client_name LIKE '" . $moreClientsQuery . $escapedQuery . "%'")->or_having("client_surname LIKE '" . $moreClientsQuery . $escapedQuery . "%'")->or_having("client_fullname LIKE '" . $moreClientsQuery . $escapedQuery . "%'")->orderBy('client_name')->get()->result();
        foreach ($clients as $client) {
            $response[] = ['id' => $client->client_id, 'text' => htmlsc(format_client($client, false))];
        }
        // Return the results
        echo json_encode($response);
    }

    /**
     * @originalName getLatest
     *
     * @originalFile AjaxController.php
     */
    public function getLatest()
    {
        // Load the model & helper
        $this->load->model('clients/mdl_clients');
        $response = [];
        $clients  = (new ClientsService())->where('client_active', 1)->limit(5)->orderBy('client_date_created')->get()->result();
        foreach ($clients as $client) {
            $response[] = ['id' => $client->client_id, 'text' => htmlsc(format_client($client, false))];
        }
        // Return the results
        echo json_encode($response);
    }

    /**
     * @originalName savePreferencePermissiveSearchClients
     *
     * @originalFile AjaxController.php
     */
    public function savePreferencePermissiveSearchClients()
    {
        $this->load->model('mdl_settings');
        $permissiveSearchClients = $this->input->get('permissive_search_clients');
        if ( ! preg_match('!^[0-1]{1}$!', $permissiveSearchClients)) {
            exit;
        }
        (new SettingsService())->save('enable_permissive_search_clients', $permissiveSearchClients);
    }

    /**
     * @originalName deleteClientNote
     *
     * @originalFile AjaxController.php
     */
    public function deleteClientNote()
    {
        $success        = 0;
        $client_note_id = $this->input->post('client_note_id');
        $this->load->model('mdl_client_notes');
        // Only continue if the note exists or no item id was provided
        if ((new ClientNotesService())->getById($client_note_id) || empty($client_note_id)) {
            // Delete invoice item
            $this->load->model('mdl_client_notes');
            $item = (new ClientNotesService())->delete($client_note_id);
            // Check if deletion was successful
            if ($item) {
                $success = 1;
            }
        }
        // Return the response
        echo json_encode(['success' => $success]);
    }

    /**
     * @originalName saveClientNote
     *
     * @originalFile AjaxController.php
     */
    public function saveClientNote()
    {
        $this->load->model('clients/mdl_client_notes');
        if ((new ClientNotesService())->runValidation()) {
            (new ClientNotesService())->save();
            $response = ['success' => 1, 'new_token' => $this->security->get_csrf_hash()];
        } else {
            $this->load->helper('json_error');
            $response = ['success' => 0, 'new_token' => $this->security->get_csrf_hash(), 'validation_errors' => json_errors()];
        }
        echo json_encode($response);
    }

    /**
     * @originalName loadClientNotes
     *
     * @originalFile AjaxController.php
     */
    public function loadClientNotes()
    {
        $this->load->model('clients/mdl_client_notes');
        $data = ['client_notes' => (new ClientNotesService())->where('client_id', $this->input->post('client_id'))->get()->result()];
        $this->layout->loadView('clients/partial_notes', $data);
    }
}
