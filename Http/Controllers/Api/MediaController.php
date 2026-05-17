<?php

namespace Modules\Media\Http\Controllers\Api;


use Modules\Media\Http\Requests\MediaUploadRequest;
use Modules\Media\Services\MediaLibraryService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Media\Models\User;
use Illuminate\Support\Str;
use App\Http\Controllers\OrionBaseController;
use Orion\Http\Requests\Request;
use Modules\Media\Http\Requests\MediaRequest;
use Modules\Media\Models\MediaLibrary;
use Intervention\Image\ImageManagerStatic as Image;

use Intervention\Image\ImageManager;


class MediaController extends OrionBaseController
{


    protected $model = MediaLibrary::class;

    protected $request = MediaRequest::class;


    protected $filterableBy = [
        "collection_name"
    ];



    public function store(Request $req)
    {


        try {


            $collection = $req->get("collection") ?? "uploads";

      
            // Hent den aktuelle bruger
            $user = User::findOrFail(auth()->id());

          

            if (!$user) {
                return response()->json(["error" => "Bruger ikke fundet."], 401);
            }


            // Validér om en fil er uploadet
            if (!$req->hasFile('file')) {

                return response()->json(["error" => "Ingen fil uploadet."], 400);

            }


            $file = $req->file('file');

            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

            $extension = $file->getClientOriginalExtension();
            

            // Generer URL-venligt navn med unik ID
            $slugifiedName = Str::slug($originalFilename);

            $uniqueFilename = $slugifiedName . '-' . uniqid() . '.' . $extension;


            // Bestem den korrekte disk baseret på filens extension
            $disk = (new MediaLibraryService())->determineDiskFromExtension($uniqueFilename);


            if (!$disk) {

                return response()->json(["error" => "Filtypen understøttes ikke."], 400);

            }

      
            $media = (new MediaLibraryService)->uploadFile($user, $file, $collection, $disk, $slugifiedName, $uniqueFilename);


            return MediaLibrary::findOrFail($media->id)->toArray();

         
        } catch (\Exception $e) {

            return response()->json(["error" => "Fejl under upload: " . $e->getMessage()], 500);
        }
        
    }

       

    private function createThumbnail($file, $slugifiedName, $extension, $disk)
    {

        // Hvis filen er et billede (kan tilpasses efter behov)
        if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif'])) {

            // Brug Intervention Image til at åbne filen
            $image = Image::make($file);

            // Skaler billedet til en bestemt bredde og højde
            $image->resize(200, 200, function ($constraint) {
                $constraint->aspectRatio(); // Bevar billedets proportioner
                $constraint->upsize(); // Undgå at skaleres op, hvis billedet er mindre end 200px
            });

            // Bestem filens sti for thumbnail
            $thumbnailPath = $slugifiedName . '-thumb.' . $extension;
            $disk = "thumbs";

            // Gem thumbnail'en på den valgte disk
            $path = Storage::put($thumbnailPath, $image->stream());

            // Returner stien til thumbnailen
            return $thumbnailPath;
        }

        return null;
    }



    /**
     * Slet en fil baseret på sti
     */
    public function destroy(Request $req, ...$args)
    {
        
        $id = $args[0];
        
        MediaLibrary::findOrFail($id)->delete();


        return response()->json(['message' => 'File deleted successfully']);
    }


    public function scanDisk()
    {
        $images = (new MediaLibraryService())->scanImagesDisk(); // Scanner 'images'-disken
        return response()->json($images);
    }
}
