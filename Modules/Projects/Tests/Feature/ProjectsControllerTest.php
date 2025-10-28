<?php

namespace Modules\Projects\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Clients\Models\Client;
use Modules\Projects\Controllers\ProjectsController;
use Modules\Projects\Models\Project;
use Modules\Tasks\Models\Task;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

use function Tests\Feature\Projects\route;

use Tests\TestCase;

#[CoversClass(ProjectsController::class)]
class ProjectsControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_projects_list()
    {
        // Arrange: create projects
        $project = \Modules\Projects\Models\Project::factory()->create();

        // Act: visit projects index
        $response = $this->get(route('projects.index'));

        // Assert: projects are displayed
        $response->assertStatus(200);
        $response->assertViewIs('projects.index');
        $response->assertSee($project->project_name);
    }

    #[Test]
    public function it_displays_project_form_for_new_project()
    {
        // Act: visit new project form
        $response = $this->get(route('projects.form'));

        // Assert: form is displayed
        $response->assertStatus(200);
        $response->assertViewIs('projects.form');
        $response->assertViewHas('clients');
    }

    #[Test]
    public function it_creates_new_project(): void
    {
        $projectData = [
            'client_id'        => $this->client->client_id,
            'project_name'     => 'Test Project',
            'project_status'   => 'draft',
            'project_due_date' => now()->addDays(30)->format('Y-m-d'),
        ];

        $response = $this->post(route('projects.form'), $projectData);

        $response->assertRedirect(route('projects.index'));
        $this->assertDatabaseHas('ip_projects', [
            'project_name' => 'Test Project',
            'client_id'    => $this->client->client_id,
        ]);
    }

    #[Test]
    public function it_redirects_when_cancel_button_is_clicked_on_form()
    {
        // Act: submit form with cancel button
        $response = $this->post(route('projects.form'), [
            'btn_cancel' => true,
        ]);

        // Assert: redirects to projects index
        $response->assertRedirect(route('projects'));
    }

    #[Test]
    public function it_displays_project_view()
    {
        // Arrange: create a project
        $project = \Modules\Projects\Models\Project::factory()->create();

        // Act: visit project view page
        $response = $this->get(route('projects.view', ['project_id' => $project->id]));

        // Assert: view is displayed
        $response->assertStatus(200);
        $response->assertViewIs('projects.view');
        $response->assertViewHas('project');
        $response->assertSee($project->project_name);
    }

    #[Test]
    public function it_returns_404_for_non_existent_project_view()
    {
        // Act: visit view for non-existent project
        $response = $this->get(route('projects.view', ['project_id' => 99999]));

        // Assert: 404 error
        $response->assertStatus(404);
    }

    #[Test]
    public function it_deletes_project_and_updates_tasks()
    {
        // Arrange: create a project with tasks
        $project = \Modules\Projects\Models\Project::factory()->create();

        // Act: delete the project
        $response = $this->get(route('projects.delete', ['id' => $project->id]));

        // Assert: redirects and project is deleted
        $response->assertRedirect(route('projects'));
        $this->assertDatabaseMissing('ip_projects', ['project_id' => $project->id]);
    }

    #[Test]
    public function it_updates_existing_project(): void
    {
        $project = Project::factory()->create([
            'client_id'    => $this->client->client_id,
            'project_name' => 'Original Name',
        ]);

        $updateData = [
            'project_name'     => 'Edited Project',
            'project_status'   => 'in_progress',
            'project_due_date' => now()->addDays(45)->format('Y-m-d'),
        ];

        $response = $this->post(route('projects.form', ['id' => $project->project_id]), $updateData);

        $response->assertRedirect(route('projects.index'));
        $this->assertDatabaseHas('ip_projects', [
            'project_id'     => $project->project_id,
            'project_name'   => 'Edited Project',
            'project_status' => 'in_progress',
        ]);
    }

    #[Test]
    public function it_views_project_with_tasks(): void
    {
        $project = Project::factory()->create();
        Task::factory()->count(3)->create(['project_id' => $project->project_id]);

        $response = $this->get(route('projects.view', ['project_id' => $project->project_id]));

        $response->assertSuccessful();
        $response->assertViewHas('project', function ($viewProject) use ($project) {
            return $viewProject->project_id === $project->project_id;
        });
        $response->assertViewHas('tasks', function ($tasks) {
            return count($tasks) === 3;
        });
    }

    #[Test]
    public function it_returns_404_when_viewing_nonexistent_project(): void
    {
        $response = $this->get(route('projects.view', ['project_id' => 99999]));

        $response->assertNotFound();
    }

    #[Test]
    public function it_deletes_project_and_updates_related_tasks(): void
    {
        $project = Project::factory()->create();
        $task    = Task::factory()->create(['project_id' => $project->project_id]);

        $response = $this->delete(route('projects.delete', ['id' => $project->project_id]));

        $response->assertRedirect(route('projects.index'));
        $this->assertDatabaseMissing('ip_projects', ['project_id' => $project->project_id]);
        // Task should be updated to have null project_id or similar
        $task->refresh();
        $this->assertNull($task->project_id);
    }

    #[Test]
    public function it_cancels_project_form_and_redirects(): void
    {
        $response = $this->post(route('projects.form'), ['btn_cancel' => true]);

        $response->assertRedirect(route('projects.index'));
    }

    #[Test]
    public function it_loads_project_form_with_active_clients(): void
    {
        Client::factory()->count(3)->create(['client_active' => 1]);
        Client::factory()->count(2)->create(['client_active' => 0]);

        $response = $this->get(route('projects.form'));

        $response->assertSuccessful();
        $response->assertViewHas('clients', function ($clients) {
            return $clients->count() === 3;
        });
    }
}
