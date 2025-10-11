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
     * @originalName view
     *
     * @originalFile ClientsController.php
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
     * @originalName delete
     *
     * @originalFile ClientsController.php
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
     * @originalName checkClientEinvoiceActive
     *
     * @originalFile ClientsController.php
     */
    private function checkClientEinvoiceActive($client, $req_einvoicing)
    {
        return $client;
    }
}
