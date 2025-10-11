<?php

namespace Tests\Unit\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Invoices\Models\Invoice;
use Modules\Invoices\Models\Item;
use Modules\Projects\Models\Project;
use Modules\Tasks\Models\Task;
use Modules\Tasks\Services\TasksService;
use Tests\TestCase;

class TasksServiceTest extends TestCase
{
    use RefreshDatabase;

    private TasksService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TasksService();
    }

    public function test_byTask_filters_by_task_name(): void
    {
        // Arrange: Create tasks with different names
        Task::factory()->create(['task_name' => 'Design Homepage']);
        Task::factory()->create(['task_name' => 'Develop Backend']);
        Task::factory()->create(['task_name' => 'Test Application']);

        // Act: Search for tasks with "Design" in name
        $results = $this->service->byTask('Design')->get();

        // Assert: Should find task with "Design" in name
        $this->assertCount(1, $results);
        $this->assertEquals('Design Homepage', $results->first()->task_name);
    }

    public function test_byTask_filters_by_task_description(): void
    {
        // Arrange: Create tasks with different descriptions
        Task::factory()->create([
            'task_name' => 'Task 1',
            'task_description' => 'Create the user interface',
        ]);
        Task::factory()->create([
            'task_name' => 'Task 2',
            'task_description' => 'Implement database schema',
        ]);

        // Act: Search for tasks with "interface" in description
        $results = $this->service->byTask('interface')->get();

        // Assert: Should find task with "interface" in description
        $this->assertCount(1, $results);
        $this->assertEquals('Task 1', $results->first()->task_name);
    }

    public function test_byTask_filters_by_name_or_description(): void
    {
        // Arrange: Create tasks
        Task::factory()->create([
            'task_name' => 'Database Design',
            'task_description' => 'Design schema',
        ]);
        Task::factory()->create([
            'task_name' => 'Frontend Work',
            'task_description' => 'Create beautiful design',
        ]);
        Task::factory()->create([
            'task_name' => 'Backend API',
            'task_description' => 'Build REST endpoints',
        ]);

        // Act: Search for "design" (appears in name or description)
        $results = $this->service->byTask('design')->get();

        // Assert: Should find both tasks with "design"
        $this->assertCount(2, $results);
    }

    public function test_byTask_returns_empty_for_no_match(): void
    {
        // Arrange: Create tasks
        Task::factory()->create(['task_name' => 'Development', 'task_description' => 'Code']);
        Task::factory()->create(['task_name' => 'Testing', 'task_description' => 'QA']);

        // Act: Search for non-existent term
        $results = $this->service->byTask('NonExistent')->get();

        // Assert: Should return empty collection
        $this->assertCount(0, $results);
    }

    public function test_getInvoiceForTask_returns_null_when_no_task_id(): void
    {
        // Act: Get invoice for null task ID
        $result = $this->service->getInvoiceForTask(null);

        // Assert: Should return null
        $this->assertNull($result);
    }

    public function test_getInvoiceForTask_returns_null_when_task_not_in_invoice(): void
    {
        // Arrange: Create a task not associated with any invoice
        $task = Task::factory()->create();

        // Act: Get invoice for task
        $result = $this->service->getInvoiceForTask($task->task_id);

        // Assert: Should return null
        $this->assertNull($result);
    }

    public function test_getInvoiceForTask_returns_invoice_when_task_is_invoiced(): void
    {
        // Arrange: Create task, invoice, and link them
        $task = Task::factory()->create();
        $invoice = Invoice::factory()->create();
        Item::factory()->create([
            'invoice_id' => $invoice->invoice_id,
            'item_task_id' => $task->task_id,
        ]);

        // Act: Get invoice for task
        $result = $this->service->getInvoiceForTask($task->task_id);

        // Assert: Should return the invoice
        $this->assertNotNull($result);
        $this->assertEquals($invoice->invoice_id, $result->invoice_id);
    }

    public function test_getTasksToInvoice_returns_empty_for_null_invoice_id(): void
    {
        // Act: Get tasks for null invoice
        $result = $this->service->getTasksToInvoice(null);

        // Assert: Should return empty array
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_getTasksToInvoice_includes_completed_tasks_without_project(): void
    {
        // Arrange: Create completed tasks without projects
        Task::factory()->create([
            'project_id' => 0,
            'task_status' => 3, // Completed
            'task_name' => 'Task 1',
        ]);
        Task::factory()->create([
            'project_id' => 0,
            'task_status' => 3, // Completed
            'task_name' => 'Task 2',
        ]);
        
        // Create invoice
        $invoice = Invoice::factory()->create();

        // Act: Get tasks to invoice
        $result = $this->service->getTasksToInvoice($invoice->invoice_id);

        // Assert: Should include both completed tasks
        $this->assertCount(2, $result);
    }

    public function test_getTasksToInvoice_excludes_incomplete_tasks_without_project(): void
    {
        // Arrange: Create tasks with different statuses
        Task::factory()->create([
            'project_id' => 0,
            'task_status' => 1, // Not completed
        ]);
        Task::factory()->create([
            'project_id' => 0,
            'task_status' => 2, // Not completed
        ]);
        
        $invoice = Invoice::factory()->create();

        // Act: Get tasks to invoice
        $result = $this->service->getTasksToInvoice($invoice->invoice_id);

        // Assert: Should not include incomplete tasks
        $this->assertCount(0, $result);
    }

    public function test_getTasksToInvoice_includes_tasks_from_matching_client_projects(): void
    {
        // Arrange: Create client, project, invoice, and tasks
        $client = \Modules\Clients\Models\Client::factory()->create();
        $project = Project::factory()->create(['client_id' => $client->client_id]);
        $invoice = Invoice::factory()->create(['client_id' => $client->client_id]);
        
        $task = Task::factory()->create([
            'project_id' => $project->project_id,
            'task_status' => 3, // Completed
            'task_name' => 'Project Task',
        ]);

        // Act: Get tasks to invoice
        $result = $this->service->getTasksToInvoice($invoice->invoice_id);

        // Assert: Should include the project task
        $this->assertGreaterThan(0, count($result));
        $taskFound = false;
        foreach ($result as $t) {
            if ($t->task_id == $task->task_id) {
                $taskFound = true;
                $this->assertObjectHasProperty('project_name', $t);
                break;
            }
        }
        $this->assertTrue($taskFound);
    }

    public function test_getTasksToInvoice_excludes_tasks_from_different_client_projects(): void
    {
        // Arrange: Create different clients and their projects
        $client1 = \Modules\Clients\Models\Client::factory()->create();
        $client2 = \Modules\Clients\Models\Client::factory()->create();
        
        $project = Project::factory()->create(['client_id' => $client2->client_id]);
        $invoice = Invoice::factory()->create(['client_id' => $client1->client_id]);
        
        Task::factory()->create([
            'project_id' => $project->project_id,
            'task_status' => 3,
        ]);

        // Act: Get tasks to invoice for client1
        $result = $this->service->getTasksToInvoice($invoice->invoice_id);

        // Assert: Should not include tasks from client2's projects
        $projectTasks = array_filter($result, fn($t) => $t->project_id != 0);
        $this->assertCount(0, $projectTasks);
    }

    public function test_updateOnInvoiceDelete_marks_tasks_as_complete(): void
    {
        // Arrange: Create invoice with associated tasks
        $invoice = Invoice::factory()->create();
        $task1 = Task::factory()->create(['task_status' => 4]); // Invoiced
        $task2 = Task::factory()->create(['task_status' => 4]); // Invoiced
        
        Item::factory()->create([
            'invoice_id' => $invoice->invoice_id,
            'item_task_id' => $task1->task_id,
        ]);
        Item::factory()->create([
            'invoice_id' => $invoice->invoice_id,
            'item_task_id' => $task2->task_id,
        ]);

        // Act: Update tasks on invoice delete
        $this->service->updateOnInvoiceDelete($invoice->invoice_id);

        // Assert: Tasks should be marked as complete
        $this->assertDatabaseHas('ip_tasks', [
            'task_id' => $task1->task_id,
            'task_status' => 3, // Complete
        ]);
        $this->assertDatabaseHas('ip_tasks', [
            'task_id' => $task2->task_id,
            'task_status' => 3, // Complete
        ]);
    }

    public function test_updateOnInvoiceDelete_handles_null_invoice_id(): void
    {
        // Act: Try to update with null invoice ID
        $this->service->updateOnInvoiceDelete(null);

        // Assert: Should not throw exception
        $this->assertTrue(true);
    }

    public function test_updateOnInvoiceDelete_handles_zero_invoice_id(): void
    {
        // Act: Try to update with zero invoice ID
        $this->service->updateOnInvoiceDelete(0);

        // Assert: Should not throw exception
        $this->assertTrue(true);
    }

    public function test_updateOnInvoiceDelete_only_affects_tasks_from_specified_invoice(): void
    {
        // Arrange: Create multiple invoices with tasks
        $invoice1 = Invoice::factory()->create();
        $invoice2 = Invoice::factory()->create();
        
        $task1 = Task::factory()->create(['task_status' => 4]);
        $task2 = Task::factory()->create(['task_status' => 4]);
        
        Item::factory()->create([
            'invoice_id' => $invoice1->invoice_id,
            'item_task_id' => $task1->task_id,
        ]);
        Item::factory()->create([
            'invoice_id' => $invoice2->invoice_id,
            'item_task_id' => $task2->task_id,
        ]);

        // Act: Update tasks for invoice1 only
        $this->service->updateOnInvoiceDelete($invoice1->invoice_id);

        // Assert: Only task1 should be updated
        $this->assertDatabaseHas('ip_tasks', [
            'task_id' => $task1->task_id,
            'task_status' => 3,
        ]);
        $this->assertDatabaseHas('ip_tasks', [
            'task_id' => $task2->task_id,
            'task_status' => 4, // Still invoiced
        ]);
    }

    public function test_byTask_is_case_insensitive(): void
    {
        // Arrange: Create task with mixed case
        Task::factory()->create([
            'task_name' => 'Design Homepage',
            'task_description' => 'Create beautiful UI',
        ]);

        // Act: Search with different case
        $results1 = $this->service->byTask('DESIGN')->get();
        $results2 = $this->service->byTask('design')->get();
        $results3 = $this->service->byTask('Beautiful')->get();

        // Assert: Should find task regardless of case
        $this->assertCount(1, $results1);
        $this->assertCount(1, $results2);
        $this->assertCount(1, $results3);
    }

    public function test_getTasksToInvoice_orders_by_finish_date_and_name(): void
    {
        // Arrange: Create tasks with different finish dates
        Task::factory()->create([
            'project_id' => 0,
            'task_status' => 3,
            'task_name' => 'B Task',
            'task_finish_date' => date('Y-m-d', strtotime('+2 days')),
        ]);
        Task::factory()->create([
            'project_id' => 0,
            'task_status' => 3,
            'task_name' => 'A Task',
            'task_finish_date' => date('Y-m-d', strtotime('+1 day')),
        ]);
        
        $invoice = Invoice::factory()->create();

        // Act: Get tasks to invoice
        $result = $this->service->getTasksToInvoice($invoice->invoice_id);

        // Assert: Should be ordered by finish date then name
        $this->assertCount(2, $result);
        $this->assertEquals('A Task', $result[0]->task_name);
        $this->assertEquals('B Task', $result[1]->task_name);
    }

    public function test_getInvoiceForTask_returns_correct_invoice_when_multiple_items_exist(): void
    {
        // Arrange: Create task with invoice item
        $task = Task::factory()->create();
        $invoice1 = Invoice::factory()->create();
        $invoice2 = Invoice::factory()->create();
        
        // Task should be associated with the first invoice
        Item::factory()->create([
            'invoice_id' => $invoice1->invoice_id,
            'item_task_id' => $task->task_id,
        ]);
        
        // Create unrelated item in second invoice
        Item::factory()->create([
            'invoice_id' => $invoice2->invoice_id,
            'item_task_id' => null,
        ]);

        // Act: Get invoice for task
        $result = $this->service->getInvoiceForTask($task->task_id);

        // Assert: Should return the correct invoice
        $this->assertNotNull($result);
        $this->assertEquals($invoice1->invoice_id, $result->invoice_id);
    }
}