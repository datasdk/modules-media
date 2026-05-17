<?php

namespace Modules\Media\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Media\Services\MediaLibraryService;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaResource extends JsonResource
{
    /**
     * @param Media $media
     */
    public function toArray($request)
    {
        $mediaService = app(MediaLibraryService::class);

        return [
            'id'        => $this->id,
            'type'      => $mediaService->determineDiskFromExtension($this->file_name),
            'file_name' => $this->file_name,
            'src'       => $this->getPublicUrl(),
        ];
    }
}
