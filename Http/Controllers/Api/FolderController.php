<?php

namespace Modules\Media\Http\Controllers\Api;

use App\Http\Controllers\OrionBaseController;
use Orion\Http\Requests\Request;
use Modules\Media\Models\Categories;
use Modules\Media\Models\Folders;
use Modules\Media\Http\Requests\FolderRequest;


class FolderController extends OrionBaseController
{

    protected $model = Folders::class;
    protected $request = FolderRequest::class;

}