<?php

namespace Tests\Feature\Tasks;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Tasks\Controllers\TasksController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(TasksController::class)]
class TasksControllerTest extends TestCase
{
    use RefreshDatabase;

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
        $response = $this->get(route('tasks.delete', ['id' => $task->id]));

        // Assert: redirects and task is deleted
        $response->assertRedirect(route('tasks'));
        $this->assertDatabaseMissing('ip_tasks', ['task_id' => $task->id]);
    }
}
