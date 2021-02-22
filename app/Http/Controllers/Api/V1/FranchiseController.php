<?php

namespace App\Http\Controllers\Api\V1;

use App\Admin;
use App\Franchise;
use App\Http\Controllers\Controller;
use App\Model\FranchiseStock;
use App\Models\AssignOrder;
use App\Models\Cart;
use App\Models\Category;
use App\Models\CustomOrder;
use App\Models\Flavour;
use App\Models\FranchiseBalance;
use App\Models\FranchisePrice;
use App\Models\FranchiseToken;
use App\Models\Order;
use App\Models\OrderImage;
use App\Models\OrderItem;
use App\Models\PriceCategoryModel;
use App\Models\Product;
use App\Models\SubCategoryModel;
use App\Models\Wishlist;
use App\SaleReturn;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Razorpay\Api\Api;

class FranchiseController extends Controller
{
    private function franchiseFields($franchise)
    {
        return [
            'id' => $franchise->id,
            'name' => $franchise->name,
            'city_type' => $franchise->city->city_type,
            'city' => $franchise->city->name,
            'city_id' => $franchise->city_id,
            'balance' => !empty($franchise->balance) ? $franchise->balance : 0,
            'email' => $franchise->email,
            'additionaladdress' => $franchise->additionaladdress,
            'mobile_no' => $franchise->mobile_no,
            // 'profile'=>(!empty($franchise->profile) ? $admin->profile : asset('theme/images/default_profile.jpg')),
        ];
    }

    private function franchiseDiscountFields($id)
    {
        $price = FranchisePrice::where('id', $id)->first();
        // dd($price);
        return [
            'id' => $price->id,
            'category' => $price->category->name,
            'percentage' => $price->percentage,

        ];
    }

    private function cartFields($franchise_id)
    {
        $items = Cart::where('franchise_id', $franchise_id)->get();
        $fields = ['total' => 0, 'items' => []];
        foreach ($items as $item) {
            $product = Product::where('id', $item->product_id)->first();
            $priceRateName = [];
            if (!empty($item->type_rate)) {
                $type_ids = json_decode($item->type_rate);
                $ar = [];
                foreach ($type_ids as $key => $value) {
                    $priceRate = PriceCategoryModel::where('id', $value)->first();                    // array_push($priceRateName, $priceRate);
                    $ar = [
                        'name' => $priceRate->cat_name,
                        'price' => $priceRate->price,
                    ];
                }
                $priceRateName[] = $ar;
            }

            $photo_rate = [];
            if (!empty($item->is_photo)) {
                $photoRate = PriceCategoryModel::where('id', $item->is_photo)->first();
                $photo_rate[] = [
                    'name' => $photoRate->cat_name,
                    'price' => $photoRate->price,
                ];

            }
// dd($product);
            $subcategory = SubCategoryModel::where('id', $product->subcategory_id)->first();
            $percentage = (FranchisePrice::where(['franchise_id' => $franchise_id, 'category_id' => $subcategory->category_id])->value('percentage'));
            $fields['items'][] = [
                'item_id' => $item->id,
                'product_name' => $product->name,
                'product_price' => $product->price,
                'subcategory' => $subcategory->name,
                'discountpercent' => $percentage,
                'subcategory_type' => $subcategory->subcat_type,
                'flavour' => !empty($item->flavour->name) ? $item->flavour->name : NULL,
                'image' => !empty($product->image) ? Storage::url($product->image) : NULL,
                'amount' => $item->amount,
                'quantity' => $product->quantity,
                'weight' => !empty($item->weight) ? $item->weight : NULL,
                'size' => !empty($item->size) ? $item->size : NULL,
                'cake_image' => (!empty($item->image)) ? url('public/orders/' . $item->image) : NULL,
                'message_on_cake' => !empty($item->message_on_cake) ? $item->message_on_cake : NULL,
                'instruction' => !empty($item->instruction) ? $item->instruction : NULL,
                'quantity' => !empty($item->qty) ? $item->qty : NULL,
                'customer_no' => !empty($item->customer_no) ? $item->customer_no : NULL,
                'customer_name' => !empty($item->customer_name) ? $item->customer_name : NULL,
                'is_photo' => $photo_rate,
                'note' => $item->note,
                'delivery_date' => \Carbon\Carbon::parse($item->delivery_date)->format('d-m-Y'),
                'typeRate' => $priceRateName,
            ];
            $fields['total'] += $item->amount;
        }
        return $fields;
    }

