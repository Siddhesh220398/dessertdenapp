<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = ['id'];

     public function weights() {
     	return $this->hasMany('\App\Models\Weight');
     }

     public function flavours() {
     	return $this->belongsToMany('\App\Models\Flavour')->withTimestamps();
     }

     public function categories() {
     	return $this->belongsTo('\App\Models\SubCategoryModel');
     }

     public function prices() {
          return $this->belongsToMany('\App\Models\PriceModel');
     }
}
