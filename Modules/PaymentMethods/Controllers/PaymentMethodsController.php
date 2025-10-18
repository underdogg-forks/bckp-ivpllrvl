<?php

namespace Modules\PaymentMethods\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;
use Modules\PaymentMethods\Services\PaymentMethodsService;

#[AllowDynamicProperties]
class PaymentMethodsController extends AdminController
{
    /**
     * Initialize the PaymentMethodsController.
     *
     * Ensures base AdminController initialization is executed.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @originalName index
     *
     * @originalFile PaymentMethodsController.php
     */
    public function index($page = 0): View
    {
        (new PaymentMethodsService())->paginate(site_url('payment_methods/index'), $page);
        $payment_methods = (new PaymentMethodsService())->result();
        return view('payment_methods.index', ['payment_methods' => $payment_methods]);
    }

    /**
     * @originalName form
     *
     * @originalFile PaymentMethodsController.php
     */
    public function form(Request $request, $id = null): View|RedirectResponse
    {
        if ($request->post('btn_cancel')) {
            return redirect()->route('payment_methods');
        }
        $this->filterInput();
        // <<<--- filters _POST array for nastiness
        if ($request->post('is_update') == 0 && $request->post('payment_method_name') != '') {
            $exists = DB::table('ip_payment_methods')
                ->where('payment_method_name', $request->post('payment_method_name'))
                ->exists();
            if ($exists) {
                session()->flash('alert_error', trans('payment_method_already_exists'));
                return redirect()->route('payment_methods.form');
            }
        }
        if ((new PaymentMethodsService())->runValidation(null, $request)) {
            (new PaymentMethodsService())->save($request, $id);
            return redirect()->route('payment_methods');
        }
        if ($id && ! $request->post('btn_submit')) {
            if ( ! (new PaymentMethodsService())->prepForm($id)) {
                abort(404);
            }
            (new PaymentMethodsService())->setFormValue('is_update', true);
        }
        return view('payment_methods.form');
    }

    /**
     * Delete a payment method and redirect to the payment methods index.
     *
     * Deletes the payment method identified by the given ID, then redirects to the
     * payment methods listing route.
     *
     * @param int|string $id the identifier of the payment method to delete
     */
    public function delete($id): RedirectResponse
    {
        (new PaymentMethodsService())->delete($id);
        return redirect()->route('payment_methods');
    }
}
