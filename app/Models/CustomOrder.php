<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomOrder extends Model
{
     protected $guarded = ['id'];

    public function order() {
    	return $this->belongsTo('App\Models\Order','order_id','id');
    }

    public function subcategory() {
    	return $this->belongsTo('App\Models\SubCategoryModel');
    }

    public function flavour() {
    	return $this->belongsTo('App\Models\Flavour');
    }

    public function assignorder() {
        return $this->belongsTo('App\Models\AssignOrder');
    }

}
