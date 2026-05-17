<?php 

namespace Modules\Media\Models;

use ActionModel;
use Categorizable;
use Model;
use DataSDK\Tools\Traits\Sorting;

use Spatie\Tags\HasTags;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

use Modules\Media\Traits\InteractsWithMedia;
use Modules\Media\Contracts\HasMedia;



class Categories extends \DataSDK\Categories\Models\Categories implements HasMedia {
    
    use InteractsWithMedia;


}


class Category extends Categories{
}


class CategoriesModels extends Model {

    public $timestamps = false;

    protected $fillable = [
        "category_id",
        "model_type",
        "model_id"
    ];

    
    public function category(){

        return $this->morphTo("model_type","model_id");

    }
}
