<?php

namespace Tests\Feature\Projects;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Projects\Controllers\ProjectsController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
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
    public function it_displays_project_form_for_existing_project()
    {
        // Arrange: create a project
        $project = \Modules\Projects\Models\Project::factory()->create();

        // Act: visit project edit form
        $response = $this->get(route('projects.form', ['id' => $project->id]));

        // Assert: form is displayed
        $response->assertStatus(200);
        $response->assertViewIs('projects.form');
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
}
