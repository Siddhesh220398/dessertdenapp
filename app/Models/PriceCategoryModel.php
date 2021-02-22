<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceCategoryModel extends Model
{
      protected $guarded = ['id'];

    public function price()
    {
    	return $this->belongsTo('\App\Models\PriceModel');
    }
}
