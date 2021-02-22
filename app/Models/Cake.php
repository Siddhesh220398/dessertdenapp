<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cake extends Model
{
     protected $guarded = ['id'];

     public function weights() {
     	return $this->hasMany('\App\Models\Weight');
     }

     public function flavours() {
     	return $this->belongsToMany('\App\Models\Flavour')->withTimestamps();
     }

     public function categories() {
     	return $this->belongsToMany('\App\Models\Category')->withTimestamps();
     }

     public function prices() {
          return $this->belongsToMany('\App\Models\PriceModel');
     }

      public function priceCat() {
     return $this->hasMany('App\Models\PriceCategoryModel');
    } 


}
