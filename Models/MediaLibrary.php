<?php

namespace Modules\Media\Models;


use HasFactory;
use Model;

use Modules\Media\Traits\InteractsWithMedia;
use Modules\Media\Services\MediaLibraryService;
use Modules\Media\Contracts\HasMedia;
use Modules\Media\Models\Media;


class MediaLibrary extends Media  implements HasMedia
{
    
    use InteractsWithMedia;
    use HasFactory;


    protected $table = "media";
    
    protected $appends = ['file_type',"src",'thumb','download_link'];



    public function getSrcAttribute(){

          
        return $this->getPublicUrl();
   
    }

    public function getThumbAttribute()
    {
        return $this->getThumbUrl();
    }


    public function getDownloadLinkAttribute(){

          
        return (new MediaLibraryService())->getDownloadUrl($this->file_name);
   
    }



    public function getFileTypeAttribute(){

       // Hent filens utvidelse (for eksempel 'jpg', 'png', 'mp4', osv.)
        $fileExtension = pathinfo($this->file_name, PATHINFO_EXTENSION);

        // Returner utvidelsen i små bokstaver for konsekvens
        return (new MediaLibraryService())->getFileType($fileExtension);

    }

 

}
