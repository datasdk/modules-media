<?php

namespace Modules\Media\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class CategoriesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test at index returnerer kategorier.
     */
    public function test_index_categories()
    {
        $user = User::factory()->create();
    

        $response = $this->actingAs($user)->getJson('api/categories');

        $response->assertStatus(200);
    }

    /**
     * Test at children-metoden returnerer underkategorier.
     */
    public function test_children_categories()
    {
        $user = User::factory()->create();
    

        // Her antages det, at ruten for children er "api/categories/{type}/search"
        $type = 'example';  
        $payload = ['query' => 'test'];

        $response = $this->actingAs($user)->postJson("api/categories/{$type}/search", $payload);

        $response->assertStatus(200);
    }
}