    private function orderListFields($orders)
    {
        $fields = [];

        foreach ($orders as $order) {
            $data = [
                'order_id' => $order->id,
                'order_no' => $order->order_no,
                'payment_method' => $order->payment_method,
                'status' => $order->status,
                'delivery_date' => \Carbon\Carbon::parse($order->delivery_date)->format('d-m-Y'),
                'delivery_time' => \Carbon\Carbon::parse($order->time->startingtime)->format('h:i A') . ' to ' . \Carbon\Carbon::parse($order->time->endingtime)->format('h:i A'),
                'type' => $order->type,
                'note' => $order->note,
                'total_amount' => $order->total_amount,
                'real_amount' => $order->real_amount
            ];
            $data['items'] = [];
            if ($order->type == 'Normal') {
                foreach ($order->items as $item) {
                    $priceRateName = [];
                    if (!empty($item->type_rate)) {
                        $type_ids = json_decode($item->type_rate);
                        $ar = [];
                        foreach ($type_ids as $key => $value) {
                            $priceRate = PriceCategoryModel::where('id', $value)->first();                    // array_push($priceRateName, $priceRate);
                            $ar = [
                                'name' => $priceRate->cat_name,
                                'price' => $priceRate->price,
                            ];
                        }
                        $priceRateName[] = $ar;
                    }

                    $photo_rate = [];
                    if (!empty($item->is_photo)) {
                        $photoRate = PriceCategoryModel::where('id', $item->is_photo)->first();
                        $photo_rate[] = [
                            'name' => $photoRate->cat_name,
                            'price' => $photoRate->price,
                        ];

                    }

                    $subcategory = SubCategoryModel::where('id', $item->product->subcategory_id)->first();
                    $percentage = 0;
                    $subcat_type = SubCategoryModel::where('id', $item->product->subcategory_id)->value('subcat_type');

                    $percentage = (FranchisePrice::where(['franchise_id' => $order->franchises_id, 'category_id' => $subcategory->category_id])->value('percentage'));
                    $data['items'][] = [
                        'item_id' => $item->id,
                        'product_id' => $item->product->id,
                        'product_name' => $item->product->name,
                        'product_price' => $item->product->price,
                        'discount' => $percentage,
                        'flavour' => !empty($item->flavour_id) ? $item->flavour->name : NULL,
                        'flavour_id' => !empty($item->flavour_id) ? $item->flavour->id : NULL,
                        'flavour_price' => !empty($item->flavour_id) ? $item->flavour->rate : NULL,
                        'image' => Storage::url($item->product->image),
                        'amount' => $item->amount,
                        'subcat_type' => $subcat_type,
                        'weight' => !empty($item->weight) ? $item->weight : NULL,
                        'qty' => !empty($item->qty) ? $item->qty : NULL,
                        'voice_msg' => !empty($item->voice_msg) ? url('public/voice' . $item->voice_msg) : NULL,
                        'cake_image' => (!empty($item->image)) ? url('public/orders/' . $item->image) : NULL,
                        'completed_image' => (!empty($item->completed_image)) ? Storage::url($item->completed_image) : NULL,
                        'customer_name' => $item->customer_name,
                        'customer_no' => $item->customer_no,
                        'message_on_cake' => $item->message_on_cake,
                        'instruction' => $item->instruction,
                        'size' => $item->size,
                        'typeRate' => $priceRateName,
                        'delivery_date' => !empty($item->delivery_date) ? \Carbon\Carbon::parse($item->delivery_date)->format('d-m-Y') : \Carbon\Carbon::parse($order->delivery_date)->format('d-m-Y'),
                        'note' => $item->note,
                        'is_photo' => $photo_rate,
                        'customer_no' => !empty($item->customer_no) ? $item->customer_no : NULL,
                        'customer_name' => !empty($item->customer_name) ? $item->customer_name : NULL,
                    ];
                }
            } else {
                $item = $order->customitem;
                if (!empty($item)) {
                    $name = SubCategoryModel::where('id', $item->subcategory_id)->value('name');
                    $priceRateName = [];
                    if (!empty($item->type_rate)) {
                        $type_ids = json_decode($item->type_rate);
                        $ar = [];
                        foreach ($type_ids as $key => $value) {
                            $priceRate = PriceCategoryModel::where('id', $value)->first();                    // array_push($priceRateName, $priceRate);
                            $ar = [
                                'name' => $priceRate->cat_name,
                                'price' => $priceRate->price,
                            ];
                        }
                        $priceRateName = $ar;
                    }

                    $photo_rate = [];
                    if (!empty($item->is_photo)) {
                        $photoRate = PriceCategoryModel::where('id', $item->is_photo)->first();
                        $photo_rate = [
                            'name' => $photoRate->cat_name,
                            'price' => $photoRate->price,
                        ];

                    }
                    $subcategory = SubCategoryModel::where('id', $item->sub_category_id)->first();
                    $percentage = (FranchisePrice::where(['franchise_id' => $order->franchises_id, 'category_id' => $subcategory->category_id])->value('percentage'));
                    $data['items'][] = [
                        'theme' => $item->theme,
                        'item_id' => $item->id,
                        'discount' => $percentage,
                        'subcategory_id' => $item->sub_category_id,
                        'subcategory' => $subcategory->name,
                        'flavour_id' => $item->flavour->id,
                        'flavour' => $item->flavour->name,
                        'flavour_price' => $item->flavour->rate,
                        'amount' => $item->amount,
                        'size' => $item->size,
                        'weight' => $item->weight,
                        'edit' => $item->edit,
                        'reject_instruction' => $item->reject_instruction,
                        'message_on_cake' => $item->message_on_cake,
                        'instruction' => $item->instruction,
                        'completed_image' => (!empty($item->completed_image)) ? Storage::url($item->completed_image) : NULL,
                        'customer_no' => !empty($item->customer_no) ? $item->customer_no : NULL,
                        'customer_name' => !empty($item->customer_name) ? $item->customer_name : NULL,
                        'typeRate' => $priceRateName,
                        'is_photo' => $photo_rate,
                        'delivery_date' => \Carbon\Carbon::parse($item->delivery_date)->format('d-m-Y'),

                    ];
                    $data['idea_images'] = [];
                    $data['cake_images'] = [];

                    foreach ($order->images()->where(['type' => 'idea'])->get() as $item) {
                        $data['idea_images'][] = url('public/' . $item->image);;
                    }
                    foreach ($order->images()->where(['type' => 'cake'])->get() as $item) {
                        $data['cake_images'][] = url('public/' . $item->image);;
                    }
                }
                $fields[] = $data;
            }
        }
        return $fields;
    }

    private function wishListFields($franchise_id)
    {
        $items = Wishlist::where('franchise_id', $franchise_id)->get();
        $fields = ['items' => []];
        foreach ($items as $item) {
            $product = Product::where('id', $item->product_id)->first();
            // dd($product);
            $subcategory = SubCategoryModel::where('id', $product->subcategory_id)->first();
            // $category_id=SubCategoryModel::where('id',$request->subcategory_id)->value('category_id');
            // dd($category_id);
            $percentage = (FranchisePrice::where(['franchise_id' => $franchise_id, 'category_id' => $subcategory->category_id])->value('percentage'));


            $fields['items'][] = [
                'item_id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $product->name,
                'product_price' => $product->price,
                'discountpercent' => $percentage,
                'subcategory' => $subcategory->name,
                'subcategory_type' => $subcategory->subcat_type,
                'code' => $item->product->code,
                'quantity' => $item->product->quantity,
                'image' => !empty($item->product->image) ? Storage::url($item->product->image) : NULL,
                'description' => $product->description,
                'weights' => !empty($product->weights()->pluck('weight')->toArray()) ? $product->weights()->pluck('weight')->toArray() : NULL,
                // 'types' => (!empty($prices)) ? $prices : [],
                'flavours' => !empty($product->flavours()->get()) ? $product->flavours()->get(['flavours.id', 'flavours.name', 'flavours.rate', 'is_default'])->toArray() : NULL,
                'default' => !empty($product->flavours()->where('is_default', 1)->first()) ? $product->flavours()->where('is_default', 1)->first(['flavours.id', 'flavours.name', 'flavours.rate'])->toArray() : NULL,
            ];

        }
        return $fields;
    }

