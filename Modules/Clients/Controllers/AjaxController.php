<?php

namespace Modules\Clients\Controllers;

use AllowDynamicProperties;
use Modules\Clients\Services\ClientNotesService;
use Modules\Clients\Services\ClientsService;
use Modules\Core\Controllers\AdminController;
use Modules\Settings\Services\SettingsService;

#[AllowDynamicProperties]
class AjaxController extends AdminController
{
    public $ajax_controller = true;

    /**
     * @originalName nameQuery
     *
     * @originalFile AjaxController.php
     */
    public function nameQuery(): void
    {
        $response = [];
        $query                   = $this->input->get('query');
        $permissiveSearchClients = $this->input->get('permissive_search_clients');
        if (empty($query)) {
            echo json_encode($response);
            exit;
        }
        $moreClientsQuery = $permissiveSearchClients ? '%' : '';
        $escapedQuery = $this->db->escape_str($query);
        $escapedQuery = str_replace('%', '', $escapedQuery);
        $clients      = (new ClientsService())->where('client_active', 1)->having("client_name LIKE '" . $moreClientsQuery . $escapedQuery . "%'")->or_having("client_surname LIKE '" . $moreClientsQuery . $escapedQuery . "%'")->or_having("client_fullname LIKE '" . $moreClientsQuery . $escapedQuery . "%'")->orderBy('client_name')->get()->result();
        foreach ($clients as $client) {
            $response[] = ['id' => $client->client_id, 'text' => htmlsc(format_client($client, false))];
        }
        echo json_encode($response);
    }

    /**
     * @originalName getLatest
     *
     * @originalFile AjaxController.php
     */
    public function getLatest(): void
    {
        $response = [];
        $clients  = (new ClientsService())->where('client_active', 1)->limit(5)->orderBy('client_date_created')->get()->result();
        foreach ($clients as $client) {
            $response[] = ['id' => $client->client_id, 'text' => htmlsc(format_client($client, false))];
        }
        echo json_encode($response);
    }

    /**
     * @originalName savePreferencePermissiveSearchClients
     *
     * @originalFile AjaxController.php
     */
    public function savePreferencePermissiveSearchClients(): void
    {
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
    public function deleteClientNote(): void
    {
        $success        = 0;
        $client_note_id = $this->input->post('client_note_id');
        if ((new ClientNotesService())->getById($client_note_id) || empty($client_note_id)) {
            $item = (new ClientNotesService())->delete($client_note_id);
            if ($item) {
                $success = 1;
            }
        }
        echo json_encode(['success' => $success]);
    }

    /**
     * @originalName saveClientNote
     *
     * @originalFile AjaxController.php
     */
    public function saveClientNote(): void
    {
        if ((new ClientNotesService())->runValidation()) {
            (new ClientNotesService())->save();
            $response = ['success' => 1, 'new_token' => $this->security->get_csrf_hash()];
        } else {
            $response = ['success' => 0, 'new_token' => $this->security->get_csrf_hash(), 'validation_errors' => json_errors()];
        }
        echo json_encode($response);
    }

    /**
     * @originalName loadClientNotes
     *
     * @originalFile AjaxController.php
     */
    public function loadClientNotes(): void
    {
        $data = ['client_notes' => (new ClientNotesService())->where('client_id', $this->input->post('client_id'))->get()->result()];
        $this->layout->loadView('clients/partial_notes', $data);
    }
}
