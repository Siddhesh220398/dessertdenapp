<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = ['id'];
    public function subcategory()
    {
    	return $this->hasMany('\App\Models\SubCategoryModel')->withTimestamps();
    }
}