    private function assignsListFields($assignListitem)
    {

        $fields = [];

        foreach ($assignListitem as $assignOrder) {
            $order = $assignOrder->order;
            $data = [
                'order_id' => $order->id,
                'order_no' => $order->order_no,
                'payment_method' => $order->payment_method,
                'status' => $order->status,
                'shipping_method' => $order->shipping_method,
                'franchise' => !empty($order->franchise->name) ? $order->franchise->name : NULL,
                'address' => $order->address,
                'zip' => $order->zip,
                'total_amount' => $order->total_amount,
                'payment_method' => $order->payment_method,
                'delivery_date' => \Carbon\Carbon::parse($order->delivery_date)->format('d-m-Y'),
                'delivery_time' => \Carbon\Carbon::parse($order->time->startingtime)->format('h:i A') . ' to ' . \Carbon\Carbon::parse($order->time->endingtime)->format('h:i A'),
                'adminstatus' => $order->admin_status,
                'type' => $order->type,
                'note' => $order->note,
                'accept_time' => \Carbon\Carbon::parse($order->accept_time)->format('d-m-Y h:i A'),
                'start_preparing_time' => \Carbon\Carbon::parse($order->start_preparing_time)->format('d-m-Y h:i A'),
                'stop_preparing_time' => \Carbon\Carbon::parse($order->stop_preparing_time)->format('d-m-Y h:i A'),
                'way_to_delievered_time' => \Carbon\Carbon::parse($order->way_to_delievered_time)->format('d-m-Y h:i A'),
                'delieverd_time' => \Carbon\Carbon::parse($order->delieverd_time)->format('d-m-Y h:i A'),
                'delieverd_time_id' => !empty($order->time_id) ? $order->time_id : Null,
                'user_type' => !empty($order->user_id) ? 'customer' : 'franchise',
                'dept_type' => $order->p_type,
            ];
            $data['items'] = [];
            if ($order->type == 'Normal') {
                foreach ($order->items as $item) {
                    $priceRateName = [];
                    if (!empty($item->type_rate)) {
                        $type_ids = json_decode($item->type_rate);
                        $ar = [];
                        foreach ($type_ids as $key => $value) {
                            $priceRate = PriceCategoryModel::where('id', $value)->first();                    // array_push($priceRateName, $priceRate);
                            $ar = [
                                'name' => $priceRate->cat_name,
                                'price' => $priceRate->price,
                            ];
                        }
                        $priceRateName[] = $ar;
                    }

                    $photo_rate = [];
                    if (!empty($item->is_photo)) {
                        $photoRate = PriceCategoryModel::where('id', $item->is_photo)->first();
                        $photo_rate[] = [
                            'name' => $photoRate->cat_name,
                            'price' => $photoRate->price,
                        ];

                    }

                    $subcategory = SubCategoryModel::where('id', $item->product->subcategory_id)->first();
                    $percentage = 0;
                    if (!empty($order->franchises_id)) {

                        $percentage = (FranchisePrice::where(['franchise_id' => $order->franchises_id, 'category_id' => $subcategory->category_id])->value('percentage'));
                    }
                    $data['items'][] = [
                        'item_id' => $item->id,
                        'product_id' => $item->product->id,
                        'product_name' => $item->product->name,
                        'product_price' => $item->product->price,
                        'discount' => $percentage,
                        'flavour' => !empty($item->flavour_id) ? $item->flavour->name : NULL,
                        'flavour_id' => !empty($item->flavour_id) ? $item->flavour->id : NULL,
                        'flavour_price' => !empty($item->flavour_id) ? $item->flavour->rate : NULL,
                        'image' => !empty($item->product->image) ? Storage::url('app/public/' . $item->product->image) : NULL,
                        'amount' => $item->amount,
                        'weight' => !empty($item->weight) ? $item->weight : NULL,
                        'qty' => !empty($item->qty) ? $item->qty : NULL,
                        'customer_name' => $item->customer_name,
                        'customer_no' => $item->customer_no,
                        'voice_msg' => !empty($item->voice_msg) ? url('public/voice' . $item->voice_msg) : NULL,
                        'cake_image' => (!empty($item->image)) ? url('public/orders/' . $item->image) : NULL,
                        'completed_image' => (!empty($item->completed_image)) ? Storage::url('app/public/' . $item->completed_image) : NULL,
                        'message_on_cake' => $item->message_on_cake,
                        'instruction' => $item->instruction, 'is_photo' => $photo_rate,
                        'typeRate' => $priceRateName,
                        'is_photo' => $photo_rate,
                        'size' => $item->size,
                        'note' => $item->note,
                        'start_time' => $item->start_time,
                        'end_time' => $item->end_time,
                        // 		'delivery_date' =>\Carbon\Carbon::parse($item->delivery_date)->format('d-m-Y'),
                    ];
                }
            } else {
                $item = $order->customitem;

                $priceRateName = [];
                if (!empty($item->type_rate)) {
                    $type_ids = json_decode($item->type_rate);
                    $ar = [];
                    foreach ($type_ids as $key => $value) {
                        $priceRate = PriceCategoryModel::where('id', $value)->first();                    // array_push($priceRateName, $priceRate);
                        $ar = [
                            'name' => $priceRate->cat_name,
                            'price' => $priceRate->price,
                        ];
                    }
                    $priceRateName[] = $ar;
                }

                $photo_rate = [];
                if (!empty($item->is_photo)) {
                    $photoRate = PriceCategoryModel::where('id', $item->is_photo)->first();
                    $photo_rate[] = [
                        'name' => $photoRate->cat_name,
                        'price' => $photoRate->price,
                    ];

                }

                $subcategory_name = SubCategoryModel::where('id', $item->sub_category_id)->value('name');
                $subcategory = SubCategoryModel::where('id', $item->sub_category_id)->first();
                $percentage = 0;
                if (!empty($order->franchises_id)) {

                    $percentage = (FranchisePrice::where(['franchise_id' => $order->franchises_id, 'category_id' => $subcategory->category_id])->value('percentage'));
                }
                $data['items'][] = [

                    'item_id' => $item->id,
                    'subcategory_id' => $item->sub_category_id,
                    'subcategory' => $subcategory_name,
                    'discount' => $percentage,
                    'flavour_id' => $item->flavour->id,
                    'flavour' => $item->flavour->name,
                    'flavour_price' => $item->flavour->rate,
                    'amount' => $item->amount,
                    'weight' => $item->weight,
                    'theme' => $item->theme,
                    'pdf' => (!empty($item->pdf)) ? url('public/' . $item->pdf) : NULL,
                    'completed_image' => (!empty($item->completed_image)) ? Storage::url('app/public/' . $item->completed_image) : NULL,
                    'message_on_cake' => $item->message_on_cake,
                    'instruction' => $item->instruction,
                    'start_time' => $item->start_time,
                    'end_time' => $item->end_time,
                    'customer_name' => $item->customer_name,
                    'customer_no' => $item->customer_no,
                    'typeRate' => $priceRateName,
                    'is_photo' => $photo_rate,
                    // 		'delivery_date' =>\Carbon\Carbon::parse($item->delivery_date)->format('d-m-Y'),
                ];
                $data['idea_images'] = [];
                $data['cake_images'] = [];

                foreach ($order->images()->where(['type' => 'idea'])->get() as $item) {
                    $data['idea_images'][] = url('public/' . $item->image);
                }
                foreach ($order->images()->where(['type' => 'cake'])->get() as $item) {
                    $data['cake_images'][] = url('public/' . $item->image);
                }
            }

            $data['customer'] = !empty(Franchise::where('id', $order->franchises_id)->first(['id', 'name', 'address', 'mobile_no', 'email'])) ? Franchise::where('id', $order->franchises_id)->first(['id', 'name', 'address', 'mobile_no', 'email']) : $order->user()->first(['id', 'first_name', 'last_name', 'mobile_no', 'email']);

            $data['chef'] = [
                'id' => $assignOrder->admin->id,
                'name' => $assignOrder->admin->name,
                'type' => $assignOrder->admin->type
            ];

            $data['franchise'] = [
                'id' => $assignOrder->franchise->id,
                'name' => $assignOrder->franchise->name,
                'discount' => $assignOrder->discount,
                'instruction_franchise' => $assignOrder->instruction_franchise,

            ];

            $deliveryboy = Admin::where('id', $assignOrder->delivery_boy_id)->first();
            $data['delivery_boy'] = !empty($assignOrder->delivery_boy_id) ? [
                'id' => $deliveryboy->id,
                'name' => $deliveryboy->name,
                'type' => $deliveryboy->type
            ] : Null;


            $data['instruction'] = $assignOrder->instruction;
            $data['instruction_delivery_boy'] = $assignOrder->instruction_delivery_boy;

            $fields[] = $data;

        }
        return $fields;
    }

    private function salereturnFields($salereturns){
        $fields = [];
        foreach ($salereturns as $salereturn){
            $order_item=null;
            $custom_order=null;
            $flavour=null;

            $order=Order::where('id',$salereturn->order_id)->first();
            if($order->type == "Normal"){
                $order_item=OrderItem::where('id',$salereturn->item_id)->first();
                $flavour=($order_item->flavour_id)?$order_item->flavour->name:null;
            }else{
                $custom_order=CustomOrder::where('id',$salereturn->item_id)->first();
                $flavour=($custom_order->flavour_id)?$custom_order->flavour->name:null;
            }
            $data=[
                'sales_id'=>$salereturn->id,
                'date'=>$salereturn->date,
                'order_id'=>$salereturn->order_id,
                'franchise_name'=>Franchise::where('id',$salereturn->franchise_id)->value('name'),
                'order_no'=>$order->order_no,
                'order_date'=>$order->delivery_date,
                'item_name'=>($order_item)?$order_item->product->name:$custom_order->subcategory->name,
                'flavour'=>($flavour)?$flavour:null,
                'weight'=>$salereturn->weight,
                'amount'=>$salereturn->amount,
                'qty'=>$salereturn->qty,
                'status'=>$salereturn->status,
            ];
            $fields[] = $data;
        }
        return $fields;
    }

