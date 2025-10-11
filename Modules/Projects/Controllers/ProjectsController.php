<?php

namespace Modules\Projects\Controllers;

use AllowDynamicProperties;
use Modules\Clients\Services\ClientsService;
use Modules\Core\Controllers\AdminController;
use Modules\Projects\Services\ProjectsService;
use Modules\Tasks\Services\TasksService;

#[AllowDynamicProperties]
class ProjectsController extends AdminController
{
    /**
     * ProjectsController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_projects');
    }

    /**
     * @originalName index
     *
     * @originalFile ProjectsController.php
     */
    public function index($page = 0)
    {
        (new ProjectsService())->paginate(site_url('projects/index'), $page);
        $projects = (new ProjectsService())->result();

        return view('projects.index', ['filter_display' => true, 'filter_placeholder' => trans('filter_projects'), 'filter_method' => 'filter_projects', 'projects' => $projects]);
    }

    /**
     * Display and process the project create/edit form.
     *
     * Validates and saves submitted project data, handles cancel redirects, and prepares
     * project and active clients data for rendering the form view.
     *
     * @param int|null $id Project ID to edit, or null to create a new project.
     * @return mixed Rendered view for the project form, or a redirect/response issued after submit or cancel.
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect()->route('projects');
        }
        $this->filterInput();
        // <<<--- filters _POST array for nastiness
        if ((new ProjectsService())->runValidation()) {
            (new ProjectsService())->save($id);
            redirect()->route('projects');
        }
        if ($id && ! $this->input->post('btn_submit') && ! (new ProjectsService())->prepForm($id)) {
            show_404();
        }
        $this->load->model('clients/mdl_clients');

// File: Modules/Projects/Controllers/ProjectsController.php

use Modules\Clients\Services\ClientsService;
use Modules\Projects\Services\ProjectsService;
use Modules\Tasks\Services\TasksService;

class ProjectsController extends BaseController
{
    /**
     * Initialize the controller by assigning injected services and loading the projects model.
     *
     * Loads the `mdl_projects` model and makes ClientsService, ProjectsService, and TasksService
     * available to the controller via constructor property promotion.
     */
    public function __construct(
        private ClientsService $clientsService,
        private ProjectsService $projectsService,
        private TasksService $tasksService
    ) {
        parent::__construct();
        $this->load->model('mdl_projects');
    }

    /**
     * Render the project creation/edit form populated with the specified project and active clients.
     *
     * @param int $id The project identifier to edit.
     * @return string The rendered view content for the projects form.
     */

    public function form(int $id)
    {
        // …
        return view('projects.form', [
            'project' => $this->projectsService->getById($id),
            'clients' => $this->clientsService->getActive()
        ]);
    }

    // …
}
    }

    /**
     * @originalName view
     *
     * @originalFile ProjectsController.php
     */
    public function view($project_id)
    {
        if ($this->input->post('btn_cancel')) {
            redirect()->route('projects');
        }
        $this->load->model('projects/mdl_projects');
        $project = (new ProjectsService())->getById($project_id);
        if ( ! $project) {
            show_404();
        }
        $this->load->model('tasks/mdl_tasks');

        return view('projects.view', ['project' => $project, 'tasks' => (new ProjectsService())->getTasks($project->project_id), 'task_statuses' => (new TasksService())->statuses()]);
    }

    /**
     * @originalName delete
     *
     * @originalFile ProjectsController.php
     */
    public function delete($id)
    {
        $this->load->model('tasks/mdl_tasks');
        (new TasksService())->updateOnProjectDelete($id);
        (new ProjectsService())->delete($id);
        redirect()->route('projects');
    }
}