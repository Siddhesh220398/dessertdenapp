<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Model;



class PriceModel extends Model

{

    protected $guarded = ['id'];

    protected $hidden = ['pivot'];



    public function priceCat() {

    	return $this->hasMany('App\Models\PriceCategoryModel', 'price_id');

    } 

//     public function getPriceIntAttribute($value) {
// return ucfirst($value);
//     }

}

