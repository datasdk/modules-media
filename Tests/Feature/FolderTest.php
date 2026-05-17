<?php

namespace Modules\Media\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Modules\Media\Models\Folders;

class FolderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test at index returnerer mapper.
     */
    public function test_index_folders()
    {
        $user = User::factory()->create();
      
        // Opret nogle mapper for at teste index
        Folders::factory()->count(3)->create();

        $response = $this->actingAs($user)->getJson(route('api.folders.index'));

        $response->assertStatus(200);
    }

    /**
     * Test at en mappe kan oprettes.
     */
    public function test_store_folder()
    {
        $user = User::factory()->create();
  

        $payload = [
            'name' => 'New Folder'
        ];

        $response = $this->actingAs($user)->postJson(route('api.folders.store'), $payload);

        $response->assertStatus(201);
    }
}