    private function franchisestockFields($franchisestocks){
        $fields = [];
        foreach ($franchisestocks as $franchisestock){

            $data=[
                'id'=>$franchisestock->id,
                'product_id'=>$franchisestock->product_id,
                'flavour_id'=>$franchisestock->flavour_id,
                'product_name'=>Product::where('id',$franchisestock->product_id)->value('name'),
                'flavour_name'=>($franchisestock->flavour_id)?Flavour::where('id',$franchisestock->flavour_id)->value('name'):null,
                'weight'=>$franchisestock->weight,
                'stock'=>$franchisestock->stock,
                'franchise_name'=>Franchise::where('id',$franchisestock->franchise_id)->value('name'),

            ];
            $fields[] = $data;
        }
        return $fields;
    }

    public function login(Request $request)
    {
        $rules = [
            'mobile_no' => 'required|exists:franchises,mobile_no',
            'password' => 'required',
        ];

        if ($this->ApiValidator($request->all(), $rules)) {
            $franchise = Franchise::where('mobile_no', $request->mobile_no)->first();
            if (!empty($franchise)) {
                if (Hash::check($request->password, $franchise->password)) {
                    $data['franchise'] = $this->franchiseFields($franchise);
                    $token = Str::random(80);
                    FranchiseToken::updateOrCreate(['franchise_id' => $franchise->id], ['franchise_id' => $franchise->id, 'token' => $token]);
                    if (!empty($request->device_id)) {
                        $franchise->push_token = $request->device_id;
                        $franchise->token = $request->device_token;
                        $franchise->save();
                    }
                    $data['token'] = $token;
                    $this->response['message'] = trans('api.login');
                    $this->status = 200;
                    $this->response['data'] = $data;
                } else {
                    $this->status = 401;
                    $this->response['message'] = trans('api.unauthenticated');
                }
            } else {
                $this->status = 401;
                $this->response['message'] = trans('api.unauthenticated');
            }
        }
        return $this->return_response();
    }

    public function getProfile(Request $request)
    {

        $data['franchise'] = $this->franchiseFields(Auth::guard('franchise')->user());
        $this->response['message'] = trans('api.list', ['entity' => 'Profile']);
        $this->status = 200;
        $this->response['data'] = $data;
        return $this->return_response();

    }

    public function editProfile(Request $request)
    {
        if (!empty($request->mobile_no)) {
            $franchise = Franchise::where('mobile_no', $request->mobile_no)->first();
            if (!empty($franchise)) {
                $franchise->name = $request->name;
                if (!empty($request->email)) {
                    $franchise->email = $request->email;
                }
                $franchise->city_id = $request->city_id;
                $franchise->address = $request->address;
                $franchise->mobile_no = $request->mobile_no;
                // $franchise->balance= $request->balance;
                $franchise->save();

                $data['franchise'] = $this->franchiseFields($franchise);
                $this->response['message'] = "Profile of Franchise";
                $this->status = 200;
                $this->response['data'] = $data;
            } else {
                $this->response['message'] = "Mobile No is invalid";
                $this->status = 412;
            }
        } else {
            $this->response['message'] = "Mobile No is required";
            $this->status = 412;
        }
        return $this->return_response();
    }

    public function addToCart(Request $request)
    {

        $rules = [
            'franchise_id' => 'required|exists:franchises,id',
            'product_id' => 'required|exists:products,id',
            'flavour_id' => 'nullable|exists:flavours,id',
            'amount' => 'required|numeric',
            'weight' => 'nullable|numeric',
            'message_on_cake' => 'nullable',
            'instruction' => 'nullable'
        ];

        if ($this->ApiValidator($request->all(), $rules)) {

            $product = Product::find($request->product_id);

            $category_id = SubCategoryModel::where('id', $product->subcategory_id)->value('category_id');

            $cart_type = Category::where('id', $category_id)->value('type');

            if ($cart_type === 'bakery') {
                $cart = 1;
            } else if ($cart_type === 'cake') {
                $cart = 0;
            } else {
                $cart = 2;
            }

            $discount_price1 = (FranchisePrice::where(['franchise_id' => $request->franchise_id, 'category_id' => $category_id])->value('percentage'));
            $discount_price = !empty($discount_price1) ? $discount_price1 : 0;

            $total = 0;
            $flavourRate = $product->flavours()->where('flavours.id', $request->flavour_id)->value('rate');

            $priceRate = [];
            if (!empty($request->typeRate)) {
                foreach ($request->typeRate as $type_id) {
                    $typeRate = PriceCategoryModel::where('id', $type_id)->value('price');
                    array_push($priceRate, $type_id);
                    if (!empty($typeRate)) {
                        $total += $typeRate;
                    } else {
                        $this->response['message'] = "Type Id Is invalid";
                    }
                }
            }

            if (!empty($request->photoprice_id)) {
                $photoprice_id = PriceCategoryModel::where('id', $request->photoprice_id)->value('price');
            }


            if (!empty($flavourRate)) {

                $photoprice = (!empty($photoprice_id)) ? $photoprice_id : 0;
                $discountamount = (((($flavourRate + $total) * $request->weight) + $photoprice) * $discount_price) / 100;
                $totaldis = (($flavourRate + $total) * $request->weight) + $photoprice;
                $amount = $totaldis - $discountamount;
                // dd($discount_price);


                if ($amount == $request->amount) {
                    if (!empty($request->image)) {
                        // $image= $request->cake_image[0];
                        $image = $request->image;
                        $newname = date('Ymd') . $image->getClientOriginalName();
                        $image->move(public_path('orders'), $newname);
                    }
                    $item = [
                        'franchise_id' => $request->franchise_id,
                        'product_id' => $request->product_id,
                        'flavour_id' => $request->flavour_id,
                        'amount' => $request->amount,
                        'weight' => $request->weight,
                        'size' => (!empty($request->size)) ? $request->size : NULL,
                        'image' => (!empty($newname)) ? $newname : NULL,
                        'message_on_cake' => $request->message_on_cake,
                        'instruction' => $request->instruction,
                        'cart_type' => $cart,
                        'type_rate' => !empty($priceRate) ? (json_encode($priceRate)) : NULL,
                        'is_photo' => $request->photoprice_id,
                        'note' => $request->note,
                        'customer_no' => !empty($request->customer_no) ? $request->customer_no : NULL,
                        'customer_name' => !empty($request->customer_name) ? $request->customer_name : NULL,
                        'delivery_date' => $request->delivery_date,
                    ];
                    Cart::Create($item);
                    $this->response['data'] = $this->cartFields($request->franchise_id);
                    $this->status = 200;
                    $this->response['message'] = trans('api.list', ['entity' => 'Cart']);
                } else {

                    $this->response['message'] = "Amount mismatched!";
                }
            } else {
                $discountamount = ((($product->price) * ($request->qty)) * $discount_price) / 100;
                $totaldis = ($product->price) * ($request->qty);
                $amount = $totaldis - $discountamount;
                // dd($amount);
                if ($amount == $request->amount) {

                    $item = [
                        'franchise_id' => $request->franchise_id,
                        'product_id' => $request->product_id,
                        'amount' => $request->amount,
                        'qty' => $request->qty,
                        'cart_type' => $cart,
                        'delivery_date' => $request->delivery_date,
                    ];
                    Cart::Create($item);
                    $this->response['data'] = $this->cartFields($request->franchise_id);
                    $this->status = 200;
                    $this->response['message'] = trans('api.list', ['entity' => 'Cart']);
                } else {

                    $this->response['message'] = "Amount mismatched!";
                }
            }
        }
        return $this->return_response();
    }

