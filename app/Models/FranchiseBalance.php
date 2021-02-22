<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FranchiseBalance extends Model
{
    protected $guarded = ['id'];


   public function franchise() {
     	return $this->belongsTo('\App\Franchise');
    }
}
