<?php

namespace Modules\Media\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;
use Modules\Media\Models\MediaLibrary;


class MultipleFilesRule implements Rule
{

    protected $message;

    /**
     * Bestemmer om værdien består validering.
     */
    public function passes($attribute, $value)
    {

        if (!$value) {
            return true;
        }

        // Sørg for at behandle alle inputs som array
        $values = is_array($value) ? $value : [$value];


        foreach ($values as $item) {
    
         
            // Hvis det er et heltal (id)
            if (is_numeric($item)) {

                $media = MediaLibrary::find($item);
                if (!$media) {
                    $this->message = 'Filen er ikke knyttet til det angivne id.';
                    return false;
                }

                continue;

            }


            // Hvis det er array med 'id' key
            if (is_array($item) && isset($item['id'])) {

                $media = MediaLibrary::find($item['id']);

                if (!$media) {
                    $this->message = 'Filen er ikke knyttet til det angivne objekt.';
                    return false;
                }

                continue;

            }


            // Hvis det er en uploaded fil
            if ($item instanceof UploadedFile) {

                $allowedExtensions = config('media.allowed_file_extensions', []);
                $fileExtension = strtolower($item->getClientOriginalExtension());


                if (!in_array($fileExtension, $allowedExtensions)) {

                    $this->message = 'Filen skal være en af følgende filtyper: ' . implode(', ', $allowedExtensions);
                    return false;

                }


                if ($item->getSize() > 5 * 1024 * 1024) {

                    $this->message = 'Filen må ikke overstige 5MB.';
                    return false;

                }


                continue;

            }


            // Hvis ingen af ovenstående, fejler valideringen
            $this->message = 'Filen er ikke en gyldig uploadet fil eller et gyldigt id.';

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
