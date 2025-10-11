<?php

namespace Modules\Tasks\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;
use Modules\Projects\Services\ProjectsService;
use Modules\Tasks\Services\TasksService;
use Modules\TaxRates\Services\TaxRatesService;

#[AllowDynamicProperties]
class TasksController extends AdminController
{
    /**
     * TasksController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @originalName index
     *
     * @originalFile TasksController.php
     */
    public function index($page = 0)
    {
        (new TasksService())->paginate(site_url('tasks/index'), $page);
        $tasks = (new TasksService())->result();

        return view('tasks.index', ['filter_display' => true, 'filter_placeholder' => trans('filter_tasks'), 'filter_method' => 'filter_tasks', 'tasks' => $tasks, 'task_statuses' => (new TasksService())->statuses()]);
    }

    /**
     * @originalName form
     *
     * @originalFile TasksController.php
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect()->route('tasks');
        }
        $this->filterInput();
        // <<<--- filters _POST array for nastiness
        if ((new TasksService())->runValidation()) {
            (new TasksService())->save($id);
            redirect()->route('tasks');
        }
        if ( ! $this->input->post('btn_submit')) {
            $prep_form = (new TasksService())->prepForm($id);
            if ($id && ! $prep_form) {
                show_404();
            }
        }

        return view('tasks.form', ['projects' => (new ProjectsService())->get()->result(), 'task_statuses' => (new TasksService())->statuses(), 'tax_rates' => (new TaxRatesService())->get()->result()]);
    }

    /**
     * @originalName delete
     *
     * @originalFile TasksController.php
     */
    public function delete($id)
    {
        (new TasksService())->delete($id);
        redirect()->route('tasks');
    }
}
