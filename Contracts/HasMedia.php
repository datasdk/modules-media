<?php

namespace Modules\Media\Contracts;

use Spatie\MediaLibrary\HasMedia as SpartieHasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;

interface HasMedia extends SpartieHasMedia
{
    public function addMainImage($image);

    /**
     * Tilføj flere filer til en media collection
     *
     * @param MediaCollection|array $files
     * @param string $collection
     * @return void
     */
    public function addFiles(MediaCollection|array $files, string $collection = 'standard', string $disk = 'uploads');

    public function uploadFile($file);

    public function getAllMedia();
}
