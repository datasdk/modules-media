<?php

namespace Modules\Media\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Media\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test uploading a file via MediaController@store.
     */
    public function test_store_upload_file()
    {
        Storage::fake('public');

        $user = User::factory()->create();

        // Create a fake image
        $file = UploadedFile::fake()->image('test_upload.jpg', 600, 600);

        $payload = [
            'file'       => $file,
            'collection' => 'uploads',
        ];

        $response = $this->actingAs($user)
            ->postJson(route('api.media.upload'), $payload);

        $response->assertStatus(200);

        // Check that the response contains media ID and file info
        $this->assertArrayHasKey('id', $response->json());
        $this->assertArrayHasKey('file_name', $response->json());

    }

    /**
     * Test deleting a previously uploaded file using its media ID.
     */
    public function test_delete_uploaded_file()
    {
        Storage::fake('public');

        $user = User::factory()->create();

        // First, upload a fake image
        $file = UploadedFile::fake()->image('delete_test.jpg', 400, 400);

        $uploadResponse = $this->actingAs($user)
            ->postJson(route('api.media.upload'), [
                'file' => $file,
                'collection' => 'uploads',
            ]);

        $uploadResponse->assertStatus(200);

        $mediaId = $uploadResponse->json('id');

  
        // Now delete using the media ID
        $deleteResponse = $this->actingAs($user)
            ->deleteJson(route('api.media.media.destroy', $mediaId));

        $deleteResponse->assertStatus(200)
            ->assertJson(['message' => 'File deleted successfully']);


    }

    /**
     * Test attempting to delete a non-existent media ID.
     */
    public function test_delete_nonexistent_file()
    {

        $user = User::factory()->create();


        $response = $this->actingAs($user)
            ->deleteJson(route('api.media.media.destroy', 9999)); // ID that doesn't exist


        $response->assertStatus(404);


    }

    /**
     * Test viewing a non-existent file (returning 404).
     */
    public function test_view_nonexistent_file()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson(route('media.public.show', ['filename' => 'uploads/nonexistent.jpg']));

        $response->assertStatus(404);
    }
}
