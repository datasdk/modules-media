<?php

namespace Modules\Media\Http\Controllers;


use Modules\Media\Http\Requests\MediaRequest;
use Modules\Media\Services\MediaLibraryService;
use Illuminate\Support\Facades\Storage;
use Modules\Media\Models\User;
use Illuminate\Support\Str;
use Orion\Http\Requests\Request;
use Modules\Media\Models\MediaLibrary;
use Intervention\Image\ImageManagerStatic as Image;
use App\Http\Controllers\OrionBaseController;



class MediaController extends OrionBaseController
{

    protected $model = MediaLibrary::class;

    protected $request = MediaRequest::class;
    

    protected $filterableBy = [
        "collection_name"
    ];


    /**
     * Display a listing of the resource.
     */
    public function index(Request $req, ...$args)
    {
        return view('media::index');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $req, ...$args)
    {
        return view('media::create');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $req, ...$args)
    {

        try {

            $collection = $req->get("collection") ?? "uploads";
            $user = User::findOrFail(auth()->id());


            if (!$user) {
                return redirect()->back()->withErrors(['error' => 'Bruger ikke fundet.']);
            }


            if (!$req->hasFile('file')) {
                return redirect()->back()->withErrors(['error' => 'Ingen fil uploadet.']);
            }


            $file = $req->file('file');
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            

            $slugifiedName = Str::slug($originalFilename);
            $uniqueFilename = $slugifiedName . '-' . uniqid() . '.' . $extension;


            $disk = (new MediaLibraryService())->determineDiskFromExtension($uniqueFilename);


            if (!$disk) {
                return redirect()->back()->withErrors(['error' => 'Filtypen understøttes ikke.']);
            }


            $media = (new MediaLibraryService)->uploadFile($user, $file, $collection, $disk, $slugifiedName, $uniqueFilename);


            return redirect()->route('media.index')->with('success', 'Filen blev uploadet succesfuldt!');


        } catch (\Exception $e) {


            return redirect()->back()->withErrors(['error' => 'Fejl under upload: ' . $e->getMessage()]);


        }


    }


    /**
     * Display the specified resource.
     */
    public function show(Request $req, ...$args)
    {

        $id = $args[0];

        $media = MediaLibrary::findOrFail($id);
        
        return view('media::show', compact('media'));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $req, ...$args)
    {

        $id = $args[0];

        $media = MediaLibrary::findOrFail($id);
        
        return view('media::edit', compact('media'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $req, ...$args)
    {

        $id = $args[0];
        
        try {

            $collection = $req->get("collection") ?? "uploads";

            $user = User::findOrFail(auth()->id());


            if (!$user) {

                return redirect()->back()->withErrors(['error' => 'Bruger ikke fundet.']);
            }

            if (!$req->hasFile('file')) {
                return redirect()->back()->withErrors(['error' => 'Ingen fil uploadet.']);
            }

            $file = $req->file('file');
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            
            $slugifiedName = Str::slug($originalFilename);
            $uniqueFilename = $slugifiedName . '-' . uniqid() . '.' . $extension;

            $disk = (new MediaLibraryService())->determineDiskFromExtension($uniqueFilename);

            if (!$disk) {
                return redirect()->back()->withErrors(['error' => 'Filtypen understøttes ikke.']);
            }

            $media = (new MediaLibraryService)->uploadFile($user, $file, $collection, $disk, $slugifiedName, $uniqueFilename);

            return redirect()->route('media.index')->with('success', 'Filen blev opdateret succesfuldt!');


        } catch (\Exception $e) {

            return redirect()->back()->withErrors(['error' => 'Fejl under opdatering: ' . $e->getMessage()]);

        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $req, ...$args)
    {

        $id = $args[0];
        
        try {
            MediaLibrary::findOrFail($id)->delete();
            return redirect()->route('media.index')->with('success', 'Filen blev slettet succesfuldt!');
            
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Fejl under sletning: ' . $e->getMessage()]);
        }

    }

    /**
     * Scan disk for images (optional)
     */
    public function scanDisk(Request $req, ...$args)
    {

        $images = (new MediaLibraryService())->scanImagesDisk();

        return view('media::scan', compact('images'));

    }
    
    private function createThumbnail($file, $slugifiedName, $extension, $disk)
    {

        if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif'])) {

            $image = Image::make($file);

            $image->resize(200, 200, function ($constraint) {

                $constraint->aspectRatio();
                $constraint->upsize();

            });


            $thumbnailPath = $slugifiedName . '-thumb.' . $extension;

            $disk = "thumbs";

            Storage::put($thumbnailPath, $image->stream());

            return $thumbnailPath;

        }
        

        return null;

    }
}

