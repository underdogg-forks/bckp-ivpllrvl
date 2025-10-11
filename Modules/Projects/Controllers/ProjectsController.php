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
     * @originalName form
     *
     * @originalFile ProjectsController.php
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
    public function __construct(
        private ClientsService $clientsService,
        private ProjectsService $projectsService,
        private TasksService $tasksService
    ) {
        parent::__construct();
        $this->load->model('mdl_projects');
    }

    // …

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
