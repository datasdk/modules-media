<?php

namespace Modules\Media\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Modules\Tasks\Models\PDF;
use Symfony\Component\HttpFoundation\Response;

class PDFControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test at PDF genereres og gemmes.
     */
    public function test_generate_pdf()
    {
        Storage::fake('local');

        $user = User::factory()->create();
  

        // Antager at ruten hedder "api/pdf/generate"
        $response = $this->actingAs($user)->postJson(route('api.pdf.generate'));

        $response->assertStatus(201);

        // Bekræft, at en PDF-post er oprettet i databasen
        $this->assertDatabaseCount('pdfs', 1);
    }
}