    public function cartList(Request $request)
    {
        $rules = [
            'franchise_id' => 'required|exists:franchises,id',
        ];

        if ($this->ApiValidator($request->all(), $rules)) {
            $this->status = 200;
            $this->response['data'] = $this->cartFields($request->franchise_id);
            $this->response['message'] = trans('api.list', ['entity' => 'Cart']);
        }
        return $this->return_response();
    }

    public function cartUpdate(Request $request)
    {
        $rules = [
            'item_id' => 'required|exists:carts,id',
            'message_on_cake' => 'required',
            'instruction' => 'required'
        ];

        if ($this->ApiValidator($request->all(), $rules)) {
            $item = Cart::find($request->item_id);
            $item->message_on_cake = $request->message_on_cake;
            $item->instruction = $request->instruction;
            $item->note = $request->note;
            $item->delivery_date = $request->delivery_date;
            $item->save();
            $this->status = 200;
            $this->response['data'] = $this->cartFields($item->device_id);
            $this->response['message'] = trans('api.list', ['entity' => 'Cart']);
        }
        return $this->return_response();
    }

    public function cartRemove(Request $request)
    {
        $rules = [
            'item_id' => 'required|exists:carts,id',
        ];

        if ($this->ApiValidator($request->all(), $rules)) {
            $item = Cart::find($request->item_id);
            $franchise_id = $item->franchise_id;
            $item->delete();

            $this->status = 200;
            $this->response['data'] = $this->cartFields($franchise_id);
            $this->response['message'] = trans('api.list', ['entity' => 'Cart']);

        }
        return $this->return_response();
    }

    public function cartEdit(Request $request)
    {
        $rules = [
            'item_id' => 'required|exists:carts,id',
            'qty' => 'required',
            'amount' => 'required'
            // 'user_id'=>'required|exists:users,id'
        ];

        if ($this->ApiValidator($request->all(), $rules)) {

            $item = Cart::where('id', $request->item_id)->first();
            $subcategory_id = Product::where('id', $item->product_id)->value('subcategory_id');
            $product_price = Product::where('id', $item->product_id)->value('price');

            $category_id = SubCategoryModel::where('id', $subcategory_id)->value('category_id');
            // dd($category_id);
            $discount_price1 = (FranchisePrice::where(['franchise_id' => $item->franchise_id, 'category_id' => $category_id])->value('percentage'));
            $discount_price = !empty($discount_price1) ? $discount_price1 : 0;

            $discountamount = (($request->qty * $product_price) * $discount_price) / 100;
            $totaldis = ($request->qty * $product_price);
            $amount = $totaldis - $discountamount;
            // dd($amount);
            if ($amount == $request->amount) {
                $item->qty = $request->qty;
                $item->amount = $request->amount;
                $item->note = $request->note;
                $item->delivery_date = $request->delivery_date;
                $item->save();
                $this->status = 200;
                $this->response['data'] = $this->cartFields($item->franchise_id);
                $this->response['message'] = trans('api.list', ['entity' => 'Cart']);
            } else {
                $this->response['message'] = "Amount mismatch";
                $this->status = 412;
            }
        }
        return $this->return_response();
    }

    public function franchiseDiscount(Request $request)
    {
        $rules = [
            'franchise_id' => 'required|exists:franchises,id',
            'category_id' => 'required|exists:categories,id'
        ];

        if ($this->ApiValidator($request->all(), $rules)) {
            $franchise_id = FranchisePrice::where(['franchise_id' => $request->franchise_id, 'category_id' => $request->category_id])->first();
            if (!empty($franchise_id)) {
                $this->status = 200;
                // dd($franchise_id->id);
                $this->response['message'] = "Discount for Products ";
                $this->response['data'] = $this->franchiseDiscountFields($franchise_id->id);
            } else {
                $this->status = 412;
                $this->response['message'] = "There is no Discount for this Products ";
            }
        }
        return $this->return_response();
    }

    public function wishLists(Request $request)
    {
        $rules = [
            'product_id' => 'required|exists:products,id',
            'franchise_id' => 'required|exists:franchises,id'
        ];
        if ($this->ApiValidator($request->all(), $rules)) {
            $wishlist = Wishlist::where(['product_id' => $request->product_id, 'franchise_id' => $request->franchise_id])->first();
            // dd($wishlist);
            if (!empty($wishlist)) {
                // dd('he');
                $this->response['message'] = 'Product is already in wishlist';
                $this->response['data'] = $this->wishListFields($wishlist->franchise_id);
                $this->status = 200;
            } else {
                // dd('he1');

                $item = [
                    'product_id' => $request->product_id,
                    'franchise_id' => $request->franchise_id,
                ];
                Wishlist::Create($item);
                $this->response['data'] = $this->wishListFields($request->franchise_id);
                $this->status = 200;
                $this->response['message'] = trans('api.list', ['entity' => 'Wishlist']);
            }
        }
        return $this->return_response();

    }

    public function wishlistDetail(Request $request)
    {
        $rules = [

            'franchise_id' => 'required|exists:franchises,id'
        ];
        if ($this->ApiValidator($request->all(), $rules)) {
            $this->response['message'] = 'Wishlist are listing';
            $this->response['data'] = $this->wishListFields($request->franchise_id);
            $this->status = 200;
        }
        return $this->return_response();

    }

    public function wishListRemove(Request $request)
    {
        $rules = [
            'item_id' => 'nullable',
            'franchise_id' => 'nullable',
        ];

        if ($this->ApiValidator($request->all(), $rules)) {
            if (!empty($request->item_id)) {
                $item = Wishlist::find($request->item_id);
                $franchise_id = $item->franchise_id;
                $item->delete();
            }
            if (!empty($request->franchise_id)) {
                $items = Wishlist::where(['franchise_id' => $request->franchise_id, 'product_id' => $request->product_id])->delete();
                $franchise_id = $request->franchise_id;

            }

            $this->status = 200;
            $this->response['data'] = $this->wishListFields($franchise_id);
            $this->response['message'] = trans('api.list', ['entity' => 'Wishlist']);

        }
        return $this->return_response();
    }

