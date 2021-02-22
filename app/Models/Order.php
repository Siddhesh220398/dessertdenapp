<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Order extends Model{

    protected $guarded = ['id'];

    public function items() {
    	return $this->hasMany('App\Models\OrderItem');
    }

     public function customitem() {
        return $this->hasOne('App\Models\CustomOrder');
    }


    public function franchise() {
    	return $this->belongsTo('\App\Franchise');
    }

    public function franchises() {
        return $this->belongsTo('\App\Franchise','franchises_id','id');
    }

    public function subcategory() {
        return $this->belongsTo('\App\Models\SubCategoryModel');
    }

    public function flavour() {
        return $this->belongsTo('\App\Models\Flavour');
    }

    public function city() {
    	return $this->belongsTo('\App\Models\City','city_id','id');
    }

    public function time() {
        return $this->belongsTo('\App\Models\Times');
    }

    public function user() {
        return $this->belongsTo('\App\User');
    }



    public function images() {
        return $this->hasMany('App\Models\OrderImage');
    }

    public function assignorder() {
        return $this->belongsTo('App\Models\AssignOrder');
    }

    public function assignsorders() {
        return $this->belongsTo('\App\Models\AssignsOrder');
    }
}

