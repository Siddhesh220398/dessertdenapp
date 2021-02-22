<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Flavour extends Model
{
    protected $guarded = ['id'];

    protected $hidden = ['pivot'];

}