    public function placeOrder(Request $request)
    {
        $rules = [
            'payment_method' => 'required|in:balance,online',
            'shipping_method' => 'required|in:homedelivery',
            'address' => 'required_if:shipping_method,homedelivery',
            'city_id' => 'required_if:shipping_method,homedelivery|exists:cities,id',
            'zip' => 'required_if:shipping_method,homedelivery',
            'delivery_date' => 'required|date_format:d-m-Y',
            'delivery_time_id' => 'required|exists:times,id',
            'franchises_id' => 'required|exists:franchises,id',
            'razorpay_payment_id' => 'required_if:payment_method,online'
        ];


        if ($this->ApiValidator($request->all(), $rules)) {

            $ite = Cart::where('franchise_id', $request->franchises_id)->groupBy('cart_type')->pluck('cart_type')->toArray();
            foreach ($ite as $i => $value) {
                $items = Cart::where(['franchise_id' => $request->franchises_id, 'cart_type' => $value])->get();

                // $items = Cart::where('franchise_id', $request->franchises_id)->get();
                $balance = Franchise::where('id', $request->franchises_id)->value('balance');


                if (!empty($items)) {
                    $order_items = [];
                    $order_total = 0;
                    foreach ($items as $item) {

                        $order_items[] = new OrderItem([
                            'product_id' => $item->product_id,
                            'flavour_id' => !empty($item->flavour_id) ? $item->flavour_id : NULL,
                            'amount' => $item->amount,
                            'weight' => !empty($item->weight) ? $item->weight : NULL,
                            'size' => (!empty($item->size)) ? $item->size : NULL,
                            'image' => (!empty($item->image)) ? $item->image : NULL,
                            'message_on_cake' => !empty($item->message_on_cake) ? $item->message_on_cake : NULL,
                            'qty' => !empty($item->qty) ? $item->qty : NULL,
                            'instruction' => !empty($item->instruction) ? $item->instruction : NULL,
                            'voice_msg' => !empty($item->voice_msg) ? url('public/voice' . $item->voice_msg) : NULL,
                            'customer_no' => !empty($item->customer_no) ? $item->customer_no : NULL,
                            'customer_name' => !empty($item->customer_name) ? $item->customer_name : NULL,
                            'note' => $item->note,
                            'type_rate' => $item->type_rate,
                            'is_photo' => $item->is_photo,
                            'delivery_date' => $item->delivery_date,
                        ]);
                        $order_total += $item->amount;

                    }

                }


                if (!empty($request->razorpay_payment_id)) {
                    $payment_status = $this->verifyRazorOrder($request->razorpay_payment_id, $order_total);
                    if ($payment_status['status'] == false) {
                        $this->status = 200;
                        $this->response['message'] = $payment_status['error_message'];
                        return $this->return_response();
                    }
                }

                $last_id = Order::latest()->value('id');
                $last_id = (!empty($last_id) ? $last_id + 1 : 1);
                $order = new Order;
                $order->franchises_id = $request->franchises_id;
                $order->order_no = date('Ymd') . "/" . time() . "/" . $last_id;
                $order->shipping_method = $request->shipping_method;

                $order->city_id = $request->city_id;
                $order->address = $request->address;
                $order->zip = $request->zip;
                $order->delivery_date = \Carbon\Carbon::parse($request->delivery_date)->format('Y-m-d');
                $order->time_id = $request->delivery_time_id;
                $order->note = $request->note;
                $order->status = 'place_order';
                $order->p_type = $value;
                $order->type = 'Normal';
                $order->total_amount = $order_total;
                $order->payment_method = $request->payment_method;
                $order->razorpay_payment_id = $request->razorpay_payment_id;
                $order->payment_data = !empty($payment_status) ? json_encode($payment_status['payment_response']) : NULL;
                $order->save();
                $order->items()->saveMany($order_items);
                if ($request->payment_method === 'balance') {
                    $balancetotal = $balance - $order_total;
                    $debititem = [
                        'franchise_id' => $request->franchises_id,
                        'debit' => $order_total,
                        'totalbalance' => $balancetotal,
                    ];
                    Franchise::where('id', $request->franchises_id)->update(['balance' => $balancetotal]);
                    FranchiseBalance::create($debititem);
                }
                Cart::where(['franchise_id' => $request->franchises_id, 'cart_type' => $value])->delete();
            }

            if (!empty(Franchise::where('id', $request->franchises_id)->value('push_token'))) {
                sendPushMessage(Franchise::where('id', $request->franchises_id)->value('push_token'), "Order placed successfully");
            }
            $admin_token = Admin::where('type', 'Admin')->value('push_token');
            if (!empty($admin_token)) {
                sendPushMessage($admin_token, "New order received");
            }

            $this->status = 200;
            $this->response['message'] = trans('api.orderlist', ['entity' => 'Order']);
        } else {
            $this->status = 200;
            $this->response['message'] = trans('api.orderlist', ['entity' => 'Order']);
        }

        return $this->return_response();
    }

    public function customPlaceOrder(Request $request)
    {
        $rules = [
            'shipping_method' => 'required|in:pickup,homedelivery',
            // 'franchises_id'=>'required_if:shipping_method,pickup|exists:franchises,id',
            // 'address'=>   'required_if:shipping_method,homedelivery',
            // 'city_id'=>   'required_if:shipping_method,homedelivery|exists:cities,id',
            // 'zip'=>   'required_if:shipping_method,homedelivery',
            'delivery_date' => 'required|date_format:d-m-Y',
            'delivery_time_id' => 'required|exists:times,id',
            // 'device_id'=>'required',
            'subcategory_id' => 'required|exists:sub_category_models,id',
            'flavour_id' => 'required|exists:flavours,id',
            'weight' => 'required',
            'idea' => 'array',
            'idea.*' => 'nullable|image',
            'cake' => 'array',
            'cake.*' => 'nullable|image',
        ];

        if ($this->ApiValidator($request->all(), $rules)) {
            if (!empty($request->idea)) {

                foreach ($request->idea as $image) {
                    $newname = $image->getClientOriginalName();
                    $order_images[] = new OrderImage(['image' => 'orders/' . $newname, 'type' => 'idea']);
                    $image->move(public_path('orders'), $newname);
                }
            }
            if (!empty($request->cake)) {
                foreach ($request->cake as $image) {
                    $newname = $image->getClientOriginalName();
                    $order_images[] = new OrderImage(['image' => 'orders/' . $newname, 'type' => 'cake']);
                    $image->move(public_path('orders'), $newname);
                }
            }
            $total = 0;

            $category_id = SubCategoryModel::where('id', $request->subcategory_id)->value('category_id');
            // dd($category_id);
            $discount_price1 = (FranchisePrice::where(['franchise_id' => $request->franchises_id, 'category_id' => $category_id])->value('percentage'));
            $discount_price = !empty($discount_price1) ? $discount_price1 : 0;
            $priceRate = [];
            if (!empty($request->typeRate)) {
                foreach ($request->typeRate as $type_id) {
                    $typeRate = PriceCategoryModel::where('id', $type_id)->value('price');
                    array_push($priceRate, $type_id);
                    if (!empty($typeRate)) {
                        $total += $typeRate;
                    } else {
                        $this->response['message'] = "Type Id Is invalid";
                    }
                }
            }

            if (!empty($request->photoprice_id)) {
                $photoprice_id = PriceCategoryModel::where('id', $request->photoprice_id)->value('price');
            }

            $flavourRate = Flavour::where('id', $request->flavour_id)->value('rate');
            $photoprice = (!empty($photoprice_id)) ? $photoprice_id : 0;
            $amount = (($flavourRate + $total) * $request->weight) + $photoprice;

            $discountamount = (((($flavourRate + $total) * $request->weight) + $photoprice) * $discount_price) / 100;
            $totaldis = (($flavourRate + $total) * $request->weight) + $photoprice;
            $amount = $totaldis - $discountamount;

            if ($amount == $request->amount) {
                if (!empty($request->image)) {
                    // $image= $request->cake_image[0];
                    $image = $request->image;
                    $newname = date('Ymd') . $image->getClientOriginalName();
                    $image->move(public_path('orders'), $newname);
                }

                $order_items = new CustomOrder([
                    'sub_category_id' => $request->subcategory_id,
                    'flavour_id' => $request->flavour_id,
                    'weight' => $request->weight,
                    'theme' => $request->theme,
                    'message_on_cake' => $request->message_on_cake,
                    'instruction' => $request->instruction,
                    'type_rate' => !empty($priceRate) ? (json_encode($priceRate)) : NULL,
                    'is_photo' => $request->photoprice_id,
                    'amount' => $amount,
                    'customer_name' => $request->customer_name,
                    'customer_no' => $request->customer_no,

                ]);

                $last_id = Order::latest()->value('id');
                $last_id = (!empty($last_id) ? $last_id + 1 : 1);
                $order = new Order;
                $order->franchises_id = $request->franchises_id;
                $order->order_no = date('Ymd') . "/" . time() . "/" . $last_id;
                $order->shipping_method = $request->shipping_method;
                $order->type = 'Custom';
                $order->city_id = $request->city_id;
                $order->address = $request->address;
                $order->zip = $request->zip;
                $order->delivery_date = \Carbon\Carbon::parse($request->delivery_date)->format('Y-m-d');
                $order->time_id = $request->delivery_time_id;
                $order->status = 'place_order';
                $order->note = $request->note;
                $order->real_amount = $totaldis;

                $order->total_amount = $amount;
                $order->save();
                $order->customitem()->save($order_items);
                if (!empty($order_images)) {
                    $order->images()->saveMany($order_images);
                }

                if (!empty(Franchise::where('id', $request->franchises_id)->value('push_token'))) {
                    sendPushMessage(Franchise::where('id', $request->franchise_id)->value('push_token'), "Order placed successfully");
                }
                $admin_token = Admin::where('type', 'Admin')->value('push_token');
                if (!empty($admin_token)) {
                    sendPushMessage($admin_token, "New order received");
                }

                $this->status = 200;
                $this->response['message'] = trans('api.orderlist', ['entity' => 'Order']);
            } else {
                $this->status = 200;
                $this->response['message'] = "Amount mismatched!";
            }
        }
        return $this->return_response();
    }

