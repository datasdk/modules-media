<?php

namespace Modules\Media\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Modules\Media\Models\MediaLibrary;
use Modules\Media\Models\Categories;
use Modules\Media\Services\PDFGenerator;

class AttachTest extends TestCase
{

    use RefreshDatabase;


    protected Categories $category;


    protected function setUp(): void
    {

        parent::setUp();

        $this->category = Categories::factory()->create();

        $this->category2 = Categories::factory()->create();

    }


    protected function tearDown(): void
    {

        $this->category->clearMediaCollection('testing');

        $this->category2->clearMediaCollection('testing');

        parent::tearDown();

    }



    public function test_it_can_attach_uploaded_file()
    {

        $filePath = $this->uploadPdfFile();

        $uploadedFile = new UploadedFile($filePath, 'sample.pdf', null, null, true);

        $this->category->addFiles([$uploadedFile], 'testing');

        $media = $this->category->getFirstMedia('testing');

        $this->assertCount(1, $this->category->getMedia('testing'));

        $this->assertFileExists($media->getPath());


    }


 
    public function test_it_can_attach_media_library_model()
    {

        
        $filePath = $this->uploadPdfFile();

        $uploadedFile = new UploadedFile($filePath, 'share_sample.pdf', null, null, true);

        $this->category->addFiles([$uploadedFile], 'testing');

        $mediaModel = $this->category->getFirstMedia('testing');


        $this->category2->addFiles([$mediaModel], 'testing');

        $media = $this->category2->getFirstMedia('testing');

 
        $this->assertNotNull($media);

        $this->assertCount(1, $this->category->getMedia('testing'));

        $this->assertFileExists($media->getPath());


    }


  
    public function test_it_can_attach_direct_file_path()
    {

        $filePath = $this->uploadPdfFile();

        $this->category->addFiles([$filePath], 'testing');

        $media = $this->category->getFirstMedia('testing');

        
        $this->assertNotNull($media);

        $this->assertCount(1, $this->category->getMedia('testing'));

        $this->assertFileExists($media->getPath());

    }


    private function uploadPdfFile(): string
    {

        $params = [
            'id' => uniqid(),
            'text' => 'Hello world',
        ];

        
        return app(PDFGenerator::class)->generatePDF('media::pdf.test', $params, true, 'testing');

    }
}
