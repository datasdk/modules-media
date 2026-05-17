<?php

namespace Modules\Media\Models;

use Illuminate\Database\Eloquent\Model;
Use DataSDK\Tools\Traits\Nestable;

use Illuminate\Database\Eloquent\Factories\HasFactory;


class Folders extends Model
{
    
    use Nestable;
    use HasFactory;

    protected $fillable = ['name', 'parent_id'];
}
