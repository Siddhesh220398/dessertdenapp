<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderImage extends Model
{
    protected $guarded = ['id'];

    public function order() {
    	return $this->belongsTo('App\Models\Order');
    }
}
