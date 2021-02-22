<?php

namespace App\Http\Controllers\Api\V1;

use App\Admin;
use App\Http\Controllers\Controller;
use App\Models\AdminToken;
use App\Models\AssignOrder;
use App\Models\CustomOrder;
use App\Franchise;
use App\Models\Order;
use App\Models\OrderImage;
use App\Models\OrderItem;
use App\Models\PriceCategoryModel;
use App\Models\Product;
use App\Models\SubCategoryModel;
use App\Models\UserBalance;
use App\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\random;
use Illuminate\Support\Str;
use PDF;

class AdminController extends Controller
{
    private function adminFields($admin)
    {
        return [
            'id' => $admin->id,
            'name' => $admin->name,
            'email' => $admin->email,
            'mobile_no' => $admin->mobile_no,
            'is_custom_permission' => $admin->is_custom_permission,
            'type' => $admin->type,
            'is_custom_permission' => $admin->is_custom_permission,
            'profile' => (!empty($admin->profile) ? $admin->profile : asset('theme/images/default_profile.jpg')),
        ];
    }

    private function chefFields($chefs)
    {
        $fields = [];
        foreach ($chefs as $chef) {
            $fields[] = [
                'id' => $chef->id,
                'name' => $chef->name,
                'email' => $chef->email,
                'mobile_no' => $chef->mobile_no,
                'department_id' => $chef->category_id,
                'is_custom_permission' => $chef->is_custom_permission,
                'type' => $chef->type,
                'is_custom_permission' => $chef->is_custom_permission,
                'profile' => (!empty($chef->profile) ? $chef->profile : asset('theme/images/default_profile.jpg')),
            ];
        }

        return $fields;
    }

    private function deliveryBoyFields($deliveryBoys)
    {
        $fields = [];
        foreach ($deliveryBoys as $deliveryBoy) {
            $fields[] = [
                'id' => $deliveryBoy->id,
                'name' => $deliveryBoy->name,
                'email' => $deliveryBoy->email,
                'mobile_no' => $deliveryBoy->mobile_no,
                'is_custom_permission' => $deliveryBoy->is_custom_permission,
                'type' => $deliveryBoy->type,
                'is_custom_permission' => $deliveryBoy->is_custom_permission,
                'profile' => (!empty($deliveryBoy->profile) ? $deliveryBoy->profile : asset('theme/images/default_profile.jpg')),
            ];
        }

        return $fields;
    }

    private function userFields($users)
    {
        $fields = [];
        foreach ($users as $user) {
            $fields[] = [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'mobile_no' => $user->mobile_no,
                'balance' => $user->balance,
                'is_balance' => $user->is_balance,
                'profile' => (!empty($user->profile) ? Storage::url('app/public/' . $user->profile) : asset('theme/images/default_profile.jpg')),
            ];
        }

        return $fields;
    }

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

