<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Weight extends Model
{
    protected $guarded = ['id'];

    public function cake()
    {
    	return $this->belongsTo('\App\Models\Cake');
    }
    public function product()
    {
    	return $this->belongsTo('\App\Models\Product');
    }
}
