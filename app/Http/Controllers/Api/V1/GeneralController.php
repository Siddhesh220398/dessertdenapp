<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Banners;
use App\Models\Category;
use App\Models\City;
use App\Models\Flavour;
use App\Models\Franchise;
use App\Models\PriceCategoryModel;
use App\Models\PriceModel;
use App\Models\SubCategoryModel;
use App\Models\Times;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GeneralController extends Controller
{
	private function categoryFields($categories) {
		$fields = [];
		foreach ($categories as $category) {
			$fields[] = [
				'id' => $category->id,
				'name' => $category->name,
				'sequence' => $category->sequence,
				'image' => Storage::url('app/public/'.$category->image),
				'type'=>$category->type,
			];
		}
		return $fields;
	}

	private function subcategoryFields($subcategories) {
		$fields = [];
		foreach ($subcategories as $subcategory) {
			$fields[] = [
				'id' => $subcategory->id,
				'category'=>$subcategory->category->name,
				'category_id'=>$subcategory->category_id,
				'name' => $subcategory->name,
				'subcategory_type'=>$subcategory->subcat_type,
				'image' => Storage::url('app/public/'.$subcategory->image)
			];
		}
		return $fields;
	}

	private function priceFields($prices) {
		$fields = [];
		foreach ($prices as $price) {
			$fields[] = [
				'id' => $price->id,
				'name' => $price->name,
				'price' => $price->price,
			];
		}
		return $fields;
	}
	private function pricecatFields($prices) {
		$fields = [];
		foreach ($prices as $price) {
			$fields[] = [
				'id' => $price->id,
				'name' => $price->cat_name,
				'price' => $price->price,
			];
		}
		return $fields;
	}
	private function cityFields($cities) {
		$fields = [];
		foreach ($cities as $city) {
			$fields[] = [
				'id' => $city->id,
				'name' => $city->name,
				'city_type' => $city->city_type,
			];
		}
		return $fields;
	}
	private function bannerFields($banners) {
		$fields = [];
		foreach ($banners as $banner) {
			$fields[] = [
				'id' => $banner->id,
				'sequence' => $banner->serial,
				'image' => Storage::url('app/public/'.$banner->image),
			];
		}
		return $fields;
	}
	private function flavourFields($flavours) {
		$fields = [];
		foreach ($flavours as $flavour) {
			$fields[] = [
				'id' => $flavour->id,
				'name' => $flavour->name,
				'rate' => $flavour->rate,

			];
		}
		return $fields;
	}
	private function franchiseFields($franchises) {
		$fields = [];
		foreach ($franchises as $franchise) {
			$fields[] = [
				'id' => $franchise->id,
				'name' => $franchise->name,
				'address' => $franchise->address,
				'city' => $franchise->city->name,
				'city_type' => $franchise->city->city_type,
				'mobile_no' => $franchise->mobile_no,
				'opening_time' => \Carbon\Carbon::parse($franchise->opening_time)->format('h:i A'),
				'closing_time' => \Carbon\Carbon::parse($franchise->closing_time)->format('h:i A'),
			];
		}
		return $fields;
	}
	private function deliveryTimesFields($deliveryTimes) {
		$fields = [];
		foreach ($deliveryTimes as $deliveryTime) {
			$fields[] = [
				'id' => $deliveryTime->id,
				'startingtime' => \Carbon\Carbon::parse($deliveryTime->startingtime)->format('h:i A'),
				'endingtime' => \Carbon\Carbon::parse($deliveryTime->endingtime)->format('h:i A'),
				'hours' => $deliveryTime->hours,
			];
		}
		return $fields;
	}

	public function categories(Request $request) {
		$categories = Category::where('active', 1)->orderBy('sequence','ASC')->get();
		$this->status = 200;
		$this->response['data'] = $this->categoryFields($categories);
		$this->response['message'] = trans('api.list', ['entity' => 'Categories']);
		return $this->return_response();
	}

	public function subcategories(Request $request) {
		$rules=[
			'category_id'=>'required|exists:categories,id'
		];
		if ($this->ApiValidator($request->all(), $rules)) {

			$category_id=$request->category_id;
			$subcategories = SubCategoryModel::where('category_id', $category_id)->get();
			$this->status = 200;
			$this->response['data'] = $this->subcategoryFields($subcategories);
			$this->response['message'] = trans('api.list', ['entity' => 'Categories']);
		}
		return $this->return_response();
	}

	public function subcategoriesCake(Request $request) {

		$subcategories = SubCategoryModel::where('subcat_type', 0)->get();
		$this->status = 200;
		$this->response['data'] = $this->subcategoryFields($subcategories);
		$this->response['message'] = trans('api.list', ['entity' => 'Categories']);

		return $this->return_response();
	}

	public function cities(Request $request) {
		$categories = City::where('active', 1)->get();
		$this->status = 200;
		$this->response['data'] = $this->cityFields($categories);
		$this->response['message'] = trans('api.list', ['entity' => 'Cities']);
		return $this->return_response();
	}

	public function franchises(Request $request)
	{
		$franchises=Franchise::where('active', 1)->get();
		$this->status = 200;
		$this->response['data'] = $this->franchiseFields($franchises);
		$this->response['message'] = trans('api.list', ['entity' => 'franchises']);
		return $this->return_response();
	}

	public function banners(Request $request)
	{
		$banners=Banners::where('active', 1)->get();
		$this->status = 200;
		$this->response['data'] = $this->bannerFields($banners);
		$this->response['message'] = trans('api.list', ['entity' => 'banners']);
		return $this->return_response();
	}
	public function flavours(Request $request)
	{
		$flavours=Flavour::where('active', 1)->get();
		$this->status = 200;
		$this->response['data'] = $this->flavourFields($flavours);
		$this->response['message'] = trans('api.list', ['entity' => 'flavours']);
		return $this->return_response();
	}

	public function deliveryTimes(Request $request)
	{
		$deliveryTimes=Times::where('active', 1)->get();
		$this->status = 200;
		$this->response['data'] = $this->deliveryTimesFields($deliveryTimes);
		$this->response['message'] = trans('api.list', ['entity' => 'deliveryTimes']);
		return $this->return_response();
	}

	public function price(Request $request)
	{
		$price = PriceModel::where('active', 1)->get();
		$this->status = 200;
		$this->response['data'] = $this->priceFields($price);
		$this->response['message'] = trans('api.list', ['entity' => 'Types']);
		return $this->return_response();
	}

	public function priceCat(Request $request)
	{
		$price = PriceCategoryModel::where('active', 1)->where('price_id','!=',2)->get();
		$this->status = 200;
		$this->response['data'] = $this->pricecatFields($price);
		$this->response['message'] = trans('api.list', ['entity' => 'Types']);
		return $this->return_response();
	}
	public function pricePhoto(Request $request)
	{
		$price = PriceCategoryModel::where(['price_id'=>2,'active'=> 1])->get();
		$this->status = 200;
		$this->response['data'] = $this->pricecatFields($price);
		$this->response['message'] = trans('api.list', ['entity' => 'Types']);
		return $this->return_response();
	}
}
