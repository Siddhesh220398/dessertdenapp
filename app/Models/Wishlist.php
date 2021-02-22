<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    protected $guarded = ['id'];

    public function product() {
    	return $this->belongsTo('App\Models\Product');
    }

    public function franchise() {
    	return $this->belongsTo('App\Models\Franchise');
    }
    
    public function user() {
    	return $this->belongsTo('App\User');
    }
}
