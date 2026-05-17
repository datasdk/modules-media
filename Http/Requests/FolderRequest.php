<?php

namespace Modules\Media\Http\Requests;

use Orion\Http\Requests\Request;

class FolderRequest extends Request
{
    // Validation rules for storing a folder
    public function storeRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:folders,id',
        ];
    }

    // Validation rules for updating a folder
    public function updateRules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'parent_id' => 'sometimes|nullable|exists:folders,id',
        ];
    }
}
