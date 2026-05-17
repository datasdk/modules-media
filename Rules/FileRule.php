<?php

namespace Modules\Media\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;
use Modules\Media\Models\MediaLibrary;

class FileRule implements Rule
{
    protected $message;


 

    /**
     * Bestemmer om værdien består validering.
     */
    public function passes($attribute, $value)
    {

        if(!$value){ return true; }


         // Tillad allerede tilknyttede filer ved opdatering
         if (is_int($value)) {

            $media = MediaLibrary::find('id', $value);

            if ($media) {

                return true;
                
            }

            $this->message = 'Filen er ikke knyttet til det angivne id.';

            return false;

        }


        // Tillad allerede tilknyttede filer ved opdatering
        if (is_array($value) && isset($value['id'])) {

            $media = MediaLibrary::find($value['id']);

            if ($media) {

                return true;

            }

            $this->message = 'Filen er ikke knyttet til det angivne objekt.';

            return false;

        }


        // Ny uploadet fil
        if (!$value instanceof UploadedFile) {

            $this->message = 'Filen er ikke en gyldig uploadet fil.';

            return false;

        }


        // Tjek filtype
        $allowedExtensions = config('media.allowed_file_extensions', []);

        $fileExtension = strtolower($value->getClientOriginalExtension());

        if (!in_array($fileExtension, $allowedExtensions)) {

            $this->message = 'Filen skal være en af følgende filtyper: ' . implode(', ', $allowedExtensions);

            return false;

        }


        // Tjek filstørrelse (maks 5MB)
        if ($value->getSize() > 5 * 1024 * 1024) {

            $this->message = 'Filen må ikke overstige 5MB.';

            return false;

        }


        return true;

    }

    /**
     * Returnerer fejlbesked.
     */
    public function message()
    {
        return $this->message ?? 'Filen er ikke gyldig.';
    }
}
