<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignsOrder extends Model
{
    protected $guarded = ['id'];

    public function order() {
    	return $this->belongsTo('App\Models\Order');
    }
    public function product() {
    	return $this->belongsTo('App\Models\Order');
    }

    public function admin() {
    	return $this->belongsTo('App\Admin','admin_id');
    }


}
