<?php

namespace Modules\Media\Models;

use Modules\Media\Contracts\HasMedia;
use Modules\Media\Traits\InteractsWithMedia;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User as OrigUser;


class User extends Authenticatable implements HasMedia
{
    use InteractsWithMedia;
    use HasFactory;


    protected $table = "users";


}
