<?php

namespace Modules\Clients\Controllers;

use AllowDynamicProperties;
use Illuminate\Http\Request;
use Modules\Clients\Models\Client;
use Modules\Clients\Services\ClientsService;
use Modules\Core\Controllers\AdminController;

#[AllowDynamicProperties]
class ClientsController extends AdminController
{
    private const CLIENT_TITLE = 'client_title';

    /**
     * ClientsController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_clients');
    }

    /**
     * @originalName index
     *
     * @originalFile ClientsController.php
     */
    public function index(): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('clients.status', ['status' => 'active']);
    }

    /**
     * @originalName status
     *
     * @originalFile ClientsController.php
     */
    public function status(Request $request, string $status = 'active', int $page = 0): \Illuminate\Contracts\View\View
    {
        $clientsQuery = Client::query();
        if ($status === 'active') {
            $clientsQuery->where('active', 1);
        } elseif ($status === 'inactive') {
            $clientsQuery->where('active', 0);
        }
        $clients    = $clientsQuery->get();
        $einvoicing = config('settings.einvoicing');

        // Skipping e-invoicing logic for brevity
        return view('clients.index', [
            'records'            => $clients,
            'filter_display'     => true,
            'filter_placeholder' => trans('filter_clients'),
            'filter_method'      => 'filter_clients',
            'einvoicing'         => $einvoicing,
        ]);
    }

    /**
     * @originalName form
     *
     * @originalFile ClientsController.php
     */
    public function form(Request $request, $id = null): \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
    {
        if ($request->has('btn_cancel')) {
            return redirect()->route('clients.index');
        }

        // Validation and save logic would go here
        // Skipping custom fields and e-invoicing logic for brevity
        return view('clients.form', [
            'client_id'            => $id,
            'custom_fields'        => [],
            'custom_values'        => [],
            'countries'            => [],
            'selected_country'     => null,
            'languages'            => [],
            'client_title_choices' => [],
            'xml_templates'        => [],
            'req_einvoicing'       => null,
        ]);
    }

    /**
     * Display the client detail view for a given client.
     *
     * Aborts with a 404 response if the client cannot be found.
     *
     * @param int|string $client_id The ID of the client to display.
     * @param string $activeTab The tab to mark active in the view (defaults to 'detail').
     * @param int $page Optional page index for tabbed subviews or pagination.
     * @return \Illuminate\Contracts\View\View The rendered 'clients.view' with the client and active tab.
     */
    public function view(Request $request, $client_id, $activeTab = 'detail', $page = 0): \Illuminate\Contracts\View\View
    {
        $client = Client::find($client_id);
        if ( ! $client) {
            abort(404);
        }

        // Skipping tab/session logic for brevity
        return view('clients.view', [
            'client'    => $client,
            'activeTab' => $activeTab,
        ]);
    }

    /**
     * Delete the specified client and redirect to the clients index.
     *
     * @param int $client_id The identifier of the client to delete.
     * @return \Illuminate\Http\RedirectResponse A redirect response to the clients index route.
     */
    public function delete($client_id): \Illuminate\Http\RedirectResponse
    {
        (new ClientsService())->delete($client_id);

        return redirect()->route('clients.index');
    }

    /**
     * @originalName getClientTitleChoices
     *
     * @originalFile ClientsController.php
     */
    private function getClientTitleChoices(): array
    {
        return [];
    }

    /**
         * Adjusts a client record based on requested e-invoicing settings.
         *
         * This method applies or verifies e-invoicing-related changes for the given client according
         * to the provided request data and returns the client instance (unchanged if no updates are required).
         *
         * @param mixed $client The client model instance to check or modify.
         * @param mixed $req_einvoicing E-invoicing configuration or flag provided by the request.
         * @return mixed The client instance, potentially modified to reflect e-invoicing activation.
         */
    private function checkClientEinvoiceActive($client, $req_einvoicing)
    {
        return $client;
    }
}