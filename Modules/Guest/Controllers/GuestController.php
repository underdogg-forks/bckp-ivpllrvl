<?php
use Modules\Core\Controllers\AdminController;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Controllers\GuestController;
use Modules\Core\Controllers\UserController;
use Modules\Core\Models\BaseModel;
use Modules\Core\Models\FormValidationModel;
use Modules\Core\Models\MyModel;
use Modules\Core\Models\ResponseModel;


namespace Modules\Guest\Controllers;

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */
#[AllowDynamicProperties]
class GuestController extends GuestController
{
    /**
     * @originalName index
     *
     * @originalFile GuestController.php
     */
    public function index()
    {
        $this->load->model(['quotes/mdl_quotes', 'invoices/mdl_invoices']);
        $this->layout->set(['overdue_invoices' => $this->mdl_invoices->isOverdue()->where_in('ip_invoices.client_id', $this->user_clients)->get()->result(), 'open_quotes' => $this->mdl_quotes->isOpen()->where_in('ip_quotes.client_id', $this->user_clients)->get()->result(), 'open_invoices' => $this->mdl_invoices->isOpen()->where_in('ip_invoices.client_id', $this->user_clients)->get()->result(), 'enable_online_payments' => get_setting('enable_online_payments')]);
        $this->layout->buffer('content', 'guest/index');
        $this->layout->render('layout_guest');
    }
}
