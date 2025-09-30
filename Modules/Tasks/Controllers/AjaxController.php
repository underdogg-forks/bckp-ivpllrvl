<?php

namespace Modules\Tasks\Controllers;

use AllowDynamicProperties;
use Illuminate\Http\Request;
use Modules\Core\Controllers\AdminController;
use Modules\Tasks\Services\TasksService;

#[AllowDynamicProperties]
class AjaxController extends AdminController
{
    /**
     * @originalName modalTaskLookups
     *
     * @originalFile AjaxController.php
     * 
     * Note: Creates new TasksService instance instead of dependency injection.
     * This is acceptable for single usage within the method.
     */
    public function modalTaskLookups($invoice_id = null)
    {
        $default_item_tax_rate = get_setting('default_item_tax_rate');
        $data                  = ['default_item_tax_rate' => $default_item_tax_rate !== '' ?: 0, 'tasks' => []];
        if ( ! empty($invoice_id)) {
            $data['tasks'] = (new TasksService())->getTasksToInvoice($invoice_id);
        }
        return view('tasks.modal_task_lookups', $data);
    }

    /**
     * @originalName processTaskSelections
     *
     * @originalFile AjaxController.php
     * 
     * Note: Creates new TasksService instance instead of dependency injection.
     * This is acceptable for single usage within the method.
     */
    public function processTaskSelections(Request $request): void
    {
        $taskIds = $request->input('task_ids', []);
        $tasks = (new TasksService())->query()->whereIn('task_id', $taskIds)->get();
        foreach ($tasks as $task) {
            $task->task_price = format_amount($task->task_price);
        }
        echo json_encode($tasks);
    }
}
