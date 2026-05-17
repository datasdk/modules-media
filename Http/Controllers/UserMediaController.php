<?php

namespace Modules\Media\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Modules\Media\Models\Media;
use ZipArchive;
use Storage;

// må ikke slettes

class UserMediaController extends Controller {
   


    public function show(Request $req, $filename)
    {

      
        $media = Media::where('file_name', $filename)->firstOrFail();


        if($req->has("thumb") && $media->hasGeneratedConversion('thumb')){

            $filePath = $media->getPath('thumb');
   
        } else {

            $filePath = $media->getPath();

        }


        $mediaData = [
            'disk' => $media->disk,
            'file_name' => $media->file_name,
            'mimeType' => $media->mime_type,
            'filePath' => $filePath,
        ];


 

        if (!$mediaData) {

            return response()->json(['error' => 'File not found in database'], 404);

        }

        // Tjek om filen findes fysisk på disken
        if (!file_exists($mediaData['filePath'])) {

            return response()->json(['error' => 'File not found on disk'], 404);

        }


        
        // Returner filen som inline visning i browseren
        return response()->file($mediaData['filePath'], [

            'Content-Type'        => $mediaData['mimeType'],
            'Content-Disposition' => 'inline; filename="' . $mediaData['file_name'] . '"',

            'Cache-Control'       => 'public, max-age=31536000, immutable',
            'Expires'             => now()->addYear()->format('D, d M Y H:i:s \G\M\T'),
            'ETag'                => md5_file($mediaData['filePath']),
            'Last-Modified'       => date('D, d M Y H:i:s \G\M\T', filemtime($mediaData['filePath'])),
            
        ]);


    }


    // Download metode
    public function download(Request $req, $filename)
    {

        // Tjek om filens data allerede er i cachen
        $cacheKey = "media_file_{$filename}";

        $mediaData = cache()->remember($cacheKey, now()->addMinutes(60), function () use ($filename) {
            // Hent filen fra databasen
            $media = Media::where("file_name", $filename)->first();

            if (!$media) {
                return null;
            }

            return [
                'disk' => $media->disk,
                'file_name' => $media->file_name,
                'mimeType' => $media->mime_type,
                'filePath' => $media->getPath(),
            ];
        });


        if (!$mediaData) {
            return response()->json(['error' => 'File not found in database'], 404);
        }

        // Tjek om filen findes fysisk på disken
        if (!file_exists($mediaData['filePath'])) {
            return response()->json(['error' => 'File not found on disk'], 404);
        }

        // Returner filen som en nedlasting
        return response()->download($mediaData['filePath'], $mediaData['file_name'], [
            'Content-Type'        => $mediaData['mimeType'],
            'Cache-Control'       => 'public, max-age=3600, must-revalidate',
        ]);


    }



    public function downloadAll(Request $req)
    {

        // Hent alle billeder fra databasen
        $mediaFiles = Media::all();
        
        if ($mediaFiles->isEmpty()) {
            return response()->json(['error' => 'No media files found'], 404);
        }

        // Opret en midlertidig fil for ZIP-arkivet
        $zip = new ZipArchive();
        $zipFile = storage_path('app/public/media_download.zip');
        
        // Hvis arkivet ikke kan oprettes, returneres fejl
        if ($zip->open($zipFile, ZipArchive::CREATE) !== TRUE) {
            return response()->json(['error' => 'Failed to create ZIP file'], 500);
        }

        // Tilføj hver billedfil til ZIP
        foreach ($mediaFiles as $media) {
            
            $filePath = $media->getPath();

            // Tjek om filen eksisterer på disken
            if (file_exists($filePath)) {
                // Tilføj filen til ZIP med det originale filnavn
                $zip->uploadFile($filePath, $media->file_name);
            }
        }


        // Luk ZIP-filen
        $zip->close();

        // Returner ZIP-filen som download
        return response()->download($zipFile)->deleteFileAfterSend(true);

    }
    


}



  
   
