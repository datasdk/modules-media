<?php

namespace Modules\Media\Http\Controllers\Api;

use App\Http\Controllers\OrionBaseController;
use Orion\Http\Requests\Request;
use Modules\Media\Models\Categories;



class CategoriesController extends \DataSDK\Categories\Http\Controllers\Api\CategoriesController
{

    protected $model = Categories::class;


}
