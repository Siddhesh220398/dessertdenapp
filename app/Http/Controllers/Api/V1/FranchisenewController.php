<?php
namespace App\Http\Controllers\Api\V1;

use App\Admin;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CustomOrder;
use App\Models\Flavour;
use App\Models\Franchise;
use App\Models\FranchiseBalance;
use App\Models\FranchisePrice;
use App\Models\Order;
use App\Models\OrderImage;
use App\Models\OrderItem;
use App\Models\PriceCategoryModel;
use App\Models\Product;
use App\Models\SubCategoryModel;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Razorpay\Api\Api;

class FranchisenewController extends Controller
{

	public function customPlaceOrder(Request $request){
	
		$rules = [
			'shipping_method' => 'required|in:pickup,homedelivery',
			'franchises_id'=> 'required|exists:franchises,id',
			'address'=>   'required_if:shipping_method,homedelivery',
			'city_id'=>   'required_if:shipping_method,homedelivery|exists:cities,id',
			'zip'=>   'required_if:shipping_method,homedelivery',
			'delivery_date'=>   'required|date_format:d-m-Y',
			'delivery_time_id'=>   'required|exists:times,id',
			'subcategory_id'=>'required|exists:sub_category_models,id',
			'flavour_id'=>'required|exists:flavours,id',
			'weight' => 'required',
			'idea' => 'array',
			'idea.*' => 'nullable|image',
			'cake' => 'array',
			'cake.*' => 'nullable|image',
		];

		if ($this->ApiValidator($request->all(), $rules)) {
			
			if(!empty($request->idea)){
				
				foreach ($request->idea as $image) {
					$newname=$image->getClientOriginalName();
					$order_images[] = new OrderImage(['image' => 'orders/'.$newname,'type' => 'idea']);
					$image->move(public_path('orders'), $newname);
				}
			}
			
			if(!empty($request->cake)){
				
				foreach ($request->cake as $image) {
					$newname=$image->getClientOriginalName();
					$order_images[] = new OrderImage(['image' =>'orders/'.$newname,'type' => 'cake']);
					$image->move(public_path('orders'), $newname);
				}
			}

			$category_id=SubCategoryModel::where('id',$request->subcategory_id)->value('category_id');

			$discount_price1=(FranchisePrice::where(['franchise_id'=>$request->franchises_id,'category_id'=>$category_id])->value('percentage'));
			
			$discount_price= !empty($discount_price1) ? $discount_price1 : 0;

			$flavour_price = Flavour::where('id',$request->flavour_id)->value('rate');
			$total=0;
			if(!empty($request->typeRate)){
				foreach ($request->typeRate as $type_id) {
					$typeRate = PriceCategoryModel::where('id',$type_id)->value('price');
					if(!empty($typeRate)){
						$total +=$typeRate;
					}else{
						$this->response['message'] = "Type Id Is invalid";	
					}
				}
			}

			if(!empty($request->photoprice_id)){
			
				$photoprice_id=PriceCategoryModel::where('id',$request->photoprice_id)->value('price');
			}
			
			$photoprice = (!empty($photoprice_id)) ? $photoprice_id : 0 ;

			$discountamount = (((($flavour_price + $total ) * $request->weight) + $photoprice) * $discount_price) / 100;
			
			$totaldis=(($flavour_price + $total ) * $request->weight) + $photoprice ;
			
			$amount = $totaldis - $discountamount;
// dd($amount);
			if($amount == $request->amount){
				
				$order_items = new CustomOrder([
					'sub_category_id'=>$request->subcategory_id,
					'flavour_id'=>$request->flavour_id,
					'weight' => $request->weight,
					'theme'=>$request->theme,
					'size'=>$request->size,
					'amount'=>$amount,
					'message_on_cake' => $request->message_on_cake,
					'instruction' => $request->instruction,
					'customer_no' => $request->customer_no,
					'customer_name' => $request->customer_name,

				]);

				$last_id = Order::latest()->value('id');
				$last_id = (!empty($last_id) ? $last_id+1 : 1);
				$order = new Order;
				$order->franchises_id= $request->franchises_id;
				$order->order_no = date('Ymd') . "/" . time() . "/" . $last_id;
				$order->shipping_method=$request->shipping_method;
				$order->type='Custom';
				$order->city_id=$request->city_id;
				$order->address=$request->address;
				$order->zip=$request->zip;
				$order->delivery_date=\Carbon\Carbon::parse($request->delivery_date)->format('Y-m-d');
				$order->time_id=$request->delivery_time_id;
				$order->status = 'place_order';
				$order->total_amount = $amount;
				$order->save();
				$order->customitem()->save($order_items);
				
				if(!empty($order_images)){
					$order->images()->saveMany($order_images);
				}

				if (!empty(Franchise::where('id',$request->franchises_id)->value('push_token'))) {
					sendPushMessage(Franchise::where('id',$request->franchise_id)->value('push_token'), "Order placed successfully");
				}
				
				$admin_token = Admin::where('type', 'Admin')->value('push_token');
				
				if (!empty($admin_token)) {
					sendPushMessage($admin_token, "New order received");
				} 
				
				$this->status = 200;
				$this->response['message'] = trans('api.orderlist' , ['entity' => 'Order']); 
			
			}else{
			
				$this->response['message'] = "Amount mismatched!"; 
			}

		}
		return $this->return_response();
	}

}