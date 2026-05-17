<?php

namespace Modules\Media\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Media\Models\MediaLibrary;

class ImageRule implements Rule
{
    /**
     * Mæssig reglerne for validering
     *
     * @param  mixed  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Hvis der er ingen fil, er den gyldig
        if (!$value) {
            return true;
        }

        // Tjek om filen er et billede
        if (is_int($value) || is_numeric($value))
        if(MediaLibrary::find($value)) {

            return true;
            
        } else {

            return false;

        }

      
        // Tjek om filen er et billede
        if (!$value->isValid()) {
            return false;
        }

        // Tjek om MIME-typen er tilladt (jpg, jpeg, png, gif)
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($value->getMimeType(), $allowedMimes)) {
            return false;
        }

        // Tjek om filen ikke overskrider 2MB
        if ($value->getSize() > 2048 * 1024) {
            return false;
        }

        return true;
    }

    /**
     * Fejlmeddelelsen, der returneres, hvis valideringen mislykkes
     *
     * @return string
     */
    public function message()
    {
        return 'Den uploadede fil skal være et billede af typen jpg, jpeg, png eller gif og må ikke overstige 2MB.';
    }
}