            if ($order->type == "Normal") {
                $photo_cake = OrderItem::where('order_id', $order->id)->where('is_photo', '<>', '')->count('id');
            }
            $data = [
                'order_id' => $order->id,
                'order_no' => $order->order_no,
                'payment_method' => $order->payment_method,
                'status' => $order->status,
                'photo_cake' => !empty($photo_cake) ? $photo_cake : 0,
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

            $data['franchise'] = [
                'id' => $assignOrder->franchise->id,
                'name' => $assignOrder->franchise->name,
                'discount	' => $assignOrder->discount,
                'instruction_franchise	' => $assignOrder->instruction_franchise,

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

    private function chefOrderListFields($orders)
    {

        $fields = [];

        foreach ($orders as $order) {
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
                'adminstatus' => $order->admin_status,
                'address' => $address,
                'delieverd_time_id' => !empty($order->time_id) ? $order->time_id : Null,
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
                        'message_on_cake' => $item->message_on_cake,
                        'instruction' => $item->instruction,
                        'size' => $item->size,
                        'note' => $item->note,
                        'is_photo' => $item->is_photo,
                        'start_time' => $item->start_time,
                        'end_time' => $item->end_time,
                        'pdf' => (!empty($item->pdf)) ? url('public/' . $item->pdf) : NULL,
                        // 		'delivery_date' =>\Carbon\Carbon::parse($item->delivery_date)->format('d-m-Y'),

                    ];
                }
            } else {
                $item = $order->customitem;
                $data['items'][] = [
                    'subcategory' => $item->subcategory->name,
                    'flavour' => $item->flavour->name,
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
                    // 	'delivery_date' =>\Carbon\Carbon::parse($item->delivery_date)->format('d-m-Y'),
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

            $data['instruction'] = AssignOrder::where('order_id', $order->id)->value('instruction');
            $fields[] = $data;
        }
        return $fields;
    }

    private function orderListFields($orders)
    {

        $fields = [];

        foreach ($orders as $order) {
            if ($order->type == "Normal") {
                $photo_cake = OrderItem::where('order_id', $order->id)->where('is_photo', '<>', '')->count('id');
            }
            $data = [
                'order_id' => $order->id,
                'order_no' => $order->order_no,
                'status' => $order->status,
                'payment_method' => $order->payment_method,
                'shipping_method' => $order->shipping_method,
                'city_id' => $order->city_id,
                'photo_cake' => !empty($photo_cake) ? $photo_cake : 0,
                'adminstatus' => $order->admin_status,
                'delivery_date' => \Carbon\Carbon::parse($order->delivery_date)->format('d-m-Y'),
                'delivery_time' => \Carbon\Carbon::parse($order->time->startingtime)->format('h:i A') . ' to ' . \Carbon\Carbon::parse($order->time->endingtime)->format('h:i A'),
                'type' => $order->type,
                'note' => $order->note,
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
                            $priceRate = PriceCategoryModel::where('id', $value)->first();                    // array_push($priceRateName, $priceRate);
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
                        'flavour_id' => !empty($item->flavour_id) ? $item->flavour_id : '',
                        // 		'flavour'=>!empty($item->flavour_id) ? $item->flavour->name :'',
                        // 		'flavour_price'=>!empty($item->flavour_id) ? $item->flavour->rate :'',
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
                        // 		'delivery_date' =>\Carbon\Carbon::parse($item->delivery_date)->format('d-m-Y'),

                    ];
                }
            } else {

                $item = $order->customitem;
                if(!empty($item)){
                $priceRateName = [];
                if (!empty($item->type_rate)) {
                    $type_ids = json_decode($item->type_rate);
                    $ar = [];
                    foreach ($type_ids as $key => $value) {
                        $priceRate = PriceCategoryModel::where('id', $value)->first();                    // array_push($priceRateName, $priceRate);
                        $ar = [
                            'id' => $priceRate->id,
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
                        'id' => $photoRate->id,
                        'name' => $photoRate->cat_name,
                        'price' => $photoRate->price,
                    ];

                }

                $subcategory_name = SubCategoryModel::where('id', $item->sub_category_id)->value('name');


                $data['items'][] = [

                    'item_id' => $item->id,
                    'subcategory_id' => $item->sub_category_id,
                    'subcategory' => $subcategory_name,
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
                }
                $data['idea_images'] = [];
                $data['cake_images'] = [];

                foreach ($order->images()->where(['type' => 'idea'])->get() as $item) {
                    $data['idea_images'][] = url('public/' . $item->image);
                }
                foreach ($order->images()->where(['type' => 'cake'])->get() as $item) {
                    $data['cake_images'][] = url('public/' . $item->image);
                }
            }
            $data['customer'] = $order->user()->first(['id', 'first_name', 'last_name', 'mobile_no', 'email']);

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
        return $fields;
    }

    private function orderSearchFields($orders)
    {
        $fields = [];

        foreach ($orders as $order) {
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
                'user_id' => $order->user_id,
                'customer_name' => !empty($order->user) ? $order->user->first_name . ' ' . $order->user->last_name : '',
                'status' => $order->status,
                'adminstatus' => $order->admin_status,
                // 	'delivery_date' =>\Carbon\Carbon::parse($order->delivery_date)->format('d-m-Y'),
                'delivery_time' => \Carbon\Carbon::parse($order->time->startingtime)->format('h:i A') . ' to ' . \Carbon\Carbon::parse($order->time->endingtime)->format('h:i A'),
                'type' => $order->type,
                'note' => $order->note,
                'total_amount' => $order->total_amount,
                'address' => $address,
                'accept_time' => $order->accept_time,
                'start_preparing_time' => $order->start_preparing_time,
                'stop_preparing_time' => $order->stop_preparing_time,
                'way_to_delievered_time' => $order->way_to_delievered_time,
                'delieverd_time' => $order->delieverd_time,
                'pdf' => (!empty($item->pdf)) ? url('public/' . $item->pdf) : NULL,
                'dept_type' => $order->p_type


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
                        'product_name' => $item->product->name,
                        'flavour' => !empty($item->flavour_id) ? $item->flavour->name : NULL,
                        'image' => !empty($item->product->image) ? Storage::url('app/public/' . $item->product->image) : NULL,
                        'amount' => $item->amount,
                        'weight' => !empty($item->weight) ? $item->weight : NULL,
                        'qty' => !empty($item->qty) ? $item->qty : NULL,
                        'voice_msg' => !empty($item->voice_msg) ? url('public/voice/' . $item->voice_msg) : NULL,
                        'cake_image' => (!empty($item->image)) ? url('public/orders/' . $item->image) : NULL,
                        'completed_image' => (!empty($item->completed_image)) ? Storage::url('app/public/' . $item->completed_image) : NULL,
                        'message_on_cake' => $item->message_on_cake,
                        'instruction' => $item->instruction,
                        'size' => $item->size,
                        'note' => $item->note,
                        'is_photo' => $photo_rate,
                        'typeRate' => $priceRateName,
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
                    'size' => $item->size,
                    'is_photo' => $photo_rate,
                    'typeRate' => $priceRateName,
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
            $fields[] = $data;
        }
        return $fields;
    }

    private function totalFields($order_id)
    {
        $fields = ['order_items' => [], 'total' => 0, 'deliveryboy' => ''];
        $delivery_boy_id = AssignOrder::where('order_id', $order_id)->value('delivery_boy_id');
        $delivery_boy = Admin::where('id', $delivery_boy_id)->value('name');
        foreach ($order_id as $key) {

            $orders = Order::where(['id' => $key, 'status' => 'delivered', 'payment_method' => 'cod', 'delivery_date' => \Carbon\Carbon::now()->format('Y-m-d'), 'franchises_id' => NULL])->get();

            foreach ($orders as $order) {
                $data = [
                    'order_id' => $order->id,
                    'order_no' => $order->order_no,
                    'total_amount' => $order->total_amount
                ];
                $amount = $order->total_amount;

                $fields['deliveryboy'] = $delivery_boy;
                $fields['order_items'][] = $data;
                $fields['total'] += $amount;
            }
        }
        return $fields;
    }

    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|exists:admins,email',
            'password' => 'required',
        ];


        if ($this->ApiValidator($request->all(), $rules)) {
            $admin = Admin::where('email', $request->email)->first();
            if (!empty($admin)) {
                if (Hash::check($request->password, $admin->password)) {
                    $data['admin'] = $this->adminFields($admin);
                    $token = Str::random(80);
                    AdminToken::updateOrCreate(['admin_id' => $admin->id], ['admin_id' => $admin->id, 'token' => $token]);
                    if (!empty($request->device_id)) {
                        $admin->push_token = $request->device_id;
                        $admin->token = $request->device_token;
                        $admin->save();
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
        $data['admin'] = $this->adminFields(Auth::guard('admin')->user());
        $this->response['message'] = trans('api.list', ['entity' => 'Profile']);
        $this->status = 200;
        $this->response['data'] = $data;
        return $this->return_response();
    }

    public function orders(Request $request)
    {
        $rules = [
            'date' => 'nullable',
        ];

        if ($this->ApiValidator($request->all(), $rules)) {
            if (Auth::guard('admin')->user()->type == 'Admin' || Auth::guard('admin')->user()->type == 'SuperAdmin') {

                if (!empty($request->date)) {
                    $orders = Order::where('delivery_date', 'like', '%' . $request->date . '%')->where('franchises_id', NULL)->orderBy('id', 'DESC')->get();
                } else if (!empty($request->month)) {
                    $orders = Order::where('delivery_date', 'like', '%' . $request->month . '%')->where('franchises_id', NULL)->orderBy('id', 'DESC')->get();
                } else {
                    $orders = Order::where('franchises_id', NULL)->orderBy('id', 'DESC')->limit(1000)->get();
                }
                $this->response['data'] = $this->orderListFields($orders);
            } elseif (Auth::guard('admin')->user()->type == 'Chef') {
                // $order_id=AssignOrder::value('order_id');
                $orders = Order::where(['p_type' => Auth::guard('admin')->user()->category_id])->orderBy('id', 'DESC')->limit(1000)->get();
                $this->response['data'] = $this->orderListFields($orders);
            }

            $this->status = 200;
            $this->response['message'] = trans('api.orderlist', ['entity' => 'Order Listing']);
        }
        return $this->return_response();
    }

    public function chefList(Request $request)
    {
        $chef = Admin::where('type', 'Chef')->get();
        $this->status = 200;
        $this->response['data'] = $this->chefFields($chef);
        $this->response['message'] = trans('api.list', ['entity' => 'Chef Listing']);
        return $this->return_response();
    }

    public function chefAssign(Request $request)
    {

        $rules = [
            'order_id' => 'required|exists:orders,id',
            'admin_id' => 'required|exists:admins,id',
            'instruction' => 'nullable',
        ];

        if ($this->ApiValidator($request->all(), $rules)) {
            $exists = AssignOrder::updateOrCreate(['order_id' => $request->order_id], ['order_id' => $request->order_id, 'admin_id' => $request->admin_id, 'instruction' => $request->instruction]);

            $admin_token = Admin::where('type', 'Admin')->value('push_token');
            if (!empty($admin_token)) {
                sendPushMessage($admin_token, "Order Assigned to Chef");
            }
            $chef_token = Admin::where(['id' => $request->admin_id, 'type' => 'chef'])->value('push_token');
            if (!empty($chef_token)) {
                sendPushMessage($chef_token, "New Order Assigned");
            }

            $this->status = 200;
            $this->response['message'] = trans('api.assign', ['entity' => 'Order']);

        }
        return $this->return_response();
    }

    public function multiplechefAssign(Request $request)
    {

        $rules = [
            'order_id' => 'required|exists:orders,id',
            'admin_id' => 'required|exists:admins,id',
            'instruction' => 'nullable',
        ];

        if ($this->ApiValidator($request->all(), $rules)) {
            foreach ($request->order_id as $order_id) {

                $exists = AssignOrder::updateOrCreate(['order_id' => $request->order_id], ['order_id' => $request->order_id, 'admin_id' => $request->admin_id, 'instruction' => $request->instruction]);

                $this->status = 200;
                $this->response['message'] = trans('api.assign', ['entity' => 'Order']);
            }

            $admin_token = Admin::where('type', 'Admin')->value('push_token');
            if (!empty($admin_token)) {
                sendPushMessage($admin_token, "Order Assigned to Chef");
            }
            $chef_token = Admin::where(['id' => $request->admin_id, 'type' => 'chef'])->value('push_token');
            if (!empty($chef_token)) {
                sendPushMessage($chef_token, "New Order Assigned");
            }
        }
        return $this->return_response();
    }

    public function assignList(Request $request)
    {

        if (Auth::guard('admin')->user()->type == 'Admin' || Auth::guard('admin')->user()->type == 'SuperAdmin') {

            $assignList = AssignOrder::get();

            $this->status = 200;
            $this->response['data'] = $this->assignListFields($assignList);
            $this->response['message'] = trans('api.list', ['entity' => 'Assign Order Listing']);

        } else if (Auth::guard('admin')->user()->type == 'Chef') {
            $assignList = AssignOrder::where('admin_id', Auth::guard('admin')->user()->id)->get();
            $this->status = 200;
            $this->response['data'] = $this->assignListFields($assignList);
            $this->response['message'] = trans('api.list', ['entity' => 'Assign Order Listing']);
        } else if (Auth::guard('admin')->user()->type == 'Deliveryboy') {
            $assignList = AssignOrder::where('delivery_boy_id', Auth::guard('admin')->user()->id)->get();

            $this->status = 200;
            $this->response['data'] = $this->assignListFields($assignList);
            $this->response['message'] = trans('api.list', ['entity' => 'Assign Order Listing']);
        }

        return $this->return_response();
    }

    public function orderStatus(Request $request)
    {
        if (Auth::guard('admin')->user()->type == 'Admin') {
            $rules = [
                'order_id' => 'required|exists:orders,id',
                'status' => 'required|in:confirmed,rejected,preparing,on_the_way,delivered,ready_for_delivery,completed',
                'admin_status' => 'required|in:confirmed,rejected,preparing,on_the_way,delivered,ready_for_delivery,completed',
            ];

            $user_id = Order::where('id', $request->order_id)->value('user_id');
            $orderNo = Order::where('id', $request->order_id)->value('order_no');
            $order_status = Order::where('id', $request->order_id)
                ->update([
                    'status' => $request->status,
                    'admin_status' => $request->status,
                ]);

            if ($request->status === 'confirmed') {
                $user_token = User::where('id', $user_id)->value('push_token');
                $order_time = Order::where('id', $request->order_id)
                    ->update([
                        'accept_time' => \Carbon\Carbon::now('Asia/Calcutta'),
                    ]);

                if (!empty($user_token)) {
                    sendPushMessage($user_token, "Your Order " . $orderNo . " is Received.");
                }

                $admin_token = Admin::where('type', 'Admin')->value('push_token');
                if (!empty($admin_token)) {
                    sendPushMessage($admin_token, " Order " . $orderNo . " is Received.");
                }

            } else if ($request->status === 'preparing') {
                $user_token = User::where('id', $user_id)->value('push_token');
                $order_time = Order::where('id', $request->order_id)
                    ->update([

                        'start_preparing_time' => \Carbon\Carbon::now('Asia/Calcutta')
                    ]);

                if (!empty($user_token)) {
                    sendPushMessage($user_token, "Your Order " . $orderNo . " is start preparing.");
                }

                $admin_token = Admin::where('type', 'Admin')->value('push_token');
                if (!empty($admin_token)) {
                    sendPushMessage($admin_token, " Order " . $orderNo . " is start preparing.");
                }

            } else if ($request->status === 'completed') {
                $user_token = User::where('id', $user_id)->value('push_token');
                $order_time = Order::where('id', $request->order_id)
                    ->update([

                        'stop_preparing_time' => \Carbon\Carbon::now('Asia/Calcutta'),
                    ]);

                if (!empty($user_token)) {
                    sendPushMessage($user_token, "Your Order " . $orderNo . " is Completed.");
                }

                $admin_token = Admin::where('type', 'Admin')->value('push_token');
                if (!empty($admin_token)) {
                    sendPushMessage($admin_token, " Order " . $orderNo . " is Completed.");
                }

            } else if ($request->status === 'rejected') {
                $user_token = User::where('id', $user_id)->value('push_token');
                if (!empty($user_token)) {
                    sendPushMessage($user_token, "Your Order " . $orderNo . " is Rejected.");
                }

                $admin_token = Admin::where('type', 'Admin')->value('push_token');
                if (!empty($admin_token)) {
                    sendPushMessage($admin_token, " Order " . $orderNo . " is Reject.");
                }
            } else if ($request->status === 'on_the_way') {
                $user_token = User::where('id', $user_id)->value('push_token');
                $order_time = Order::where('id', $request->order_id)
                    ->update([

                        'way_to_delievered_time' => \Carbon\Carbon::now('Asia/Calcutta')
                    ]);
                if (!empty($user_token)) {
                    sendPushMessage($user_token, "Your Order " . $orderNo . " is on the way for delivery.");
                }

                $admin_token = Admin::where('type', 'Admin')->value('push_token');
                if (!empty($admin_token)) {
                    sendPushMessage($admin_token, " Order " . $orderNo . " is on the way for delivery.");
                }
            } else if ($request->status === 'delivered') {
                $order_time = Order::where('id', $request->order_id)
                    ->update([
                        'delieverd_time' => \Carbon\Carbon::now('Asia/Calcutta'),

                    ]);

                $user_token = User::where('id', $user_id)->value('push_token');
                if (!empty($user_token)) {
                    sendPushMessage($user_token, "Your Order " . $orderNo . " is delivered.");
                }

                $admin_token = Admin::where('type', 'Admin')->value('push_token');
                if (!empty($admin_token)) {
                    sendPushMessage($admin_token, " Order " . $orderNo . " is delivered.");
                }
            } else if ($request->status === 'ready_for_delivery') {
                $user_token = User::where('id', $user_id)->value('push_token');
                if (!empty($user_token)) {
                    sendPushMessage($user_token, "Your Order " . $orderNo . " is ready to be delivered at your door step.");
                }
            }


        } else if (Auth::guard('admin')->user()->type == 'Chef') {
            $rules = [
                'order_id' => 'required|exists:orders,id',
                'status' => 'required|in:confirmed,rejected,preparing,on_the_way,delivered,ready_for_delivery,completed',
                'admin_status' => 'required|in:confirmed,rejected,preparing,on_the_way,delivered,ready_for_delivery,completed',
            ];

            $order_status = Order::where('id', $request->order_id)
                ->update([
                    'status' => $request->status,
                    'admin_status' => $request->admin_status
                ]);

            $user_id = Order::where('id', $request->order_id)->value('user_id');
            $orderNo = Order::where('id', $request->order_id)->value('order_no');

            $user_token = User::where('id', $user_id)->value('push_token');
            if (!empty($user_token)) {
                sendPushMessage($user_token, "Your Order " . $orderNo . " is " . $request->status);
            }

            $admin_token = Admin::where('type', 'Admin')->value('push_token');
            if (!empty($admin_token)) {
                sendPushMessage($admin_token, " Order " . $orderNo . " is " . $request->status);
            }

        } else if (Auth::guard('admin')->user()->type == 'Deliveryboy') {

            $rules = [
                'order_id' => 'required|exists:orders,id',
                'status' => 'required|in:confirmed,rejected,preparing,on_the_way,delivered,ready_for_delivery,completed',
                'admin_status' => 'required|in:confirmed,rejected,preparing,on_the_way,delivered,ready_for_delivery,completed',
            ];

            $order_status = Order::where('id', $request->order_id)
                ->update([
                    'status' => $request->status,
                    'admin_status' => $request->admin_status
                ]);


            $user_id = Order::where('id', $request->order_id)->value('user_id');
            $orderNo = Order::where('id', $request->order_id)->value('order_no');

            if ($request->status === 'delivered') {
                $user_token = User::where('id', $user_id)->value('push_token');
                if (!empty($user_token)) {
                    sendPushMessage($user_token, "Your Order " . $orderNo . " is delivered.");
                }

                $admin_token = Admin::where('type', 'Admin')->value('push_token');
                if (!empty($admin_token)) {
                    sendPushMessage($admin_token, " Order " . $orderNo . " is delivered.");
                }
            } else if ($request->status === 'on_the_way') {
                $user_token = User::where('id', $user_id)->value('push_token');
                if (!empty($user_token)) {
                    sendPushMessage($user_token, "Your Order " . $orderNo . " is on the way for delivery.");
                }

                $admin_token = Admin::where('type', 'Admin')->value('push_token');
                if (!empty($admin_token)) {
                    sendPushMessage($admin_token, " Order " . $orderNo . " is on the way for delivery.");
                }
            }
        }

        $this->status = 200;
        $this->response['message'] = trans('api.change', ['entity' => 'Status']);
        return $this->return_response();

    }

    public function setTimer(Request $request)
    {
        if (Auth::guard('admin')->user()->type == 'Chef') {
            $rules = [
                'order_id' => 'required|exists:orders,id',
                'set_time' => 'required',
            ];

            $order_status = OrderItem::where('id', $request->order_id)
                ->update([
                    'start_time' => $request->set_time,
                ]);

            $this->status = 200;
            $this->response['message'] = "Timer is Start";
            return $this->return_response();
        }
    }

    public function stopTimer(Request $request)
    {
        if (Auth::guard('admin')->user()->type == 'Chef') {
            $rules = [
                'order_id' => 'required|exists:orders,id',
                'end_time' => 'required',
            ];

            $order_status = OrderItem::where('id', $request->order_id)
                ->update([
                    'end_time' => $request->end_time,
                ]);

            $this->status = 200;
            $this->response['message'] = "Timer is Start";
            return $this->return_response();
        }
    }

    public function setTimerCustom(Request $request)
    {
        if (Auth::guard('admin')->user()->type == 'Chef') {
            $rules = [
                'order_id' => 'required|exists:orders,id',
                'set_time' => 'required',
            ];

            $order_status = CustomOrder::where('id', $request->order_id)
                ->update([
                    'start_time' => $request->set_time,
                ]);

            $this->status = 200;
            $this->response['message'] = "Timer is Start";
            return $this->return_response();
        }
    }

    public function stopTimerCustom(Request $request)
    {
        if (Auth::guard('admin')->user()->type == 'Chef') {
            $rules = [
                'order_id' => 'required|exists:orders,id',
                'end_time' => 'required',
            ];

            $order_status = CustomOrder::where('id', $request->order_id)
                ->update([
                    'end_time' => $request->end_time,
                ]);

            $this->status = 200;
            $this->response['message'] = "Timer is Start";
            return $this->return_response();
        }
    }

    public function deliveryBoyList(Request $request)
    {
        $deliveryBoy = Admin::where('type', 'Deliveryboy')->get();
        $this->status = 200;
        $this->response['data'] = $this->deliveryBoyFields($deliveryBoy);
        $this->response['message'] = trans('api.list', ['entity' => 'Delivery Boy Listing']);
        return $this->return_response();
    }

    public function deliveryBoyAssign(Request $request)
    {

        $rules = [
            'order_id' => 'required|exists:assign_orders,order_id',
            'delivery_boy_id' => 'required|exists:admins,id',
        ];

        if ($this->ApiValidator($request->all(), $rules)) {

            $deliver_id = Order::where(['id' => $request->order_id])->value('id');
            if (!empty($deliver_id)) {
                $assign_order = AssignOrder::where('order_id', $deliver_id)
                    ->update([
                        'delivery_boy_id' => $request->delivery_boy_id,
                        'instruction_delivery_boy' => $request->instruction_delivery_boy,
                    ]);

                $admin_token = Admin::where('type', 'Admin')->value('push_token');
                if (!empty($admin_token)) {
                    sendPushMessage($admin_token, "Order Assigned to Deliveryboy");
                }
                $delivery_token = Admin::where(['id' => $request->delivery_boy_id, 'type' => 'Deliveryboy'])->value('push_token');

                if (!empty($delivery_token)) {
                    sendPushMessage($delivery_token, "New Order Assigned");
                }

                $this->status = 200;
                $this->response['message'] = trans('api.assign', ['entity' => 'Order']);
            } else {
                $this->response['message'] = "Order is not completed";
            }

        }
        return $this->return_response();
    }

    public function multipledeliveryBoyAssign(Request $request)
    {

        $rules = [
            'order_id' => 'required|exists:assign_orders,order_id',
            'delivery_boy_id' => 'required|exists:admins,id',
        ];

        if ($this->ApiValidator($request->all(), $rules)) {
            foreach ($request->order_id as $order_id) {

                $deliver_id = Order::where(['id' => $order_id, 'admin_status' => 'completed'])->value('id');
                if (!empty($deliver_id)) {
                    $assign_order = AssignOrder::where('order_id', $deliver_id)
                        ->update([
                            'delivery_boy_id' => $request->delivery_boy_id,
                            'instruction_delivery_boy' => $request->instruction_delivery_boy,
                        ]);

                    $admin_token = Admin::where('type', 'Admin')->value('push_token');
                    if (!empty($admin_token)) {
                        sendPushMessage($admin_token, "Order Assigned to Deliveryboy");
                    }
                    $delivery_token = Admin::where(['id' => $request->delivery_boy_id, 'type' => 'Deliveryboy'])->value('push_token');
// dd($delivery_token);
                    if (!empty($delivery_token)) {
                        sendPushMessage($delivery_token, "New Order Assigned");
                    }

                    $this->status = 200;
                    $this->response['message'] = trans('api.assign', ['entity' => 'Order']);
                } else {
                    $this->response['message'] = "Order is not completed";
                }
            }
        }
        return $this->return_response();
    }

    public function generatePdf(Request $request)
    {


        $rules = [
            'order_id' => 'required|exists:custom_orders,order_id',
        ];
        if ($this->ApiValidator($request->all(), $rules)) {

            $customeorder = CustomOrder::where('order_id', $request->order_id)->first();

            $staffname = AssignOrder::where('order_id', $request->order_id)->value('admin_id');

            $name = Admin::where('id', $staffname)->value('name');

            $customeimage = OrderImage::where(['order_id' => $request->order_id])->get();
            if (!empty($customeimage)) {
                $customeorder_image = OrderImage::where(['order_id' => $request->order_id, 'type' => 'cake'])->value('image');
                $customeorder_imageidea = OrderImage::where(['order_id' => $request->order_id, 'type' => 'idea'])->value('image');
            } else {
                $customeorder_image = public_path('orders/defaultcake.jpeg');
                $customeorder_imageidea = public_path('orders/defaultcake.jpeg');
            }
            $image = $customeorder_image;
            $imageidea = $customeorder_imageidea;

// return view('admin.pdfgenerate', compact('customeorder','image','imageidea','name'));
            $pdf = PDF::loadView('admin.pdfgenerate', compact('customeorder', 'image', 'imageidea', 'name'));
            $filename = $request->order_id . '.pdf';
            $pdf->save(public_path('generatepdf/' . $filename));

            $customeorders = CustomOrder::where('order_id', $request->order_id)->update([
                'pdf' => 'generatepdf/' . $filename,
            ]);

            if ($customeorders) {
                $this->status = 200;
                $this->response['pdf'] = url('public/generatepdf/' . $filename);
                $this->response['message'] = "Success";
            } else {
                $this->status = 401;
                $this->response['message'] = "error";
            }

            return $this->return_response();
        }
    }

    public function adminReject(Request $request)
    {
        $rules = [
            'order_id' => 'required|exists:custom_orders,order_id',
            'reject_instruction' => 'required',
            'edit_order' => 'required|in:0,1'
        ];

        if ($this->ApiValidator($request->all(), $rules)) {
            $customitem = CustomOrder::where('order_id', $request->order_id)
                ->update([
                    'reject_instruction' => $request->reject_instruction,
                    'edit' => $request->edit_order
                ]);

            if ($customitem) {
                $orderNo = Order::where('id', $request->order_id)->value('order_no');
                $order = Order::find($request->order_id);
                $order->status = 'rejected';
                $userToken = User::where('id', $order->user_id)->value('push_token');
                if (!empty($userToken)) {
                    sendPushMessage($userToken, "Your Order " . $orderNo . " is rejected");
                }

                $admin_token = Admin::where('type', 'Admin')->value('push_token');
                if (!empty($admin_token)) {
                    sendPushMessage($admin_token, " Order " . $orderNo . " is  Rejected");
                }

                $this->status = 200;
                $this->response['message'] = "Order is rejected";
            } else {
                $this->response['message'] = "Something is wrong";
            }
        }
        return $this->return_response();
    }

    public function userOrdersearch(Request $request)
    {
        $rules = [
            'search' => 'nullable',
        ];

        if ($this->ApiValidator($request->all(), $rules)) {
            if (!empty($request->search)) {
                $user = User::where('first_name', 'like', '%' . $request->search . '%')->value('id');
                $orders = Order::where('user_id', $user)->get();
                if (!empty($orders)) {
                    $this->status = 200;
                    $this->response['data'] = $this->orderSearchFields($orders);
                    $this->response['message'] = trans('api.orderlist', ['entity' => 'Order Listing']);
                } else {
                    $this->status = 401;
                    $this->response['message'] = "There is no Order Related to User";
                }

            }

        }
        return $this->return_response();
    }

    public function orderImage(Request $request)
    {
        $rules = [
            'completed_image' => 'required',
            'order_id' => 'required|exists:orders,id',
        ];
        if ($this->ApiValidator($request->all(), $rules)) {

            if (Auth::guard('admin')->user()->type == 'Chef') {

                $type = Order::where('id', $request->order_id)->value('type');
                if ($type === 'Normal') {

                    $order = OrderItem::where(['order_id' => $request->order_id, 'product_id' => $request->product_id])->update(['completed_image' => $request->completed_image->store('completedimage')]);
                } else {
                    $order = CustomOrder::where(['order_id' => $request->order_id])->update(['completed_image' => $request->completed_image->store('completedimage')]);
                }

            }
            $this->status = 200;
            $this->response['message'] = "Image Uploaded Successfully";
        }
        return $this->return_response();
    }

    public function customAmount(Request $request)
    {
        $rules = [
            'order_id' => 'required|exists:custom_orders,order_id',
            'amount' => 'required'
        ];

        if ($this->ApiValidator($request->all(), $rules)) {
            $order = Order::find($request->order_id);
            $order->total_amount = $request->amount;
            $order->save();

            $user_id = Order::where('id', $request->order_id)->value('user_id');
            $orderNo = Order::where('id', $request->order_id)->value('order_no');
            $user_token = User::where('id', $user_id)->value('push_token');
            if (!empty($user_token)) {
                sendPushMessage($user_token, "Check and proceed for Payment for " . $orderNo);
            }


            $this->status = 200;
            $this->response['message'] = "Amount updated successfully";
        }
        return $this->return_response();
    }

    public function todayTotal(Request $request)
    {
        if (Auth::guard('admin')->user()->type == 'Admin') {


            $deliveryboy = AssignOrder::where('delivery_boy_id', '!=', NULL)->groupBy('delivery_boy_id')->pluck('delivery_boy_id')->toArray();

            foreach ($deliveryboy as $deliveryboyid) {
                $assignList = AssignOrder::where('delivery_boy_id', $deliveryboyid)->get('order_id');
                $ar[] = [];

                foreach ($assignList as $key) {
                    $ar[] = $key->order_id;
                }
                $this->response['data'][$deliveryboyid] = $this->totalFields($ar);
            }
            $this->status = 200;
            $this->response['message'] = trans('api.list', ['entity' => 'Total Amount Order Listing']);
        }
        return $this->return_response();
    }

    public function multipleorderStatus(Request $request)
    {
        if (Auth::guard('admin')->user()->type == 'Admin') {
            $rules = [
                'order_id' => 'required|exists:orders,id',
                'status' => 'required|in:confirmed,rejected,preparing,on_the_way,delivered,ready_for_delivery,completed',
                'admin_status' => 'required|in:confirmed,rejected,preparing,on_the_way,delivered,ready_for_delivery,completed',
            ];
            foreach ($request->order_id as $order_id) {
                $user_id = Order::where('id', $order_id)->value('user_id');
                $orderNo = Order::where('id', $order_id)->value('order_no');
                $order_status = Order::where('id', $order_id)
                    ->update([
                        'status' => $request->status,
                        'admin_status' => $request->status,

                    ]);

                if ($request->status === 'confirmed') {
                    $user_token = User::where('id', $user_id)->value('push_token');
                    if (!empty($user_token)) {
                        sendPushMessage($user_token, "Your Order " . $orderNo . " is being prepared.");
                    }

                    $admin_token = Admin::where('type', 'Admin')->value('push_token');
                    if (!empty($admin_token)) {
                        sendPushMessage($admin_token, " Order " . $orderNo . " is being prepared.");
                    }

                } else if ($request->status === 'rejected') {
                    $user_token = User::where('id', $user_id)->value('push_token');
                    if (!empty($user_token)) {
                        sendPushMessage($user_token, "Your Order " . $orderNo . " is Rejected.");
                    }

                    $admin_token = Admin::where('type', 'Admin')->value('push_token');
                    if (!empty($admin_token)) {
                        sendPushMessage($admin_token, " Order " . $orderNo . " is Reject.");
                    }
                } else if ($request->status === 'on_the_way') {
                    $user_token = User::where('id', $user_id)->value('push_token');
                    if (!empty($user_token)) {
                        sendPushMessage($user_token, "Your Order " . $orderNo . " is on the way for delivery.");
                    }

                    $admin_token = Admin::where('type', 'Admin')->value('push_token');
                    if (!empty($admin_token)) {
                        sendPushMessage($admin_token, " Order " . $orderNo . " is on the way for delivery.");
                    }
                } else if ($request->status === 'delivered') {
                    $user_token = User::where('id', $user_id)->value('push_token');
                    if (!empty($user_token)) {
                        sendPushMessage($user_token, "Your Order " . $orderNo . " is delivered.");
                    }

                    $admin_token = Admin::where('type', 'Admin')->value('push_token');
                    if (!empty($admin_token)) {
                        sendPushMessage($admin_token, " Order " . $orderNo . " is delivered.");
                    }
                } else if ($request->status === 'ready_for_delivery') {
                    $user_token = User::where('id', $user_id)->value('push_token');
                    if (!empty($user_token)) {
                        sendPushMessage($user_token, "Your Order " . $orderNo . " is ready to be delivered at your door step.");
                    }
                }

            }
        } else if (Auth::guard('admin')->user()->type == 'Chef') {
            $rules = [
                'order_id' => 'required|exists:orders,id',
                'status' => 'required|in:confirmed,rejected,preparing,on_the_way,delivered,ready_for_delivery,completed',
                'admin_status' => 'required|in:confirmed,rejected,preparing,on_the_way,delivered,ready_for_delivery,completed',
            ];
            foreach ($request->order_id as $order_id) {
                $order_status = Order::where('id', $order_id)
                    ->update([
                        'status' => $request->status,
                        'admin_status' => $request->admin_status
                    ]);

                $user_id = Order::where('id', $order_id)->value('user_id');
                $orderNo = Order::where('id', $order_id)->value('order_no');

                $user_token = User::where('id', $user_id)->value('push_token');
                if (!empty($user_token)) {
                    sendPushMessage($user_token, "Your Order " . $orderNo . " is " . $request->status);
                }

                $admin_token = Admin::where('type', 'Admin')->value('push_token');
                if (!empty($admin_token)) {
                    sendPushMessage($admin_token, " Order " . $orderNo . " is " . $request->status);
                }
            }

        } else if (Auth::guard('admin')->user()->type == 'Deliveryboy') {
            $rules = [
                'order_id' => 'required|exists:orders,id',
                'status' => 'required|in:confirmed,rejected,preparing,on_the_way,delivered,ready_for_delivery,completed',
                'admin_status' => 'required|in:confirmed,rejected,preparing,on_the_way,delivered,ready_for_delivery,completed',
            ];
            foreach ($request->order_id as $order_id) {
                $order_status = Order::where('id', $order_id)
                    ->update([
                        'status' => $request->status,
                        'admin_status' => $request->admin_status
                    ]);
            }

            $user_id = Order::where('id', $order_id)->value('user_id');
            $orderNo = Order::where('id', $order_id)->value('order_no');

            if ($request->status === 'delivered') {
                $user_token = User::where('id', $user_id)->value('push_token');
                if (!empty($user_token)) {
                    sendPushMessage($user_token, "Your Order " . $orderNo . " is delivered.");
                }

                $admin_token = Admin::where('type', 'Admin')->value('push_token');
                if (!empty($admin_token)) {
                    sendPushMessage($admin_token, " Order " . $orderNo . " is delivered.");
                }
            } else if ($request->status === 'on_the_way') {
                $user_token = User::where('id', $user_id)->value('push_token');
                if (!empty($user_token)) {
                    sendPushMessage($user_token, "Your Order " . $orderNo . " is on the way for delivery.");
                }

                $admin_token = Admin::where('type', 'Admin')->value('push_token');
                if (!empty($admin_token)) {
                    sendPushMessage($admin_token, " Order " . $orderNo . " is on the way for delivery.");
                }
            }
        }

        $this->status = 200;
        $this->response['message'] = trans('api.change', ['entity' => 'Status']);
        return $this->return_response();

    }

    public function orderDelete(Request $request)
    {
        $rules = [
            'order_id' => 'required|exists:orders,id',
        ];
        // dd($request->order_id);
        if ($this->ApiValidator($request->all(), $rules)) {
            foreach ($request->order_id as $order_id) {
                Order::where('id', $order_id)->delete();
                OrderItem::where('id', $order_id)->delete();
                AssignOrder::where('order_id', $order_id)->delete();
            }

            $this->status = 200;
            $this->response['message'] = "Order are deleted";
        }

        return $this->return_response();
    }

    public function orderEdit(Request $request)
    {
        $rules = [
            'order_id' => 'required',
        ];
        if ($this->ApiValidator($request->all(), $rules)) {
            $order_type = Order::where('id', $request->order_id)->value('type');
            $order = [
                'shipping_method' => $request->shipping_method,
                'franchise_id' => $request->franchise_id,
                'city_id' => $request->city_id,
                'time_id' => $request->delivery_time_id,
                'address' => $request->address,
                'zip' => $request->zip,
                'total_amount' => $request->total_amount,
                'delivery_date' => $request->delivery_date,
                // 'p_type'=>$request->p_type,

            ];

            $order = Order::where('id', $request->order_id)->update($order);


            if ($order_type === 'Normal') {
                $photoprice_id = '';
                if (!empty($request->photoprice_id)) {
                    $photoprice_id = PriceCategoryModel::where('id', $request->photoprice_id)->value('name');
                }

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


                $order_items = [
                    'flavour_id' => !empty($request->flavour_id) ? $request->flavour_id : null,
                    'amount' => $request->amount,
                    'weight' => !empty($request->weight) ? $request->weight : Null,
                    'size' => $photoprice_id,
                    'image' => !empty($request->image) ? $request->image : Null,
                    'message_on_cake' => !empty($request->message_on_cake) ? $request->message_on_cake : null,
                    'instruction' => !empty($request->instruction) ? $request->instruction : Null,
                    'qty' => !empty($request->qty) ? $request->qty : NULL,
                    'voice_msg' => !empty($item->voice_msg) ? url('public/voice/' . $item->voice_msg) : NULL,
                    'customer_no' => !empty($request->customer_no) ? $request->customer_no : Null,
                    'customer_name' => !empty($request->customer_name) ? $request->customer_name : null,
                    'is_photo' => !empty($request->photoprice_id) ? $request->photoprice_id : Null,
                    'type_rate' => !empty($priceRate) ? (json_encode($priceRate)) : '',

                ];

                OrderItem::where(['order_id' => $request->order_id, 'id' => $request->item_id])->update($order_items);
                $this->status = 200;
                $this->response['message'] = "Order are updated";
            } else {
                $total = 0;
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
                $photoprice_id = '';
                if (!empty($request->photoprice_id)) {
                    $photoprice_id = PriceCategoryModel::where('id', $request->photoprice_id)->value('cat_name');
                }
                $order_items = [
                    // 'theme' => $request->theme,
                    'flavour_id' => $request->flavour_id,
                    'amount' => $request->amount,
                    'weight' => !$request->weight,
                    'size' => $photoprice_id,
                    'is_photo' => $request->photoprice_id,
                    'type_rate' => !empty($priceRate) ? (json_encode($priceRate)) : '',
                    // 'image'=> $request->image ,
                    'weight' => $request->weight,
                    'message_on_cake' => $request->message_on_cake,

                    'instruction' => $request->instruction,
                    // 'voice_msg' =>  $request->voice_msg ,
                    'customer_no' => $request->customer_no,
                    'customer_name' => $request->customer_name,
                    'completed_image' => $request->completed_image,
                ];
                $custom = CustomOrder::where('order_id', $request->order_id)->update($order_items);

                // if(!empty($request->idea)){
                // 	foreach ($request->idea as $image) {
                // 		$newname=$image->getClientOriginalName();
                // 		$order_images[] = new OrderImage(['image' => 'orders/'.$newname,'type' => 'idea']);
                // 		$image->move(public_path('orders'), $newname);
                // 	}
                // }
                // if(!empty($request->cake)){
                // 	foreach ($request->cake as $image) {
                // 		$newname=$image->getClientOriginalName();
                // 		$order_images[] = new OrderImage(['image' =>'orders/'.$newname,'type' => 'cake']);
                // 		$image->move(public_path('orders'), $newname);
                // 	}
                // }
                // $order->customimages()->update($order_images);
            }
            $this->status = 200;
            $this->response['message'] = "Order are updated";
        }
        return $this->return_response();
    }

    public function multipleschefAssign(Request $request)
    {

        $rules = [
            'order_id' => 'required|exists:orders,id',
            'admin_id' => 'required|exists:admins,id',
            'instruction' => 'nullable',
        ];

        if ($this->ApiValidator($request->all(), $rules)) {
            foreach ($request->product_id as $product_id => $value) {

                $exists = AssignOrder::where(['order_id' => $request->order_id, 'product_id' => $value])->count();
                if (!$exists) {
                    $assign_order = new AssignOrder;
                    $assign_order->order_id = $request->order_id;
                    $assign_order->product_id = $value;

                    $assign_order->admin_id = $request->admin_id[$product_id];
                    $assign_order->instruction = $request->instruction[$product_id];
                    $assign_order->save();
                    $admin_token = Admin::where('type', 'Admin')->value('push_token');
                    if (!empty($admin_token)) {
                        sendPushMessage($admin_token, "Order Assigned to Chef");
                    }
                    $chef_token = Admin::where(['id' => $request->admin_id[$product_id], 'type' => 'chef'])->value('push_token');
                    if (!empty($chef_token)) {
                        sendPushMessage($chef_token, "New Order Assigned");
                    }

                    $this->status = 200;
                    $this->response['message'] = trans('api.assign', ['entity' => 'Order']);
                } else {
                    $this->response['message'] = "Order already assigned.";
                }
            }
        }
        return $this->return_response();
    }

    public function getUser(Request $request)
    {
        $user = User::all();
        $this->status = 200;
        $this->response['data'] = $this->userFields($user);
        $this->response['message'] = "Customer Detail is listed";
        return $this->return_response();
    }

    public function activeBalance(Request $request)
    {
        $rules = [
            'user_id' => 'required|exists:users,id',
            'is_balance' => 'required|in:1,0',
        ];
        if ($this->ApiValidator($request->all(), $rules)) {
            User::where('id', $request->user_id)->update(['is_balance' => $request->is_balance]);
            $this->status = 200;
            $this->response['message'] = "Customer can Pay From Vallet";
        }
        return $this->return_response();
    }

    public function addUserBalance(Request $request)
    {
        $rules = [
            'user_id' => 'required|exists:users,id',
            'amount' => 'required'
        ];

        if ($this->ApiValidator($request->all(), $rules)) {
            $balance = User::where('id', $request->user_id)->value('balance');
            $balancetotal = $balance + $request->amount;

            $credit = [
                'user_id' => $request->user_id,
                'credit' => $request->amount,
                'totalbalance' => $balancetotal,
            ];

            User::where('id', $request->user_id)->update(['balance' => $balancetotal]);
            UserBalance::create($credit);

            $this->status = 200;
            $this->response['message'] = "Balance Credited";

        }
        return $this->return_response();
    }

    public function minusBalance(Request $request)
    {
        $rules = [
            'user_id' => 'required|exists:users,id',
            'amount' => 'required'
        ];

        if ($this->ApiValidator($request->all(), $rules)) {
            $balance = User::where('id', $request->user_id)->value('balance');
            $balancetotal = $balance - $request->amount;

            $credit = [
                'user_id' => $request->user_id,
                'credit' => $request->amount,
                'totalbalance' => $balancetotal,
            ];

            Franchise::where('id', $request->user_id)->update(['balance' => $balancetotal]);
            FranchiseBalance::create($credit);

            $this->status = 200;
            $this->response['message'] = "Balance Credited";

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

    public function normalgeneratePdf(Request $request)
    {

        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 200);
        $rules = [
            'order_id' => 'required|exists:order_items,order_id',
        ];
        if ($this->ApiValidator($request->all(), $rules)) {

            $customeorder = OrderItem::where(['order_id' => $request->order_id, 'id' => $request->item_id])->first();
            $order = Order::where('id', $request->order_id)->first();
            $staffname = AssignOrder::where('order_id', $request->order_id)->value('admin_id');

            $name = Admin::where('id', $staffname)->value('name');


            $customeorder_image = url('public/orders/' . $customeorder->image);
            $customeorder_imageidea = Product::where(['id' => $customeorder->product_id])->value('image');

            $image = $customeorder_image;
            $imageidea = Storage::url('app/public/' . $customeorder_imageidea);
            // dd($imageidea);

            // return view('admin.normalpdfgenerate', compact('customeorder','order','image','imageidea','name','priceRateName'));
            $pdf = PDF::loadView('admin.normalpdfgenerate', compact('customeorder', 'order', 'image', 'imageidea', 'name'));
            $filename = $request->order_id . '-' . $request->item_id . '.pdf';
            $pdf->save(public_path('generatepdf/' . $filename));

            $customeorders = OrderItem::where(['order_id' => $request->order_id, 'id' => $request->item_id])->update([
                'pdf' => 'generatepdf/' . $filename,
            ]);

            if ($customeorders) {
                $this->status = 200;
                $this->response['pdf'] = url('public/generatepdf/' . $filename);
                $this->response['message'] = "Success";
            } else {
                $this->status = 401;
                $this->response['message'] = "error";
            }
            return $this->return_response();
        }
    }


}