    public function customPayment(Request $request)
    {
        $rules = [
            'payment_method' => 'required|in:cod,online,balance',
            'razorpay_payment_id' => 'required_if:payment_method,online',
            'order_id' => 'required|exists:orders,id',

        ];

        if ($this->ApiValidator($request->all(), $rules)) {
            if (!empty($request->razorpay_payment_id)) {
                $payment_status = $this->verifyRazorOrder($request->razorpay_payment_id, $request->order_total);
                if ($payment_status['status'] == false) {
                    $this->response['message'] = $payment_status['error_message'];
                    return $this->return_response();
                }
            }

            if ($request->payment_method === 'balance') {
                $franchise_id = Order::where('id', $request->order_id)->value('franchises_id');
                $balance = Franchise::where('id', $franchise_id)->value('balance');
                $balancetotal = $balance - $request->order_total;
                $debititem = [
                    'franchise_id' => $request->franchises_id,
                    'debit' => $request->order_total,
                    'totalbalance' => $balancetotal,
                ];
                Franchise::where('id', $request->franchises_id)->update(['balance' => $balancetotal]);
                FranchiseBalance::create($debititem);
            }

            if ($request->payment_method === 'cod') {
                $status = 'cod_selected';
            } else if ($request->payment_method === 'balance') {
                $status = 'balance_selected';
            } else {
                $status = 'online_selected';
            }

            $custom_order = Order::where('id', $request->order_id)->update([
                'payment_method' => $request->payment_method,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'payment_data' => !empty($payment_status) ? json_encode($payment_status['payment_response']) : NULL,
                'status' => 'confirmed',
                'admin_status' => $status,
            ]);

            if (!empty(Franchise::where('id', $request->franchises_id)->value('push_token'))) {
                sendPushMessage(Franchise::where('id', $request->franchises_id)->value('push_token'), "Payment Done Successfully");
            }
            $admin_token = Admin::where('type', 'Admin')->value('push_token');
            if (!empty($admin_token)) {
                sendPushMessage($admin_token, "Payment Done");
            }

            $this->status = 200;
            $this->response['message'] = trans('api.orderlist', ['entity' => 'Order']);
        }

        return $this->return_response();

    }

    public function addVallet(Request $request)
    {
        $rules = [
            'franchise_id' => 'required|exists:franchises,id',
            'razorpay_payment_id' => 'required',
            'amount' => 'required',

        ];
        if ($this->ApiValidator($request->all(), $rules)) {

            if (!empty($request->razorpay_payment_id)) {
                $payment_status = $this->verifyRazorOrder($request->razorpay_payment_id, $request->amount);
                if ($payment_status['status'] == false) {
                    $this->response['message'] = $payment_status['error_message'];
                    return $this->return_response();
                }
            }
            $balance = Franchise::where('id', $request->franchise_id)->value('balance');
            $balancetotal = $balance + $request->amount;

            $credit = [
                'franchise_id' => $request->franchise_id,
                'credit' => $request->amount,
                'totalbalance' => $balancetotal,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'payment_data' => !empty($payment_status) ? json_encode($payment_status['payment_response']) : NULL,
            ];

            $b = Franchise::where('id', $request->franchise_id)->update(['balance' => $balancetotal]);
            // dd($b);
            FranchiseBalance::create($credit);

            $this->status = 200;
            $this->response['message'] = "Balance Credited";
        }
        return $this->return_response();

    }

    public function orderList(Request $request)
    {
        $orders = Order::where('franchises_id', $request->franchises_id)->orderBy('id', 'DESC')->get();

        $this->status = 200;
        $this->response['data'] = $this->orderListFields($orders);
        $this->response['message'] = trans('api.orderlist', ['entity' => 'Order Listing']);

        return $this->return_response();
    }

    public function assignorderList(Request $request)
    {
        $assignList = AssignOrder::where('franchise_id', $request->franchise_id)->orderBy('order_id', 'DESC')->get();

        $this->status = 200;
        $this->response['data'] = $this->assignsListFields($assignList);
        $this->response['message'] = trans('api.orderlist', ['entity' => 'Order Listing']);

        return $this->return_response();
    }

    public function orderDetail(Request $request)
    {
        $rules = [
            'order_id' => 'required',
        ];

        if ($this->ApiValidator($request->all(), $rules)) {
            $id = $request->order_id;
            $orders = Order::find($request->order_id)->orderBy('id', 'DESC');
            $this->status = 200;
            $this->response['data'] = $this->orderDetailFields($orders);
            $this->response['message'] = trans('api.orderlist', ['entity' => 'Order Listing']);
        }
        return $this->return_response();
    }

