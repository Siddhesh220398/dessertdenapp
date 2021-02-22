<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FranchisePrice extends Model
{
   protected $guarded = ['id'];

   public function franchise() {
     	return $this->belongsTo('\App\Franchise');
    }
   public function category() {
     	return $this->belongsTo('\App\Models\Category');
    }
}
