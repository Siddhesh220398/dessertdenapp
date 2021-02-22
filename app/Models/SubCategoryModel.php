<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

    class SubCategoryModel extends Model
{
    protected $guarded = ['id'];

    public function category() {
     	return $this->belongsTo('\App\Models\Category');
     }
}
