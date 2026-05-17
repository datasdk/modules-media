<?php

namespace Modules\Media\Observers;

use Modules\Media\Models\Media;
use Illuminate\Support\Facades\Cache;

class MediaObserver
{

    /**
     * Når et Media-objekt opdateres, ryd cachen
     */
    public function updated(Media $media)
    {

        Cache::forget("media_file_{$media->file_name}");

    }


    /**
     * Når et Media-objekt slettes, ryd cachen og fjern mappen med ID
     */
    public function deleted(Media $media)
    {

        Cache::forget("media_file_{$media->file_name}");

        // Slet mappen med media ID og alt indhold
        $folderPath = realpath(storage_path("app/uploads/{$media->id}"));

        if ($folderPath && is_dir($folderPath)) {

            $this->deleteFolderForce($folderPath);

        }

    }


    /**
     * Rekursiv sletning af mappe uanset indhold
     */
    private function deleteFolderForce($dir)
    {

        if (!is_dir($dir)) return false;

        $items = array_diff(scandir($dir), ['.', '..']);

        foreach ($items as $item) {

            $path = $dir . DIRECTORY_SEPARATOR . $item;

            if (is_dir($path)) {

                $this->deleteFolderForce($path);

            } else {

                @unlink($path);

            }

        }


        return @rmdir($dir);


    }

}
