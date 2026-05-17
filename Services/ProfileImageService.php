<?php

namespace Modules\Media\Services;

use App\Models\User;
use Modules\Media\Models\User as MediaUser;
use Illuminate\Http\UploadedFile;
use Modules\Media\Services\MediaLibraryService;


class ProfileImageService
{

    protected MediaLibraryService $mediaService;


    public function __construct(MediaLibraryService $mediaService)
    {

        $this->mediaService = $mediaService;

    }

    /**
     * Upload og tilknyt et profilbillede til brugeren.
     *
     * @param User $user
     * @param UploadedFile|null $file
     * @return string|null URL til det uploadede billede eller null
     */
    public function set(User $user, $file): ?string
    {
     
        $user = MediaUser::find($user->id);
        

        if ($file instanceof UploadedFile) {

            // Upload til MediaLibrary med collection 'profile-images'
            $media = $this->mediaService->uploadFile($user, $file, 'profile-images');

            // Hent den offentlige sti
            $image = $media->getPublicUrl();


        } else if(empty($file)){


            $image = null;


        } else {


            return false;


        }



        // Gem URL på brugeren
        $user->image = $image;

        $user->save();


        return $image;


    }

}
