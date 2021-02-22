<?php

namespace App\Http\Controllers\Api\V1;

use App\Admin;
use App\Http\Controllers\Controller;
use App\Models\AssignOrder;
use App\Franchise;
use App\Models\Order;
use App\Models\PriceCategoryModel;
use App\Models\SubCategoryModel;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\random;
use PDF;

class AdminNewController extends Controller
{

    private function assignListFields($assignListitem)
    {

        $fields = [];
        foreach ($assignListitem as $assignOrder) {
            $order = $assignOrder->order;
            if (!empty($order->franchise_id)) {
                $franchiseaddress = Franchise::where('id', $order->franchise_id)->value('name');
            }
            if ($order->shipping_method === 'pickup') {
                $address = $franchiseaddress;
            } else {
                $address = $order->address;
            }

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
                'note' => $order->note,
                'delivery_date' => \Carbon\Carbon::parse($order->delivery_date)->format('d-m-Y'),
                'delivery_time' => \Carbon\Carbon::parse($order->time->startingtime)->format('h:i A') . ' to ' . \Carbon\Carbon::parse($order->time->endingtime)->format('h:i A'),
                'adminstatus' => $order->admin_status,
                'type' => $order->type,
                'address' => $address,
                'dept_type' => $order->p_type,
            ];

            $data['items'] = [];

            if ($order->type == 'Normal') {
                foreach ($order->items as $item) {
                    $data['items'][] = [
                        'product_name' => $item->product->name,
                        'product_id' => $item->product_id,
                        'flavour' => !empty($item->flavour_id) ? $item->flavour->name : NULL,
                        'image' => !empty($item->product->image) ? Storage::url('app/public/' . $item->product->image) : NULL,
                        'amount' => $item->amount,
                        'weight' => !empty($item->weight) ? $item->weight : NULL,
                        'qty' => !empty($item->qty) ? $item->qty : NULL,
                        'voice_msg' => !empty($item->voice_msg) ? url('public/voice/' . $item->voice_msg) : NULL,
                        'cake_image' => (!empty($item->image)) ? url('public/orders/' . $item->image) : NULL,
                        'completed_image' => (!empty($item->completed_image)) ? Storage::url('app/public/' . $item->completed_image) : NULL,
                        'pdf' => (!empty($item->pdf)) ? url('public/' . $item->pdf) : NULL,
                        'message_on_cake' => $item->message_on_cake,
                        'instruction' => $item->instruction,
                        'note' => $item->note,
                        'size' => $item->size,
                        'is_photo' => $item->is_photo,
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


                $data['items'][] = [
                    'subcategory_id' => $item->subcategory->id,
                    'subcategory' => $item->subcategory->name,
                    'flavour_id' => $item->flavour->id,
                    'flavour' => $item->flavour->name,
                    'flavour_rate' => $item->flavour->rate,
                    'amount' => $item->amount,
                    'weight' => $item->weight,
                    'theme' => $item->theme,
                    'pdf' => (!empty($item->pdf)) ? url('public/' . $item->pdf) : NULL,
                    'completed_image' => (!empty($item->completed_image)) ? Storage::url('app/public/' . $item->completed_image) : NULL,
                    'message_on_cake' => $item->message_on_cake,
                    'instruction' => $item->instruction,
                    'start_time' => $item->start_time,
                    'size' => $item->size,
                    'is_photo' => $item->is_photo,
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

            $data['customer'] = $order->user()->first(['first_name', 'last_name', 'mobile_no', 'email']);

            $data['chef'] = [
                'id' => $assignOrder->admin->id,
                'name' => $assignOrder->admin->name,
                'type' => $assignOrder->admin->type
            ];


            $deliveryboy = Admin::where('id', $assignOrder->delivery_boy_id)->first();
            $data['delivery_boy'] = !empty($assignOrder->delivery_boy_id) ? [
                'id' => $deliveryboy->id,
                'name' => $deliveryboy->name,
                'type' => $deliveryboy->type
            ] : Null;


            $data['instruction_delivery_boy'] = $assignOrder->instruction_delivery_boy;
            $data['instruction'] = $assignOrder->instruction;
            $fields[] = $data;
        }
        return $fields;
    }

    private function orderListFields($orders, $uid)
    {

        $fields = [];

        foreach ($orders as $order) {
            if ($order->p_type == $uid || $uid == NULL || $uid == '' || $uid == null) {
                if (!empty($order->franchise_id)) {
                    $franchiseaddress = Franchise::where('id', $order->franchise_id)->value('name');
                }
                if ($order->shipping_method === 'pickup') {
                    $address = $franchiseaddress;
                } else {
                    $address = $order->address;
                }

                $data = [
                    'order_id' => $order->id,
                    'order_no' => $order->order_no,
                    'status' => $order->status,
                    'payment_method' => $order->payment_method,
                    'shipping_method' => $order->shipping_method,
                    'city_id' => $order->city_id,
                    'adminstatus' => $order->admin_status,
                    'delivery_date' => \Carbon\Carbon::parse($order->delivery_date)->format('d-m-Y'),
                    'delivery_time' => \Carbon\Carbon::parse($order->time->startingtime)->format('h:i A') . ' to ' . \Carbon\Carbon::parse($order->time->endingtime)->format('h:i A'),
                    'type' => $order->type,
                    'total_amount' => $order->total_amount,
                    'address' => $order->address,
                    'accept_time' => !empty($order->accept_time) ? \Carbon\Carbon::parse($order->accept_time)->format('d-m-Y h:i A') : Null,
                    'start_preparing_time' => !empty($order->start_preparing_time) ? \Carbon\Carbon::parse($order->start_preparing_time)->format('d-m-Y h:i A') : Null,
                    'stop_preparing_time' => !empty($order->stop_preparing_time) ? \Carbon\Carbon::parse($order->stop_preparing_time)->format('d-m-Y h:i A') : Null,
                    'way_to_delievered_time' => !empty($order->way_to_delievered_time) ? \Carbon\Carbon::parse($order->way_to_delievered_time)->format('d-m-Y h:i A') : Null,
                    'delieverd_time' => !empty($order->delieverd_time) ? \Carbon\Carbon::parse($order->delieverd_time)->format('d-m-Y h:i A') : Null,
                    'delieverd_time_id' => !empty($order->time_id) ? $order->time_id : Null,
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
                                $priceRate = PriceCategoryModel::where('id', $value)->first();
                                // array_push($priceRateName, $priceRate);
                                $ar[] = [
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

                        $data['items'][] = [
                            'item_id' => $item->id,
                            'product_name' => $item->product->name,
                            'product_id' => $item->product->id,
                            'product_price' => $item->product->price,
                            // 'discount_percentage'=>$discount_prices,
                            'flavour_id' => !empty($item->flavour_id) ? $item->flavour_id : NULL,
                            'flavour' => !empty($item->flavour_id) ? $item->flavour->name : NULL,
                            'flavour_price' => !empty($item->flavour_id) ? $item->flavour->rate : NULL,
                            'image' => !empty($item->product->image) ? Storage::url('app/public/' . $item->product->image) : null,
                            'amount' => $item->amount,
                            'weight' => !empty($item->weight) ? $item->weight : NULL,
                            'qty' => !empty($item->qty) ? $item->qty : NULL,
                            'voice_msg' => !empty($item->voice_msg) ? url('public/voice/' . $item->voice_msg) : NULL,
                            'customer_name' => $item->customer_name,
                            'customer_no' => $item->customer_no,
                            'cake_image' => (!empty($item->image)) ? url('public/orders/' . $item->image) : NULL,
                            'completed_image' => (!empty($item->completed_image)) ? Storage::url('app/public/' . $item->completed_image) : NULL,
                            'message_on_cake' => $item->message_on_cake,
                            'instruction' => $item->instruction,
                            'typeRate' => $priceRateName,
                            'is_photo' => $photo_rate,
                            'size' => $item->size,
                            'start_time' => $item->start_time,
                            'end_time' => $item->end_time,
                            'pdf' => (!empty($item->pdf)) ? url('public/' . $item->pdf) : NULL,

                        ];
                    }
                }
                else{

                    $item = $order->customitem;
                    $subcategory = SubCategoryModel::where('id', $item->sub_category_id)->first();
                    $priceRateName = [];
                    if (!empty($item->type_rate)) {
                        $type_ids = json_decode($item->type_rate);
                        $ar = [];
                        foreach ($type_ids as $key => $value) {
                            $priceRate = PriceCategoryModel::where('id', $value)->first();                    // array_push($priceRateName, $priceRate);
                            $ar[] = [
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

                    $data['items'][] = [
                        // 'subcategory'=>$subcategory_name,
                        'flavour' => $item->flavour->name,
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


                $assignOrder = AssignOrder::where('order_id', $order->id)->first();
                if (!empty($assignOrder)) {
                    $data['chef'] = [
                        'id' => $assignOrder->admin->id,
                        'name' => $assignOrder->admin->name,
                        'type' => $assignOrder->admin->type
                    ];

                    $deliveryboy = Admin::where('id', $assignOrder->delivery_boy_id)->first();
                    $data['delivery_boy'] = !empty($assignOrder->delivery_boy_id) ? [
                        'id' => $deliveryboy->id,
                        'name' => $deliveryboy->name,
                        'type' => $deliveryboy->type
                    ] : Null;


                }
                $fields[] = $data;
            }
        }
            return $fields;
    }

    private function orderListsFields($orders, $uid)
    {

        $fields = [];

        foreach ($orders as $order) {
            if ($order->p_type == $uid || $uid == NULL || $uid == '' || $uid == null || $order->p_type == 2) {
// 			if(!empty($order->franchise_id)){
// 				$franchiseaddress =Franchise::where('id',$order->franchise_id)->value('name');
// 			}
// 			if($order->shipping_method === 'pickup'){
// 				$address=$franchiseaddress ;
// 			}else{
// 				$address=$order->address;
// 			}

                $data = [
                    'order_id' => $order->id,
                    'order_no' => $order->order_no,
                    'status' => $order->status,
                    'payment_method' => $order->payment_method,
                    'shipping_method' => $order->shipping_method,
                    'city_id' => $order->city_id,
                    'adminstatus' => $order->admin_status,
                    'delivery_date' => \Carbon\Carbon::parse($order->delivery_date)->format('d-m-Y'),
                    'delivery_time' => \Carbon\Carbon::parse($order->time->startingtime)->format('h:i A') . ' to ' . \Carbon\Carbon::parse($order->time->endingtime)->format('h:i A'),
                    'type' => $order->type,
                    'total_amount' => $order->total_amount,
                    'address' => $order->address,
                    'accept_time' => !empty($order->accept_time) ? \Carbon\Carbon::parse($order->accept_time)->format('d-m-Y h:i A') : Null,
                    'start_preparing_time' => !empty($order->start_preparing_time) ? \Carbon\Carbon::parse($order->start_preparing_time)->format('d-m-Y h:i A') : Null,
                    'stop_preparing_time' => !empty($order->stop_preparing_time) ? \Carbon\Carbon::parse($order->stop_preparing_time)->format('d-m-Y h:i A') : Null,
                    'way_to_delievered_time' => !empty($order->way_to_delievered_time) ? \Carbon\Carbon::parse($order->way_to_delievered_time)->format('d-m-Y h:i A') : Null,
                    'delieverd_time' => !empty($order->delieverd_time) ? \Carbon\Carbon::parse($order->delieverd_time)->format('d-m-Y h:i A') : Null,
                    'delieverd_time_id' => !empty($order->time_id) ? $order->time_id : Null,
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
                                $priceRate = PriceCategoryModel::where('id', $value)->first();
                                // array_push($priceRateName, $priceRate);
                                $ar[] = [
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

                        $data['items'][] = [
                            'item_id' => $item->id,
                            'product_name' => $item->product->name,
                            'product_id' => $item->product->id,
                            'product_price' => $item->product->price,
                            // 'discount_percentage'=>$discount_prices,
                            'flavour_id' => !empty($item->flavour_id) ? $item->flavour_id : NULL,
                            'flavour' => !empty($item->flavour_id) ? $item->flavour->name : NULL,
                            'flavour_price' => !empty($item->flavour_id) ? $item->flavour->rate : NULL,
                            'image' => !empty($item->product->image) ? Storage::url('app/public/' . $item->product->image) : null,
                            'amount' => $item->amount,
                            'weight' => !empty($item->weight) ? $item->weight : NULL,
                            'qty' => !empty($item->qty) ? $item->qty : NULL,
                            'voice_msg' => !empty($item->voice_msg) ? url('public/voice/' . $item->voice_msg) : NULL,
                            'customer_name' => $item->customer_name,
                            'customer_no' => $item->customer_no,
                            'cake_image' => (!empty($item->image)) ? url('public/orders/' . $item->image) : NULL,
                            'completed_image' => (!empty($item->completed_image)) ? Storage::url('app/public/' . $item->completed_image) : NULL,
                            'message_on_cake' => $item->message_on_cake,
                            'instruction' => $item->instruction,
                            'typeRate' => $priceRateName,
                            'is_photo' => $photo_rate,
                            'size' => $item->size,
                            'start_time' => $item->start_time,
                            'end_time' => $item->end_time,
                            'pdf' => (!empty($item->pdf)) ? url('public/' . $item->pdf) : NULL,

                        ];
                    }
                } else {

                    $item = $order->customitem;
                    $subcategory = SubCategoryModel::where('id', $item->sub_category_id)->first();
                    $priceRateName = [];
                    if (!empty($item->type_rate)) {
                        $type_ids = json_decode($item->type_rate);
                        $ar = [];
                        foreach ($type_ids as $key => $value) {
                            $priceRate = PriceCategoryModel::where('id', $value)->first();                    // array_push($priceRateName, $priceRate);
                            $ar[] = [
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

                    $data['items'][] = [
                        // 'subcategory'=>$subcategory_name,
                        'flavour' => $item->flavour->name,
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


                $assignOrder = AssignOrder::where('order_id', $order->id)->first();
                if (!empty($assignOrder)) {
                    $data['chef'] = [
                        'id' => $assignOrder->admin->id,
                        'name' => $assignOrder->admin->name,
                        'type' => $assignOrder->admin->type
                    ];

                    $deliveryboy = Admin::where('id', $assignOrder->delivery_boy_id)->first();
                    $data['delivery_boy'] = !empty($assignOrder->delivery_boy_id) ? [
                        'id' => $deliveryboy->id,
                        'name' => $deliveryboy->name,
                        'type' => $deliveryboy->type
                    ] : Null;


                }
                $fields[] = $data;
            }
        }
        return $fields;
    }

    private function adminsFields($admins) {
        $fields = [];
        foreach ($admins as $admin) {
            $fields[] =[
                'id'=>$admin->id,
                'name'=>$admin->name,
                'email'=>$admin->email,
                'mobile_no'=>$admin->mobile_no,
                'type'=>$admin->type,
                'is_custom_permission'=>$admin->is_custom_permission,
                'profile'=>(!empty($admin->profile) ? $admin->profile : asset('theme/images/default_profile.jpg')),
            ];
        }

        return $fields;
    }

    public function ordersForFranchise(Request $request)
    {
        $rules = [
            'date' => 'nullable',
        ];

        if ($this->ApiValidator($request->all(), $rules)) {
            if (Auth::guard('admin')->user()->type == 'Admin') {
                $uid = '';
                $orders = Order::where('franchises_id', $request->franchises_id)->orderBy('id', 'DESC')->get();
                $this->response['data'] = $this->orderListFields($orders, $uid);
            } elseif (Auth::guard('admin')->user()->type == 'Chef') {
                // dd('h');
                // $order_id=AssignOrder::value('order_id');
                $orders = Order::where(['franchises_id' => $request->franchises_id])->orderBy('id', 'DESC')->get();
                $uid = Auth::guard('admin')->user()->category_id;
                if ($uid == "0") {
                    $this->status = 200;
                    $this->response['data'] = $this->orderListsFields($orders, $uid);
                } else if ($uid == "1") {
                    $this->status = 200;
                    $this->response['data'] = $this->orderListFields($orders, $uid);
                }
            }

            $this->status = 200;
            $this->response['message'] = trans('api.orderlist', ['entity' => 'Order Listing']);
        }
        return $this->return_response();
    }

    public function getAllAdmin(Request $request)
    {
        $admins = Admin::where('id', '<>', 0)->get();
        $this->status = 200;
        $this->response['data'] = $this->adminsFields($admins);
        $this->response['message'] = "Admin Detail is listed";
        return $this->return_response();

    }

    public function changeCustomPermission(Request $request)
    {
        $admin = Admin::where('id', $request->id)->update([
            'is_custom_permission' => $request->is_custom_permission,
        ]);
        if ($admin) {
            $admins = Admin::all();
            $this->status = 200;
            $this->response['data'] = $this->adminsFields($admins);
            $this->response['message'] = "Admin Detail is Updated";
        } else {
            $this->status = 200;
            $this->response['message'] = "Admin Detail is not updated";
        }

        return $this->return_response();
    }

    public function franchiseAssign(Request $request)
    {

        $rules = [
            'order_id' => 'required|exists:assign_orders,order_id',
            'franchise_id' => 'required|exists:franchises,id',
        ];

        if ($this->ApiValidator($request->all(), $rules)) {

            $order_id = Order::where(['id' => $request->order_id])->value('id');
            if (!empty($order_id)) {
                $assign_order = AssignOrder::where('order_id', $order_id)
                    ->update([
                        'franchise_id' => $request->franchise_id,
                        'instruction_franchise' => $request->instruction_franchise,
                        'discount' => $request->discount,
                    ]);

                $admin_token = Admin::where('type', 'Admin')->value('push_token');
                if (!empty($admin_token)) {
                    sendPushMessage($admin_token, "Order Assigned to Franchise");
                }
                $franchise_token = Franchise::where(['id' => $request->franchise_id])->value('push_token');

                if (!empty($franchise_token)) {
                    sendPushMessage($franchise_token, "New Order Assigned");
                }

                $this->status = 200;
                $this->response['message'] = trans('api.assign', ['entity' => 'Order']);
            } else {
                $this->response['message'] = "Order is not completed";
            }

        }
        return $this->return_response();
    }

    public function orderInvoice(Request $request)
    {

        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 200);
        $rules = [
            'order_id' => 'required|exists:orders,id',
        ];

        if ($this->ApiValidator($request->all(), $rules)) {
            $logo = public_path('theme/images/logo.png');
            $order = Order::where('id', $request->order_id)->first();
            $delivery_man_id = AssignOrder::where('order_id', $request->order_id)->value('delivery_boy_id');
            $delivery_man = Admin::where('id', $delivery_man_id)->value('name');
            $delivery_man = !empty($delivery_man) ? $delivery_man : '';
            $pdf = PDF::loadView('admin.invoice', compact('order', 'delivery_man', 'logo'))->setPaper('A4');
            // return view('admin.invoice', compact('order', 'delivery_man','logo'));
            // dd(public_path('invoice/' .$filename));
            $filename = $request->order_id . '.pdf';
            $pdf->save(public_path('invoice/' . $filename));

            $this->status = 200;
            $this->response['data'] = url('public/invoice/' . $filename);
            $this->response['message'] = "Pdf Generate Successfully";

            return $this->return_response();
        }

    }
}
