<?php

namespace Modules\Media\Database\factories;

use DataSDK\Categories\Database\Factories\CategoriesFactory as OrigCategoriesFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Media\Models\Categories;


class CategoriesFactory extends OrigCategoriesFactory
{
 
    protected $model = Categories::class;


}

  
