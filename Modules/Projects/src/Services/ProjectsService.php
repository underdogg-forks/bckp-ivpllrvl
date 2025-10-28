<?php

namespace Modules\Projects\Services;

use AllowDynamicProperties;
use Modules\Core\Services\BaseService;

#[AllowDynamicProperties]
class ProjectsService extends BaseService
{
    public $table = 'ip_projects';

    public $primary_key = 'ip_projects.project_id';

    /**
     * @originalName defaultSelect
     *
     * @originalFile Project.php
     */
    public function defaultSelect()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', false);
    }

    /**
     * @originalName defaultOrderBy
     *
     * @originalFile Project.php
     */
    public function defaultOrderBy()
    {
        $this->db->orderBy('ip_projects.project_id');
    }

    /**
     * @originalName defaultJoin
     *
     * @originalFile Project.php
     */
    public function defaultJoin()
    {
        $this->db->join('ip_clients', 'ip_clients.client_id = ip_projects.client_id', 'left');
    }

    /**
     * @originalName getLatest
     *
     * @originalFile Project.php
     */
    public function getLatest()
    {
        $this->db->orderBy('ip_projects.project_id', 'DESC');

        return $this;
    }

    /**
     * @originalName validationRules
     *
     * @originalFile Project.php
     */
    public function validationRules()
    {
        return ['project_name' => ['field' => 'project_name', 'label' => trans('project_name'), 'rules' => 'required'], 'client_id' => ['field' => 'client_id', 'label' => trans('client')]];
    }

    /**
     * Retrieve tasks belonging to the specified project.
     *
     * @param int|string $project_id the project identifier
     *
     * @return \Modules\Projects\app\Models\Task[] an array of Task model instances for the project; empty if no valid project id is provided
     */
    public function getTasks($project_id)
    {
        if ( ! $project_id) {
            return [];
        }

        return \Modules\Projects\app\Models\Task::query()
            ->where('project_id', $project_id)
            ->get()
            ->all();
    }
}
