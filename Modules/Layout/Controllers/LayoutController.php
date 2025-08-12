<?php
use Modules\Core\Controllers\AdminController;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Controllers\GuestController;
use Modules\Core\Controllers\UserController;
use Modules\Core\Models\BaseModel;
use Modules\Core\Models\FormValidationModel;
use Modules\Core\Models\MyModel;
use Modules\Core\Models\ResponseModel;


namespace Modules\Layout\Controllers;

use MX_Controller;
/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */
#[AllowDynamicProperties]
class LayoutController extends MX_Controller
{
    public $view_data = [];
    /**
     * @originalName buffer
     *
     * @originalFile LayoutController.php
     */
    public function buffer(...$args): static
    {
        if (count($args) == 1) {
            foreach ($args[0] as $arg) {
                $key = $arg[0];
                $view = explode('/', $arg[1]);
                $data = array_merge($arg[2] ?? [], $this->view_data);
                $this->view_data[$key] = $this->load->view($view[0] . '/' . $view[1], $data, true);
            }
        } else {
            $key = $args[0];
            $view = explode('/', $args[1]);
            $data = array_merge($args[2] ?? [], $this->view_data);
            $this->view_data[$key] = $this->load->view($view[0] . '/' . $view[1], $data, true);
        }
        return $this;
    }
    /**
     * @originalName set
     *
     * @originalFile LayoutController.php
     */
    public function set(...$args): static
    {
        if (count($args) == 1) {
            foreach ($args[0] as $key => $value) {
                $this->view_data[$key] = $value;
            }
        } else {
            $this->view_data[$args[0]] = $args[1];
        }
        return $this;
    }
    /**
     * @originalName render
     *
     * @originalFile LayoutController.php
     */
    public function render(string $view = 'layout')
    {
        $this->load->view('layout/' . $view, $this->view_data);
    }
    /**
     * @originalName loadView
     *
     * @originalFile LayoutController.php
     */
    public function loadView($view, $data = [])
    {
        $view = explode('/', $view);
        $view = $view[0] . '/' . $view[1] . (isset($view[2]) ? '/' . $view[2] : '');
        $this->load->view($view, $data);
    }
}
