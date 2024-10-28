<?php
namespace Tests\Feature;

use App\Models\Image;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ImageControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Sanctum::actingAs(User::factory()->create());
    }

    public function test_image_upload_success()
    {
        // Mock the storage disk
        Storage::fake('spaces');

        // Create a fake image file
        $file = UploadedFile::fake()->image('test-image.jpg');

        // Define the request payload
        $payload = [
            'image' => $file,
        ];

        // Send a POST request to the upload endpoint
        $response = $this->postJson('/api/images/upload', $payload);

        // Assert the response status
        $response->assertStatus(201);

        // Assert the image was stored
        $filePath = env('DO_SPACES_IMAGE_PATH') . 'products/' . $file->hashName();
        Storage::disk('spaces')->assertExists($filePath);

        // Assert the image record was created in the database
        $this->assertDatabaseHas('images', [
            'alt' => 'test-image',
            'url' => Storage::disk('spaces')->url($filePath),
        ]);
    }

    public function test_image_upload_validation_error()
    {
        // Send a POST request without an image
        $response = $this->postJson('/api/images/upload', []);

        // Assert the response status
        $response->assertStatus(422);

        // Assert the validation error message
        $response->assertJsonValidationErrors('image');
    }

    public function test_image_upload_failure()
    {
        // Mock the storage disk to throw an exception
        Storage::shouldReceive('disk->putFileAs')
            ->andThrow(new \Exception('Failed to upload image'));

        // Create a fake image file
        $file = UploadedFile::fake()->image('test-image.jpg');

        // Define the request payload
        $payload = [
            'image' => $file,
        ];

        // Send a POST request to the upload endpoint
        $response = $this->postJson('/api/images/upload', $payload);

        // Assert the response status
        $response->assertStatus(500);

        // Assert the error message
        $response->assertJson([
            'message' => 'Failed to upload image',
        ]);
    }
}