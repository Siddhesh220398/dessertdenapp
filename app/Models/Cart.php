<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $guarded = ['id'];

    public function cake() {
    	return $this->belongsTo('\App\Models\Cake');
    }

    public function product() {
    	return $this->belongsTo('\App\Models\Product');
    }
    public function flavour() {
    	return $this->belongsTo('\App\Models\Flavour');
    }
}