    public function editCustomorder(Request $request)
    {
// dd($request->status);
        $rules = [
            'shipping_method' => 'required|in:pickup,homedelivery',
            'franchises_id' => 'required|exists:franchises,id',
            'address' => 'required',
            'city_id' => 'required|exists:cities,id',
            'zip' => 'required',
            'delivery_date' => 'required|date_format:d-m-Y',
            'delivery_time_id' => 'required|exists:times,id',
            'subcategory_id' => 'required|exists:sub_category_models,id',
            'flavour_id' => 'required|exists:flavours,id',
            'weight' => 'required',
// 'theme'=>'required',
            'status' => 'required',
// 'message_on_cake' => 'required',
// 'instruction' => 'required',
            'idea' => 'nullable|array',
            'idea.*' => 'image',
            'cake' => 'nullable|array',
            'cake.*' => 'image',
            'edit' => 'required'
        ];

        if ($this->ApiValidator($request->all(), $rules)) {
            if ($request->status == 'rejected' && $request->edit == '1') {
                $order_images = '';
                if (!empty($request->idea)) {
                    foreach ($request->idea as $image) {
                        $newname = $image->getClientOriginalName();
                        $order_images = OrderImage::updateOrCreate(['order_id' => $request->order_id, 'type' => 'idea'], ['order_id' => $request->order_id, 'type' => 'idea', 'image' => 'orders/' . $newname]);
                        $image->move(public_path('orders'), $newname);
                    }
                }

                if (!empty($request->cake)) {
                    foreach ($request->cake as $image) {
                        $newname = $image->getClientOriginalName();
                        $order_images = OrderImage::updateOrCreate(['order_id' => $request->order_id, 'type' => 'cake'], ['order_id' => $request->order_id, 'type' => 'cake', 'image' => 'orders/' . $newname]);
                        $image->move(public_path('orders'), $newname);
                    }
                }

                $orders = Order::find($request->order_id);

                $orders->shipping_method = $request->shipping_method;
                $orders->franchise_id = $request->franchise_id;
                $orders->city_id = $request->city_id;
                $orders->address = $request->address;
                $orders->zip = $request->zip;
                $orders->delivery_date = \Carbon\Carbon::parse($request->delivery_date)->format('Y-m-d');
                $orders->time_id = $request->delivery_time_id;
                $orders->status = 'place_order';
                $orders->admin_status = 'place_order';

                $orders->save();

                $order_items = CustomOrder::where('order_id', $request->order_id)->
                update([
                    'sub_category_id' => $request->subcategory_id,
                    'flavour_id' => $request->flavour_id,
                    'weight' => $request->weight,
                    'theme' => $request->theme,
                    'edit' => '0',
                    'message_on_cake' => $request->message_on_cake,
                    'instruction' => $request->instruction,
                    'delivery_date' => $request->delivery_date,
                ]);

                // $orders->images()->saveMany($order_images);

                // if (!empty(Auth::user()->push_token)) {
                // 	sendPushMessage(Auth::user()->push_token, "Order Edit successfully");
                // }
                // $admin_token = Admin::where('type', 'Admin')->value('push_token');
                // if (!empty($admin_token)) {
                // 	sendPushMessage($admin_token, "Rejected Order Edited");
                // }

                if (!empty($orders)) {
                    $this->status = 200;
                    $this->response['message'] = "Your Order is Updated";
                } else {
                    $this->status = 200;
                    $this->response['message'] = "Something is wrong";
                }
            } else {
                $this->status = 200;
                $this->response['message'] = "You can't update it";
            }
        }
        return $this->return_response();
    }

    public function verifyRazorOrder($payment_id, $amount)
    {
        $api = new Api(env('RAZORPAY_API_KEY', 'rzp_test_0rYw1Uenw7GMHp'), env('RAZORPAY_API_SECRET', 'uNjdE0KAWAGuepoHoOioaWJ7'));

        try {
            $captured_payment = $api->payment->fetch($payment_id)->capture(array('amount' => ($amount * 100)));
// $payment    =   $api->payment->fetch($payment_id);
            $data = [];
            $data['payment_response'] = $captured_payment->toArray();
            if ($captured_payment->status == 'captured') {
                if ($captured_payment->amount == ($amount * 100)) {
                    $data['status'] = true;
                    $data['error_message'] = $captured_payment->error_description;
                } else {
                    $data['status'] = false;
                    $data['error_message'] = 'Payment amount mismatched!';
                }
            } else {
                $data['status'] = false;
                $data['error_message'] = $captured_payment->description;
            }
            return $data;
        } catch (\Exception $e) {
            $data['status'] = false;
            $data['error_message'] = $e->getMessage();
            $data['payment_response'] = NULL;
            return $data;
        }
    }

    public function additionalAddress(Request $request)
    {
        $rules = [
            'franchise_id' => 'required|exists:franchises,id',
            'address' => 'required',
        ];

        if ($this->ApiValidator($request->all(), $rules)) {
            $franchise = Franchise::where('id', $request->franchise_id)->first();
            $franchise->additionaladdress = $request->address;
            $franchise->save();
            $this->status = 200;
            $data['franchise'] = $this->franchiseFields($franchise);
            $this->response['message'] = "Address Add Successfully";
        }
        return $this->return_response();
    }

    public function salesReturn(Request $request){
        $rules = [
            'order_id' => 'required|exists:orders,id',
        ];
        if ($this->ApiValidator($request->all(), $rules)) {
            $sales_return =new SaleReturn;
            $sales_return->order_id=$request->order_id;
            $sales_return->item_id=$request->item_id;
            $sales_return->franchise_id=$request->franchise_id;
            $sales_return->status='salereturn';
            $sales_return->reason_f=$request->reason;
            $sales_return->date=$request->date;
            $sales_return->weight=$request->weight;
            $sales_return->qty=$request->qty;
            $sales_return->amount=$request->amount;
            $sales_return->save();
            $order=Order::where('id',$request->order_id)->first();
            if($order->type == "Normal"){
                OrderItem::where('id',$request->item_id)->update(['is_sales_return'=>1]);
            }else{
                CustomOrder::where('id',$request->item_id)->update(['is_sales_return'=>1]);
            }
            $this->status = 200;

            $this->response['message'] = "Sales Return Successfully";
        }
        return $this->return_response();
    }

    public function salesReturnHistory(Request $request){
        $rules = [
            'franchise_id' => 'required|exists:franchises,id',
        ];
        if ($this->ApiValidator($request->all(), $rules)) {
         $sales_returns= SaleReturn::where('franchise_id',$request->franchise_id)->orderBy('id','Desc')->get();
            $this->status = 200;
            $this->response['sales_returns'] = $this->salereturnFields($sales_returns);
            $this->response['message'] = "Sales Return listing Successfully";
        }
        return $this->return_response();

    }

    public function addStock(Request $request){
        $rules = [
            'product_id' => 'required|exists:products,id',
        ];
        if ($this->ApiValidator($request->all(), $rules)){
            $franchise_stock=FranchiseStock::where(['product_id'=>$request->product_id,'franchise_id'=>$request->franchise_id])->first();
            if(!empty($franchise_stock)){
                if($request->flavour_id){
                    $franchise_stocks =$franchise_stock->where('flavour_id',$request->flavour_id)->get();
                    if($franchise_stocks){
                        $franchise_stock1 =$franchise_stocks->where('weight',$request->weight)->first();
                       if($franchise_stock1){
                           $stock=$franchise_stock1->stock;
                           $franchise_stock1->stock=$stock+1;
                           $franchise_stock1->save();
                       }else{
                           FranchiseStock::insert(['product_id'=>$request->product_id,'franchise_id'=>$request->franchise_id,'flavour_id'=>$request->flavour_id,'weight'=>$request->weight,'stock'=>1]);
                       }


                    }else{
                        FranchiseStock::insert(['product_id'=>$request->product_id,'franchise_id'=>$request->franchise_id,'flavour_id'=>$request->flavour_id,'weight'=>$request->weight,'stock'=>1]);
                    }
                }else{
                    $franchise_stock=FranchiseStock::where(['product_id'=>$request->product_id,'franchise_id'=>$request->franchise_id])->first();
                    $stock=$franchise_stock->stock;
                    $franchise_stock->stock=$stock+$request->qty;
                    $franchise_stock->save();
                }
            }else{
                FranchiseStock::insert([
                    'product_id'=>$request->product_id,
                    'franchise_id'=>$request->franchise_id,
                    'flavour_id'=>$request->flavour_id,
                    'weight'=>$request->weight,
                    'qty'=>$request->qty,
                    'stock'=>1]);



            }
            $this->status = 200;

            $this->response['message'] = "Stock Added Successfully";
        }

        return $this->return_response();

    }

    public function viewStock(Request $request){
        $rules = [
            'franchise_id' => 'required|exists:franchises,id',
        ];
        if ($this->ApiValidator($request->all(), $rules)) {
            $franchise_stocks= FranchiseStock::where('franchise_id',$request->franchise_id)->orderBy('id','Desc')->get();
            $this->status = 200;
            $this->response['sales_returns'] = $this->franchisestockFields($franchise_stocks);
            $this->response['message'] = "Sales Return listing Successfully";
        }
        return $this->return_response();
    }
}
