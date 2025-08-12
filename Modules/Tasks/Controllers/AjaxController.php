<?php

namespace Modules\Tasks\Controllers;

use AllowDynamicProperties;
use Modules\Core\Controllers\AdminController;

#[AllowDynamicProperties]
class AjaxController extends AdminController
{
    /**
     * @originalName modalTaskLookups
     *
     * @originalFile AjaxController.php
     */
    public function modalTaskLookups($invoice_id = null)
    {
        $default_item_tax_rate = get_setting('default_item_tax_rate');
        $data                  = ['default_item_tax_rate' => $default_item_tax_rate !== '' ?: 0, 'tasks' => []];
        if ( ! empty($invoice_id)) {
            $this->load->model('mdl_tasks');
            $data['tasks'] = $this->mdl_tasks->getTasksToInvoice($invoice_id);
        }
        $this->layout->loadView('tasks/modal_task_lookups', $data);
    }

    /**
     * @originalName processTaskSelections
     *
     * @originalFile AjaxController.php
     */
    public function processTaskSelections()
    {
        $this->load->model('mdl_tasks');
        $tasks = $this->mdl_tasks->where_in('task_id', $this->input->post('task_ids'))->get()->result();
        foreach ($tasks as $task) {
            $task->task_price = format_amount($task->task_price);
        }
        echo json_encode($tasks);
    }
}
