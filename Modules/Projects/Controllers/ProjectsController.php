<?php

namespace Modules\Projects\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;

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
        $this->mdl_projects->paginate(site_url('projects/index'), $page);
        $projects = $this->mdl_projects->result();
        $this->layout->set(['filter_display' => true, 'filter_placeholder' => trans('filter_projects'), 'filter_method' => 'filter_projects', 'projects' => $projects]);
        $this->layout->buffer('content', 'projects/index');
        $this->layout->render();
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
        if ($this->mdl_projects->runValidation()) {
            $this->mdl_projects->save($id);
            redirect()->route('projects');
        }
        if ($id && ! $this->input->post('btn_submit') && ! $this->mdl_projects->prepForm($id)) {
            show_404();
        }
        $this->load->model('clients/mdl_clients');
        $this->layout->set(['project' => $this->mdl_projects->getById($id), 'clients' => $this->mdl_clients->where('client_active', 1)->get()->result()]);
        $this->layout->buffer('content', 'projects/form');
        $this->layout->render();
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
        $project = $this->mdl_projects->getById($project_id);
        if ( ! $project) {
            show_404();
        }
        $this->load->model('tasks/mdl_tasks');
        $this->layout->set(['project' => $project, 'tasks' => $this->mdl_projects->getTasks($project->project_id), 'task_statuses' => $this->mdl_tasks->statuses()]);
        $this->layout->buffer('content', 'projects/view');
        $this->layout->render();
    }

    /**
     * @originalName delete
     *
     * @originalFile ProjectsController.php
     */
    public function delete($id)
    {
        $this->load->model('tasks/mdl_tasks');
        $this->mdl_tasks->updateOnProjectDelete($id);
        $this->mdl_projects->delete($id);
        redirect()->route('projects');
    }
}
