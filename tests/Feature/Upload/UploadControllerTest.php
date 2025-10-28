<?php

namespace Tests\Feature\Upload;

use Illuminate\Foundation\Testing\RefreshDatabase;
use src\Controllers\UploadController;
use Tests\TestCase;

#[CoversClass(UploadController::class)]
class UploadControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_saves_file_and_returns_success()
    {
        // Arrange: create a fake customer and url_key
        $customer = \Modules\Clients\Models\tmpClient::factory()->create();
        $url_key  = 'test-key';

        // Create a fake file
        $file = \Illuminate\Http\UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        // Act: post to the upload route
        $response = $this->post(route('upload.file', ['customerId' => $customer->id, 'url_key' => $url_key]), [
            'file' => $file,
        ]);

        // Assert: file metadata is saved and response is correct
        $this->assertDatabaseHas('uploads', [
            'customer_id' => $customer->id,
            'url_key'     => $url_key,
            'filename'    => 'document.pdf',
        ]);
        $response->assertStatus(200);
        $response->assertJsonFragment(['message' => 'upload_file_uploaded_successfully']);
    }
}
