<?php

namespace Modules\Layout\Controllers;

use AllowDynamicProperties;
use App\Http\Controllers\Controller as MXController;

#[AllowDynamicProperties]
class LayoutController extends MXController
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
                $key                   = $arg[0];
                $view                  = explode('/', $arg[1]);
                $data                  = array_merge($arg[2] ?? [], $this->view_data);
                $this->view_data[$key] = echo view($view[0] . '/' . $view[1], $data, true)->render();
            }
        } else {
            $key                   = $args[0];
            $view                  = explode('/', $args[1]);
            $data                  = array_merge($args[2] ?? [], $this->view_data);
            $this->view_data[$key] = echo view($view[0] . '/' . $view[1], $data, true)->render();
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
        echo view('layout/' . $view, $this->view_data)->render();
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
        echo view($view, $data)->render();
    }
}
