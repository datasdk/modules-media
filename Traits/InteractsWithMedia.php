<?php

namespace Modules\Media\Traits;

use Spatie\MediaLibrary\InteractsWithMedia as SpatieInteractsWithMedia;
use Modules\Media\Services\MediaLibraryService;
use Modules\Media\Models\MediaLibrary;
use Modules\Media\Models\Media;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Modules\Media\Http\Resources\MediaResource;
use Spatie\MediaLibrary\MediaCollections\Models\Media as OrigMedia;
use Spatie\Image\Enums\BorderType;
use Spatie\Image\Enums\CropPosition;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieBaseMedia;


trait InteractsWithMedia
{

    use SpatieInteractsWithMedia;
 
        
   
    public function getImageAttribute()
    {
        return $this->getMediaByDisk('images')->first();
    }


    public function getImagesAttribute()
    {

        return $this->getMediaByDisk('images');
    }


    public function getDocumentsAttribute()
    {
        return $this->getMediaByDisk('documents');
    }


    public function getSoundsAttribute()
    {
        return $this->getMediaByDisk('sounds');
    }


    public function getVideosAttribute()
    {
        return $this->getMediaByDisk('videos');
    }


    public function getArchivesAttribute()
    {
        return $this->getMediaByDisk('archives');
    }


    public function getFilesAttribute(){
        
        $mediaCollection = $this->media()->get();

        return $this->getMediaParametersFromMediaCollection($mediaCollection);

    }



    private function getMediaByDisk(string $disk, string $collection = "uploads")
    {

        return $this->getMedia($collection)
        ->filter(fn($media) => $media->disk === $disk)
        ->map(fn($media) => $this->getMediaParameters($media));

    }
    


    public function getAllMedia(string $collection = "uploads")
    {

        return $this->getMedia($collection)
            ->map(fn($media) => $this->getMediaParameters($media));
           // ->groupBy(fn($item) => $item["type"] ?? "file"); // Default værdi for type

    }




    
    public function getMediaParameters(Media $media)
    {
        $mediaService = app(MediaLibraryService::class);

        $type = $mediaService->determineDiskFromExtension($media->file_name);

        return [
            "id" => $media->id,
            "type" => $type,
            "name" => $media->name,
            "file_name" => $media->file_name,
            "size" => $media->human_readable_size, // tilføjet
            "src" => $media->getPublicUrl()
        ];
    }

   

    public function getMediaParametersFromMediaCollection(MediaCollection $mediaCollection){

        $m = [];

        foreach($mediaCollection as $media){

            $m[]= $this->getMediaParameters($media);

        }

        return $m;

    }


 

    public function addMainImage($image)
    {


        if (is_file($image)) {

            $media = $this->addMedia($image);

        } elseif (is_int($image) || is_numeric($image)) {

            $mediaLibrary = Media::find($image);

            if ($mediaLibrary) {

                $media = $mediaLibrary->getPath();

            }
            
        } elseif (is_string($image)) {

            $media = $this->addMediaFromUrl($image);

        } 


        if (isset($media)) {

            $collection = "uploads";

            $disk = "images";

            $result = (new MediaLibraryService)->uploadFile($this, $media, $collection, $disk);

        }


        return $this;

    }
    
    public function addFiles(MediaCollection|array $files, string $collection = 'standard', string $disk = 'uploads'){
       
        app(MediaLibraryService::class)->addFiles($this, $files, $collection, $disk);

        return $this->refresh();

    }

    public function uploadFile($file, string $collection = 'standard', ?string $disk = 'uploads',?array $filename = null){
        
        app(MediaLibraryService::class)->uploadFile($this, $file, $collection,$disk, $filename = null);

        return $this->refresh();

    }
    

    public function uploadFiles(array $files, string $collection = 'standard', ?string $disk = 'uploads',?array $file_names = null)
    {

        app(MediaLibraryService::class)->uploadFiles($this, $files, $collection,$disk, $file_names);

        return $this->refresh();

    }

    public function deleteAllMedia2(){

        $this->media()->get()->each(function ($media) {
            $media->forceDelete(); // sletter databasen + filerne på disk
        });

        return $this;
    }



    public function images(){

        return $this->media()->where('collection_name', 'images');

    }
  
    public function files(){

        return $this->media();

    }


    public function registerMediaConversions(?SpatieBaseMedia $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->sharpen(10)
            ->quality(60)
            ->background('#ffffff') // Vigtigt for PNG med transparens
            ->nonQueued();
    }

}
