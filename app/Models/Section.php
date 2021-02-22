<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $guarded = ['id'];

    public function roles() {
    	return $this->hasMany('App\Models\Role')->where('active', '=', 1)->orderBy('sequence', 'asc');
    }
}
