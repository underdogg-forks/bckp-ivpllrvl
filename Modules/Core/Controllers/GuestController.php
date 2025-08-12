<?php
use Modules\Core\Controllers\AdminController;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Controllers\GuestController;
use Modules\Core\Controllers\UserController;
use Modules\Core\Models\BaseModel;
use Modules\Core\Models\FormValidationModel;
use Modules\Core\Models\MyModel;
use Modules\Core\Models\ResponseModel;


namespace Modules\Core\Controllers;

use AllowDynamicProperties;
/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */
#[AllowDynamicProperties]
class GuestController extends UserController
{
    /** @var array */
    public $user_clients = [];
    /**
     * Modules\Core\Controllers\Guest_Controller constructor.
     */
    public function __construct()
    {
        parent::__construct('user_type', 2);
        $this->load->model('user_clients/mdl_user_clients');
        $user_clients = $this->mdl_user_clients->assignedTo($this->session->userdata('user_id'))->get()->result();
        if (!$user_clients) {
            show_error(trans('guest_account_denied'), 403);
            exit;
        }
        foreach ($user_clients as $user_client) {
            $this->user_clients[$user_client->client_id] = $user_client->client_id;
        }
    }
}
