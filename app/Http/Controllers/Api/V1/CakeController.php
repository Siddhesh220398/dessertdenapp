<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Cake;
use App\Models\PriceCategoryModel;
use App\Models\PriceModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class CakeController extends Controller
{
	private function cakesFields($cakes) {
		$fields = [];
		foreach ($cakes as $cake) {
			$fields[] = [
				'id' => $cake->id,
				'name' => $cake->name,
				'code' => $cake->code,
				'weight'=>$cake->weights->weight,
				'image' => Storage::url($cake->image),
				'type' => $cake->prices()->pluck('name')->toArray(),
				'default' => $cake->flavours()->where('is_default', 1)->first(['flavours.id', 'flavours.name', 'flavours.rate'])->toArray()
			];
		}
		return $fields;
	}

	private function cakeDetailsFields($cake) {
		$prices = [];	
		foreach ($cake->prices as $price ) {
			$prices[] = ['type' => $price->name, 'values' => $price->priceCat()->get(['cat_name', 'price'])];
		}
		// dd($price);

		return [
			'id' => $cake->id,
			'name' => $cake->name,
			'code' => $cake->code,
			'image' => Storage::url($cake->image),
			'description' => $cake->description,
			'weights' => $cake->weights()->pluck('weight')->toArray(),
			'categories' => $cake->categories()->pluck('name')->toArray(),
			'types' => $prices,
			'flavours' => $cake->flavours()->get(['flavours.id', 'flavours.name', 'flavours.rate', 'is_default'])->toArray()
		];
	}
	
   	public function cakes(Request $request)
   	{
   		$rules = [
    		'category_id' => 'nullable|exists:categories,id',
    		'search' => 'nullable',
    	];

    	if ($this->ApiValidator($request->all(), $rules)) {
	   		$cakes=Cake::where('active', 1);

	   		if (!empty($request->category_id)) {
	   			$cakes->whereHas('categories', function($q) use($request) {
	   				$q->where('categories.id', $request->category_id);
	   			});
	   		}

	   		if (!empty($request->search)) {
	   			$cakes->where('name', 'like', '%' . $request->search . '%');
	   			$cakes->orWhere('code', 'like', '%' . $request->search . '%');
	   		}

	   		$cakes = $cakes->get();
	    	$this->status = 200;
			$this->response['data'] = $this->cakesFields($cakes);
			$this->response['message'] = trans('api.list', ['entity' => 'Cakes']);
		}
		return $this->return_response();
    }

    public function cakeDetails(Request $request) {
    	$rules = [
    		'cake_id' => 'required|exists:cakes,id'
    	];

    	if ($this->ApiValidator($request->all(), $rules)) {
    		$cake = Cake::find($request->cake_id);
    		$this->status = 200;
			$this->response['data'] = $this->cakeDetailsFields($cake);
			$this->response['message'] = trans('api.list', ['entity' => 'Cake details']);
    	}
    	return $this->return_response();
    }

    public function today_special(Request $request)
    {
    	$cakes = Cake::all()->random(2);
    	$this->status = 200;
		$this->response['data'] = $this->cakesFields($cakes);
		$this->response['message'] = trans('api.list', ['entity' => 'Cakes']);
		return $this->return_response();
    }

    
   
}
