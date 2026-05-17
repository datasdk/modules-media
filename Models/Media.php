<?php

namespace Modules\Media\Models;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;
use Spatie\Image\Manipulations;

class Media extends BaseMedia implements HasMedia
{
    use InteractsWithMedia;

   

    /**
     * Get the public URL of the original media.
     *
     * @return string
     */
    public function getPublicUrl(): string
    {
        return url(route("media.public.show", ["filename" => $this->file_name]));
    }

    /**
     * Get the URL of the thumbnail conversion.
     *
     * @return string|null
     */
    public function getThumbUrl(): ?string
    {

        return url(route("media.public.show", ["filename" => $this->file_name,"thumb"=>1]));

    }

    /**
     * Get the path to the thumbnail file.
     *
     * @return string|null
     */
    public function getThumbPath(): ?string
    {
        if ($this->hasGeneratedConversion('thumb')) {
            return $this->getPath('thumb');
        }

        return null;
    }
}
