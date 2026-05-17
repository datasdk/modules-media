<?php

namespace Modules\Media\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Media\Models\MediaLibrary;

class MediaLibraryFactory extends Factory
{
    protected $model = MediaLibrary::class;

    public function definition(): array
    {
        /**
         * Disk + collection
         * Disk = uploads
         * Mappe = testing
         */
        $disk = 'testing';
        $collection = 'testing';

        // Sørg for at disken findes i tests
        Storage::fake($disk);

        // Fake image
        $file = UploadedFile::fake()->image(
            'test_' . Str::random(10) . '.jpg',
            600,
            400
        );

        /**
         * Gem filen i:
         * storage/app/uploads/testing
         */
        $relativePath = $file->store($collection, $disk);

        return [
            'model_type' => null,   // sættes i test
            'model_id' => null,     // sættes i test

            'uuid' => (string) Str::uuid(),

            'collection_name' => $collection,

            'name' => pathinfo($relativePath, PATHINFO_FILENAME),
            'file_name' => basename($relativePath),

            'mime_type' => $file->getMimeType(),
            'disk' => $disk,
            'conversions_disk' => null,

            'size' => Storage::disk($disk)->size($relativePath),

            // Spatie Media Library forventer ARRAYS (casts)
            'manipulations' => [],
            'custom_properties' => [],
            'generated_conversions' => [],
            'responsive_images' => [],

            'order_column' => null,

            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
