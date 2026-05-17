<?php

namespace Modules\Media\Http\Requests;

use Orion\Http\Requests\Request;

class MediaRequest extends Request
{
    // Get the MIME types from the config file and format as a string
    private function allowedMimes(): string
    {

        return implode(',', config('media.allowed_file_extensions'));
    }

    // Validation rules for storing media
    public function storeRules(): array
    {
        $allowedMimes = $this->allowedMimes();

        return [
            // Ensures 'file' is a file (and not an array of files)
            "file" => "required_without:files|file|mimes:" . $allowedMimes,
            // If 'files' is provided, ensure it's an array of files
            "files" => "required_without:file|array",
            "files.*" => "required_without:file|file|mimes:" . $allowedMimes,
        ];
    }

    // Validation rules for updating media
    public function updateRules(): array
    {
        $allowedMimes = $this->allowedMimes();

        return [
            "file" => "sometimes|file|mimes:" . $allowedMimes,
            "files" => "sometimes|array",
            "files.*" => "sometimes|file|mimes:" . $allowedMimes,
        ];
    }
}
