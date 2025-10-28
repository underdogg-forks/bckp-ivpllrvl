<?php

namespace Modules\Tasks\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Tasks\Controllers\TasksController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

use function Tests\Feature\Tasks\route;

use Tests\TestCase;

#[CoversClass(TasksController::class)]
class TasksControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['user_type' => 1, 'user_active' => 1]);
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_displays_tasks_list()
    {
        // Arrange: create tasks
        $task = \Modules\Tasks\Models\Task::factory()->create();

        // Act: visit tasks index
        $response = $this->get(route('tasks.index'));

        // Assert: tasks are displayed
        $response->assertStatus(200);
        $response->assertViewIs('tasks.index');
        $response->assertSee($task->task_name);
    }

    #[Test]
    public function it_displays_task_form_for_new_task()
    {
        // Act: visit new task form
        $response = $this->get(route('tasks.form'));

        // Assert: form is displayed
        $response->assertStatus(200);
        $response->assertViewIs('tasks.form');
        $response->assertViewHas('projects');
        $response->assertViewHas('tax_rates');
    }

    #[Test]
    public function it_redirects_when_cancel_button_is_clicked()
    {
        // Act: submit form with cancel button
        $response = $this->post(route('tasks.form'), [
            'btn_cancel' => true,
        ]);

        // Assert: redirects to tasks index
        $response->assertRedirect(route('tasks'));
    }

    #[Test]
    public function it_deletes_task()
    {
        // Arrange: create a task
        $task = \Modules\Tasks\Models\Task::factory()->create();

        // Act: delete the task
        $taskId   = $task->getKey();
        $response = $this->get(route('tasks.delete', ['id' => $taskId]));

        // Assert: redirects and task is deleted
        $response->assertRedirect(route('tasks'));
        $this->assertDatabaseMissing('ip_tasks', ['task_id' => $taskId]);
    }

    #[Test]
    public function it_displays_task_lookups_modal(): void
    {
        $response = $this->get(route('tasks.ajax.modalTaskLookups'));

        $response->assertSuccessful();
        $response->assertViewHas('default_item_tax_rate');
        $response->assertViewHas('tasks');
    }

    #[Test]
    public function it_displays_tasks_for_specific_invoice(): void
    {
        $invoice = Invoice::factory()->create();
        Task::factory()->count(3)->create(['invoice_id' => null]);

        $response = $this->get(route('tasks.ajax.modalTaskLookups', ['invoice_id' => $invoice->invoice_id]));

        $response->assertSuccessful();
        $response->assertViewHas('tasks');
    }

    #[Test]
    public function it_processes_task_selections(): void
    {
        $tasks   = Task::factory()->count(3)->create(['task_price' => 50.00]);
        $taskIds = $tasks->pluck('task_id')->toArray();

        $response = $this->post(route('tasks.ajax.processSelections'), [
            'task_ids' => $taskIds,
        ]);

        $response->assertSuccessful();
        $data = $response->json();
        $this->assertCount(3, $data);
        $this->assertArrayHasKey('task_price', $data[0]);
    }

    #[Test]
    public function it_formats_task_prices_in_selection_processing(): void
    {
        $task = Task::factory()->create(['task_price' => 123.456]);

        $response = $this->post(route('tasks.ajax.processSelections'), [
            'task_ids' => [$task->task_id],
        ]);

        $data = $response->json();
        // Price should be formatted
        $this->assertMatchesRegularExpression('/^\d+\.\d{2}$/', $data[0]['task_price']);
    }

    #[Test]
    public function it_returns_empty_array_for_no_task_ids(): void
    {
        $response = $this->post(route('tasks.ajax.processSelections'), [
            'task_ids' => [],
        ]);

        $response->assertSuccessful();
        $this->assertEmpty($response->json());
    }
}
