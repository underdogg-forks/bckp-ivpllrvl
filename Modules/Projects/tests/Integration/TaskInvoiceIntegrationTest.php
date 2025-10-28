<?php

namespace Modules\Projects\tests\Integration;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Clients\Models\tmpClient;
use Modules\Invoices\Models\Invoice;
use Modules\Invoices\Models\Item;
use Modules\Projects\app\Models\Task;
use Modules\Projects\app\Services\TasksService;
use Modules\Projects\Models\Project;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use function Modules\Tasks\Tests\Integration\now;

/**
 * Integration tests for Task-Invoice workflows.
 */
class TaskInvoiceIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private TasksService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TasksService();
    }

    #[Test]
    public function complete_task_to_invoice_workflow(): void
    {
        // Arrange: Create client, project, and tasks
        $client = tmpClient::create([
            'client_name'   => 'Test Client',
            'client_active' => 1,
        ]);

        $project = Project::create([
            'project_name' => 'Client Project',
            'client_id'    => $client->client_id,
        ]);

        $invoice = Invoice::create([
            'client_id'         => $client->client_id,
            'invoice_status_id' => 1,
        ]);

        // Create completed tasks for the project
        Task::create([
            'task_name'        => 'Task 1',
            'task_description' => 'Description 1',
            'task_status'      => 3, // Completed
            'project_id'       => $project->project_id,
            'task_finish_date' => now()->subDays(2),
        ]);

        Task::create([
            'task_name'        => 'Task 2',
            'task_description' => 'Description 2',
            'task_status'      => 3, // Completed
            'project_id'       => $project->project_id,
            'task_finish_date' => now()->subDay(),
        ]);

        // Create a non-completed task (should not appear)
        Task::create([
            'task_name'        => 'Incomplete Task',
            'task_description' => 'Description',
            'task_status'      => 1, // In progress
            'project_id'       => $project->project_id,
        ]);

        // Act: Get tasks eligible for invoicing
        $tasksToInvoice = $this->service->getTasksToInvoice($invoice->invoice_id);

        // Assert: Only completed tasks should be returned
        $this->assertCount(2, $tasksToInvoice);
        $taskNames = array_map(fn ($t) => $t->task_name, $tasksToInvoice);
        $this->assertContains('Task 1', $taskNames);
        $this->assertContains('Task 2', $taskNames);
        $this->assertNotContains('Incomplete Task', $taskNames);
    }

    #[Test]
    public function task_invoice_association_retrieval(): void
    {
        // Arrange: Create a task and link it to an invoice
        $client = tmpClient::create([
            'client_name'   => 'Test Client',
            'client_active' => 1,
        ]);

        $invoice = Invoice::create([
            'client_id'         => $client->client_id,
            'invoice_status_id' => 1,
        ]);

        $task = Task::create([
            'task_name'        => 'Billable Task',
            'task_description' => 'Description',
            'task_status'      => 3,
        ]);

        // Create invoice item linking task to invoice
        Item::create([
            'invoice_id'    => $invoice->invoice_id,
            'item_task_id'  => $task->task_id,
            'item_name'     => 'Task Item',
            'item_quantity' => 1,
            'item_price'    => 100.00,
        ]);

        // Act: Retrieve invoice for the task
        $result = $this->service->getInvoiceForTask($task->task_id);

        // Assert: Should return the invoice
        $this->assertNotNull($result);
    }

    #[Test]
    public function deleting_invoice_updates_associated_tasks(): void
    {
        // Arrange: Create invoice with linked tasks
        $client = tmpClient::create([
            'client_name'   => 'Test Client',
            'client_active' => 1,
        ]);

        $invoice = Invoice::create([
            'client_id'         => $client->client_id,
            'invoice_status_id' => 1,
        ]);

        $task1 = Task::create([
            'task_name'        => 'Task 1',
            'task_description' => 'Description 1',
            'task_status'      => 1, // Not complete
        ]);

        $task2 = Task::create([
            'task_name'        => 'Task 2',
            'task_description' => 'Description 2',
            'task_status'      => 2, // In progress
        ]);

        // Link tasks to invoice
        Item::create([
            'invoice_id'    => $invoice->invoice_id,
            'item_task_id'  => $task1->task_id,
            'item_name'     => 'Task 1 Item',
            'item_quantity' => 1,
            'item_price'    => 100.00,
        ]);

        Item::create([
            'invoice_id'    => $invoice->invoice_id,
            'item_task_id'  => $task2->task_id,
            'item_name'     => 'Task 2 Item',
            'item_quantity' => 1,
            'item_price'    => 150.00,
        ]);

        // Act: Simulate invoice deletion
        $this->service->updateOnInvoiceDelete($invoice->invoice_id);

        // Assert: Tasks should be marked as complete (status 3)
        $task1->refresh();
        $task2->refresh();
        $this->assertEquals(3, $task1->task_status);
        $this->assertEquals(3, $task2->task_status);
    }

    #[Test]
    public function deleting_project_clears_task_associations(): void
    {
        // Arrange: Create project with multiple tasks
        $project = Project::create([
            'project_name' => 'Test Project',
        ]);

        $task1 = Task::create([
            'task_name'        => 'Task 1',
            'task_description' => 'Description 1',
            'task_status'      => 1,
            'project_id'       => $project->project_id,
        ]);

        $task2 = Task::create([
            'task_name'        => 'Task 2',
            'task_description' => 'Description 2',
            'task_status'      => 1,
            'project_id'       => $project->project_id,
        ]);

        // Create task for different project (should not be affected)
        $otherProject = Project::create([
            'project_name' => 'Other Project',
        ]);
        $task3 = Task::create([
            'task_name'        => 'Task 3',
            'task_description' => 'Description 3',
            'task_status'      => 1,
            'project_id'       => $otherProject->project_id,
        ]);

        // Act: Simulate project deletion
        $this->service->updateOnProjectDelete($project->project_id);

        // Assert: Tasks from deleted project should have null project_id
        $task1->refresh();
        $task2->refresh();
        $task3->refresh();

        $this->assertNull($task1->project_id);
        $this->assertNull($task2->project_id);
        $this->assertEquals($otherProject->project_id, $task3->project_id);
    }
}
