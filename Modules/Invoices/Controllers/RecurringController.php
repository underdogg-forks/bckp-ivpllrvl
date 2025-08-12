<?php
use Modules\Core\Controllers\AdminController;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Controllers\GuestController;
use Modules\Core\Controllers\UserController;
use Modules\Core\Models\BaseModel;
use Modules\Core\Models\FormValidationModel;
use Modules\Core\Models\MyModel;
use Modules\Core\Models\ResponseModel;


namespace Modules\Invoices\Controllers;

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */
#[AllowDynamicProperties]
class RecurringController extends AdminController
{
    /**
     * RecurringController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_invoices_recurring');
    }
    /**
     * @originalName index
     *
     * @originalFile RecurringController.php
     */
    public function index($page = 0)
    {
        $this->mdl_invoices_recurring->paginate(site_url('invoices/recurring'), $page);
        $recurring_invoices = $this->mdl_invoices_recurring->result();
        $this->layout->set(['filter_display' => true, 'filter_placeholder' => trans('filter_invoices_recuring'), 'filter_method' => 'filter_invoices_recuring', 'recur_frequencies' => $this->mdl_invoices_recurring->recur_frequencies, 'recurring_invoices' => $recurring_invoices]);
        $this->layout->buffer('content', 'invoices/index_recurring');
        $this->layout->render();
    }
    /**
     * @originalName stop
     *
     * @originalFile RecurringController.php
     */
    public function stop($invoice_recurring_id)
    {
        $this->mdl_invoices_recurring->stop($invoice_recurring_id);
        redirect('invoices/recurring/index');
    }
    /**
     * @originalName delete
     *
     * @originalFile RecurringController.php
     */
    public function delete($invoice_recurring_id)
    {
        $this->mdl_invoices_recurring->delete($invoice_recurring_id);
        redirect('invoices/recurring/index');
    }
}
