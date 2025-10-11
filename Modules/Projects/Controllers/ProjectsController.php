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
     * Initialize the ProjectsController and perform the parent controller setup.
     */
    public function __construct()
    {
        parent::__construct();
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
     * Display and process the project creation/edit form.
     *
     * Processes form submission, validates and saves project data, and renders the project form populated with the project and active clients when not redirected.
     *
     * @param int|null $id The project identifier to edit, or null to create a new project.
     * @return string The rendered HTML of the project form view.
     *
     * Note: this method may redirect to the projects list on cancel or after a successful save, and will trigger a 404 response if the provided `$id` cannot be prepared for editing.
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

        return view('projects.form', ['project' => (new ProjectsService())->getById($id), 'clients' => (new ClientsService())->where('client_active', 1)->get()->result()]);
    }

    /**
     * Display a project's details along with its tasks and available task statuses.
     *
     * @param int|string $project_id The identifier of the project to display.
     * @return \CodeIgniter\HTTP\RedirectResponse|\CodeIgniter\HTTP\ResponseInterface|string The view response for the project page or a redirect response when cancelling.
     */
    public function view($project_id)
    {
        if ($this->input->post('btn_cancel')) {
            redirect()->route('projects');
        }
        $project = (new ProjectsService())->getById($project_id);
        if ( ! $project) {
            show_404();
        }

        return view('projects.view', ['project' => $project, 'tasks' => (new ProjectsService())->getTasks($project->project_id), 'task_statuses' => (new TasksService())->statuses()]);
    }

    /**
     * Delete a project and update its associated tasks.
     *
     * Deletes the project identified by $id and updates tasks that reference the project so they no longer do.
     *
     * @param int|string $id The ID of the project to delete.
     */
    public function delete($id)
    {
        (new TasksService())->updateOnProjectDelete($id);
        (new ProjectsService())->delete($id);
        redirect()->route('projects');
    }
}