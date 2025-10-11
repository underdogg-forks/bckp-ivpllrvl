<?php

namespace Modules\Tasks\Services;

use AllowDynamicProperties;
use Modules\Core\Services\BaseService;
use Modules\Invoices\Models\Item;
use Modules\Tasks\Models\Task;

#[AllowDynamicProperties]
class TasksService extends BaseService
{
    public $table = 'ip_tasks';

    public $primary_key = 'ip_tasks.task_id';

    /**
     * @originalName defaultSelect
     *
     * @originalFile Task.php
     */
    public function defaultSelect()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *,
          (CASE WHEN DATEDIFF(NOW(), task_finish_date) > 0 THEN 1 ELSE 0 END) is_overdue
        ', false);
    }

    /**
     * @originalName defaultOrderBy
     *
     * @originalFile Task.php
     */
    public function defaultOrderBy()
    {
        $this->db->orderBy('ip_projects.project_name, ip_tasks.task_name');
    }

    /**
     * @originalName defaultJoin
     *
     * @originalFile Task.php
     */
    public function defaultJoin()
    {
        $this->db->join('ip_projects', 'ip_projects.project_id = ip_tasks.project_id', 'left');
    }

    /**
     * @originalName getLatest
     *
     * @originalFile Task.php
     */
    public function getLatest()
    {
        $this->db->orderBy('ip_tasks.task_id', 'DESC');

        return $this;
    }

    /**
     * @originalName byTask
     *
     * @originalFile Task.php
     */
    public function byTask($match)
    {
        $this->db->like('task_name', $match);
        $this->db->or_like('task_description', $match);
    }

    /**
     * @originalName validationRules
     *
     * @originalFile Task.php
     */
    public function validationRules()
    {
        return ['task_name' => ['field' => 'task_name', 'label' => trans('task_name'), 'rules' => 'required'], 'task_description' => ['field' => 'task_description', 'label' => trans('task_description')], 'task_price' => ['field' => 'task_price', 'label' => trans('task_price'), 'rules' => 'required'], 'task_finish_date' => ['field' => 'task_finish_date', 'label' => trans('task_finish_date'), 'rules' => 'required'], 'project_id' => ['field' => 'project_id', 'label' => trans('project')], 'task_status' => ['field' => 'task_status', 'label' => lang('status')], 'tax_rate_id' => ['field' => 'tax_rate_id', 'label' => lang('tax_rate'), 'rules' => 'numeric']];
    }

    /**
     * @originalName dbArray
     *
     * @originalFile Task.php
     */
    public function dbArray()
    {
        $db_array                     = parent::dbArray();
        $db_array['task_finish_date'] = date_to_mysql($db_array['task_finish_date']);
        $db_array['task_price']       = standardize_amount($db_array['task_price']);

        return $db_array;
    }

    /**
     * @originalName prepForm
     *
     * @originalFile Task.php
     */
    public function prepForm($id = null): bool
    {
        if ( ! parent::prepForm($id)) {
            return false;
        }
        if ( ! $id) {
            parent::setFormValue('task_finish_date', date('Y-m-d'));
            parent::setFormValue('task_price', get_setting('default_hourly_rate'));
        }

        return true;
    }

    /**
     * @originalName getInvoiceForTask
     *
     * @originalFile Task.php
     */
    public function getInvoiceForTask($task_id)
    {
        if ( ! $task_id) {
            return;
        }
        $invoice_item = Item::query()
            ->select('invoice_id')
            ->where('item_task_id', $task_id)
            ->first();
            
        if (empty($invoice_item) || ! isset($invoice_item->invoice_id)) {
            return;
        }
        $this->load->model('invoices/mdl_invoices');

        return $this->mdl_invoices->getById($invoice_item->invoice_id);
    }

    /**
     * @originalName getTasksToInvoice
     *
     * @originalFile Task.php
     */
    public function getTasksToInvoice($invoice_id)
    {
        $result = [];
        if ( ! $invoice_id) {
            return $result;
        }
        // GetController tasks without any project
        $tasks = Task::query()
            ->where('project_id', 0)
            ->where('task_status', 3)
            ->orderBy('task_finish_date', 'ASC')
            ->orderBy('task_name', 'ASC')
            ->get();
        foreach ($tasks as $row) {
            $result[] = $row;
        }
        // GetController tasks for this invoice
        $tasks = Task::query()
            ->select('ip_tasks.*', 'ip_projects.project_name')
            ->join('ip_projects', 'ip_projects.project_id', '=', 'ip_tasks.project_id')
            ->join('ip_invoices', 'ip_invoices.client_id', '=', 'ip_projects.client_id')
            ->where('ip_invoices.invoice_id', $invoice_id)
            ->where('ip_tasks.task_status', 3)
            ->orderBy('ip_tasks.task_finish_date', 'ASC')
            ->orderBy('ip_projects.project_name', 'ASC')
            ->orderBy('ip_tasks.task_name', 'ASC')
            ->get();
        foreach ($tasks as $row) {
            $result[] = $row;
        }

        return $result;
    }

    /**
     * @originalName updateOnInvoiceDelete
     *
     * @originalFile Task.php
     */
    public function updateOnInvoiceDelete($invoice_id)
    {
        if ( ! $invoice_id) {
            return;
        }
        $tasks = Task::query()
            ->join('ip_invoice_items', 'ip_invoice_items.item_task_id', '=', 'ip_tasks.task_id')
            ->where('ip_invoice_items.invoice_id', $invoice_id)
            ->get();
        foreach ($tasks as $task) {
            $this->updateStatus(3, $task->task_id);
        }
    }

    /**
     * @originalName updateStatus
     *
     * @originalFile Task.php
     */
    public function updateStatus($new_status, $task_id)
    {
        $statuses_ok = $this->statuses();
        if (isset($statuses_ok[$new_status])) {
            parent::save($task_id, ['task_status' => $new_status]);
        }
    }

    /**
     * @originalName statuses
     *
     * @originalFile Task.php
     */
    public function statuses()
    {
        return ['1' => ['label' => trans('not_started'), 'class' => 'draft'], '2' => ['label' => trans('in_progress'), 'class' => 'viewed'], '3' => ['label' => trans('complete'), 'class' => 'sent'], '4' => ['label' => trans('invoiced'), 'class' => 'paid']];
    }

    /**
     * @originalName updateOnProjectDelete
     *
     * @originalFile Task.php
     */
    public function updateOnProjectDelete($project_id)
    {
        if ( ! $project_id) {
            return;
        }
        $tasks = Task::query()->where('project_id', $project_id)->get();
        foreach ($tasks as $task) {
            parent::save($task->task_id, ['project_id' => null]);
        }
    }
}
