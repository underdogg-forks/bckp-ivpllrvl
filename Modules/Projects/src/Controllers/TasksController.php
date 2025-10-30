<?php

namespace Modules\Projects\app\Http\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;
use Modules\Projects\app\Services\TasksService;
use Modules\Projects\Services\ProjectsService;

use function Modules\Tasks\Controllers\redirect;
use function Modules\Tasks\Controllers\show_404;
use function Modules\Tasks\Controllers\site_url;
use function Modules\Tasks\Controllers\view;

use src\Services\TaxRatesService;

#[AllowDynamicProperties]
class TasksController extends AdminController
{
    /**
     * Initialize the TasksController.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display the tasks index page with paginated tasks and related view data.
     *
     * Prepares pagination for the tasks list, retrieves the current page of tasks
     * and task statuses, and returns the rendered tasks index view including
     * filter configuration.
     *
     * @param int $page the page number to display (zero-based)
     *
     * @return string|\CodeIgniter\HTTP\Response the rendered tasks index view
     */
    public function index($page = 0)
    {
        (new TasksService())->paginate(site_url('tasks/index'), $page);
        $tasks = (new TasksService())->result();

        return view('tasks.index', ['filter_display' => true, 'filter_placeholder' => trans('filter_tasks'), 'filter_method' => 'filter_tasks', 'tasks' => $tasks, 'task_statuses' => (new TasksService())->statuses()]);
    }

    /**
     * Show the task create/edit form and handle its submission.
     *
     * If the cancel action is posted, redirects to the tasks list. If the form is submitted and validates,
     * saves the task and redirects to the tasks list. When opening the form for an existing task, a missing
     * task triggers a 404.
     *
     * @param int|null $id the task ID to edit, or null to create a new task
     *
     * @return string the rendered task form view HTML
     */
    public function form($id = null)
    {
        if (request()->input('btn_cancel')) {
            redirect()->route('tasks');
        }
        $this->filterInput();
        // <<<--- filters _POST array for nastiness
        if ((new TasksService())->runValidation()) {
            (new TasksService())->save($id);
            redirect()->route('tasks');
        }
        if ( ! request()->input('btn_submit')) {
            $prep_form = (new TasksService())->prepForm($id);
            if ($id && ! $prep_form) {
                show_404();
            }
        }

        return view('tasks.form', ['projects' => (new ProjectsService())->get()->result(), 'task_statuses' => (new TasksService())->statuses(), 'tax_rates' => (new TaxRatesService())->get()->result()]);
    }

    /**
     * Delete the task identified by the given id and redirect to the tasks list.
     *
     * @param int|string $id the identifier of the task to delete
     */
    public function delete($id)
    {
        (new TasksService())->delete($id);
        redirect()->route('tasks');
    }
}
