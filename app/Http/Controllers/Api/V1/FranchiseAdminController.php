<?php

namespace App\Http\Controllers\Api\V1;

use App\Admin;
use App\Franchise;
use App\Http\Controllers\Controller;
use App\Models\AssignOrder;
use App\Models\CustomOrder;
use App\Models\FranchiseBalance;
use App\Models\FranchisePrice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PriceCategoryModel;
use App\Models\Product;
use App\Models\SubCategoryModel;
use App\SaleReturn;
use App\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\random;
use PDF;


class FranchiseAdminController extends Controller
{
    private function assignListFields($assignListitem, $uid)
    {
        $fields = [];
        foreach ($assignListitem as $assignOrder) {
            $order = $assignOrder->order;
            if ($order->p_type == $uid || $uid == NULL || $uid == '' || $uid == null) {

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
                    'franchise_discount_amount' => $order->total_amount - ($order->total_amount / $assignOrder->discount),
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
                $franchise_name = Franchise::where('id', $assignOrder->franchise_id)->first();
                if (!empty($franchise_name)) {
                    $data['franchise'] = [
                        'id' => $franchise_name->id,
                        'name' => $franchise_name->name,
                        'discount' => $assignOrder->discount,
                        'instruction_franchise' => $assignOrder->instruction_franchise,

                    ];
                }
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
        }
        return $fields;
    }

    private function assignsListFields($assignListitem, $uid)
    {

        $fields = [];

        foreach ($assignListitem as $assignOrder) {
            $order = $assignOrder->order;
            if ($order->p_type == $uid || $uid == NULL || $uid == '' || $uid == null || $order->p_type == 2) {

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
                    if (!empty($item)) {
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
                }

                $data['customer'] = !empty(Franchise::where('id', $order->franchises_id)->first(['id', 'name', 'address', 'mobile_no', 'email'])) ? Franchise::where('id', $order->franchises_id)->first(['id', 'name', 'address', 'mobile_no', 'email']) : $order->user()->first(['id', 'first_name', 'last_name', 'mobile_no', 'email']);

                $data['chef'] = [
                    'id' => $assignOrder->admin->id,
                    'name' => $assignOrder->admin->name,
                    'type' => $assignOrder->admin->type
                ];

                if (!empty($assignOrder->franchise_id)) {
                    $data['franchise'] = [
                        'id' => $assignOrder->franchise->id,
                        'name' => $assignOrder->franchise->name,
                        'discount	' => $assignOrder->discount,
                        'instruction_franchise	' => $assignOrder->instruction_franchise,

                    ];
                }
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
        }
        return $fields;
    }

    private function chefOrderListFields($orders)
    {

        $fields = [];

        foreach ($orders as $order) {
            $data = [
                'order_id' => $order->id,
                'order_no' => $order->order_no,
                'payment_method' => $order->payment_method,
                'status' => $order->status,
                'address' => $order->address,
                'note' => $order->note,
                'city_id' => $order->city_id,
                'adminstatus' => $order->admin_status,
                'accept_time' => $order->accept_time,
                'start_preparing_time' => $order->start_preparing_time,
                'stop_preparing_time' => $order->stop_preparing_time,
                'way_to_delievered_time' => $order->way_to_delievered_time,
                'delieverd_time' => $order->delieverd_time,
                'accept_time' => \Carbon\Carbon::parse($order->accept_time)->format('d-m-Y h:i A'),
                'start_preparing_time' => \Carbon\Carbon::parse($order->start_preparing_time)->format('d-m-Y h:i A'),
                'stop_preparing_time' => \Carbon\Carbon::parse($order->stop_preparing_time)->format('d-m-Y h:i A'),
                'way_to_delievered_time' => \Carbon\Carbon::parse($order->way_to_delievered_time)->format('d-m-Y h:i A'),
                'delieverd_time' => \Carbon\Carbon::parse($order->delieverd_time)->format('d-m-Y h:i A'),
                'delieverd_time_id' => !empty($order->time_id) ? $order->time_id : Null,
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
                        'product_name' => $item->product->name,
                        'product_price' => $item->product->price,
                        'flavour_id' => !empty($item->flavour_id) ? $item->flavour_id : NULL,
                        'flavour' => !empty($item->flavour_id) ? $item->flavour->name : NULL,
                        'flavour_price' => !empty($item->flavour_id) ? $item->flavour->rate : NULL,
                        'image' => Storage::url('app/public/' . $item->product->image),
                        'amount' => $item->amount,
                        'weight' => !empty($item->weight) ? $item->weight : NULL,
                        'qty' => !empty($item->qty) ? $item->qty : NULL,
                        'voice_msg' => !empty($item->voice_msg) ? url('public/voice' . $item->voice_msg) : NULL,
                        'customer_name' => $item->customer_name,
                        'customer_no' => $item->customer_no,
                        'cake_image' => (!empty($item->image)) ? url('public/orders/' . $item->image) : NULL,
                        'completed_image' => (!empty($item->completed_image)) ? Storage::url('app/public/' . $item->completed_image) : NULL,
                        'message_on_cake' => $item->message_on_cake,
                        'instruction' => $item->instruction,
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
                $subcategory_name = SubCategoryModel::where('id', $item->sub_category_id)->value('name');
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
                    'subcategory' => $subcategory_name,
                    'flavour_id' => $item->flavour_id,
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

            $data['instruction'] = AssignOrder::where('order_id', $order->id)->value('instruction');
            $fields[] = $data;
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
                'status' => $order->status,
                'payment_method' => $order->payment_method,
                'shipping_method' => $order->shipping_method,
                'city_id' => $order->city_id,
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
                    // dd($item->typeRate);

                    $priceRateName = [];
                    if (!empty($item->type_rate)) {
                        $type_ids = json_decode($item->type_rate);
                        $ar = [];
                        foreach ($type_ids as $key => $value) {
                            $priceRate = PriceCategoryModel::where('id', $value)->first();                    // array_push($priceRateName, $priceRate);
                            $ar = [
                                'id' => $value,
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
                            'id' => $item->is_photo,
                            'name' => $photoRate->cat_name,
                            'price' => $photoRate->price,
                        ];

                    }
                    $product = Product::find($item->product_id);

                    $category_id = SubCategoryModel::where('id', $product->subcategory_id)->value('category_id');
                    $discount_prices = (FranchisePrice::where(['franchise_id' => $order->franchises_id, 'category_id' => $category_id])->value('percentage'));

                    $data['items'][] = [
                        'item_id' => $item->id,
                        'product_id' => $item->product->id,
                        'product_name' => $item->product->name,
                        'product_price' => $item->product->price,
                        'discount' => $discount_prices,
                        'flavour_id' => !empty($item->flavour_id) ? $item->flavour_id : NULL,
                        'flavour' => !empty($item->flavour_id) ? $item->flavour->name : NULL,
                        'flavour_price' => !empty($item->flavour_id) ? $item->flavour->rate : NULL,
                        'image' => !empty($item->product->image) ? Storage::url('app/public/' . $item->product->image) : null,
                        'amount' => $item->amount,
                        'weight' => !empty($item->weight) ? $item->weight : NULL,
                        'qty' => !empty($item->qty) ? $item->qty : NULL,
                        'voice_msg' => !empty($item->voice_msg) ? url('public/voice' . $item->voice_msg) : NULL,
                        'customer_name' => $item->customer_name,
                        'customer_no' => $item->customer_no,
                        'cake_image' => (!empty($item->image)) ? url('public/orders/' . $item->image) : NULL,
                        'completed_image' => (!empty($item->completed_image)) ? Storage::url($item->completed_image) : NULL,
                        'message_on_cake' => $item->message_on_cake,
                        'instruction' => $item->instruction,
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
// 			dd($item);
                $subcategory_name = SubCategoryModel::where('id', $item->sub_category_id)->value('name');

                $priceRateName = [];
                if (!empty($item->type_rate)) {
                    $type_ids = json_decode($item->type_rate);
                    $ar = [];
                    foreach ($type_ids as $key => $value) {
                        $priceRate = PriceCategoryModel::where('id', $value)->first();                    // array_push($priceRateName, $priceRate);
                        $ar = [
                            'id' => $value,
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
                        'id' => $item->is_photo,
                        'name' => $photoRate->cat_name,
                        'price' => $photoRate->price,
                    ];

                }
                $subcategory = SubCategoryModel::where('id', $item->sub_category_id)->first();
                $percentage = (FranchisePrice::where(['franchise_id' => $order->franchises_id, 'category_id' => $subcategory->category_id])->value('percentage'));

                $data['items'][] = [
                    'item_id' => $item->id,
                    'subcategory_id' => $item->sub_category_id,
                    'subcategory' => $subcategory_name,
                    'flavour' => $item->flavour->name,
                    'flavour_id' => $item->flavour->id,
                    'flavour_price' => $item->flavour->rate,
                    'discount' => $percentage,
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
            $data['customer'] = Franchise::where('id', $order->franchises_id)->first(['id', 'name', 'address', 'mobile_no', 'email']);

            $assignOrder = AssignOrder::where('order_id', $order->id)->first();
            if (!empty($assignOrder)) {
                $data['chef'] = [
                    'id' => $assignOrder->admin->id,
                    'name' => $assignOrder->admin->name,
                    'type' => $assignOrder->admin->type
                ];

                if (!empty($assignOrder->delivery_boy_id)) {
                    $deliveryboy = Admin::where('id', $assignOrder->delivery_boy_id)->first();
                    $data['delivery_boy'] = [
                        'id' => $deliveryboy->id,
                        'name' => $deliveryboy->name,
                        'type' => $deliveryboy->type
                    ];
                }
            }
            $fields[] = $data;
        }


        return $fields;
    }

    private function orderSearchFields($orders)
    {
        $fields = [];

        foreach ($orders as $order) {
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
                        'item_id' => $item->id,
                        'product_name' => $item->product->name,
                        'product_id' => $item->product->id,
                        'product_price' => $item->product->price,
                        'flavour' => !empty($item->flavour_id) ? $item->flavour->name : NULL,
                        'image' => Storage::url('app/public/' . $item->product->image),
                        'amount' => $item->amount,
                        'customer_name' => $item->customer_name,
                        'customer_no' => $item->customer_no,
                        'weight' => !empty($item->weight) ? $item->weight : NULL,
                        'qty' => !empty($item->qty) ? $item->qty : NULL,
                        'voice_msg' => !empty($item->voice_msg) ? url('public/voice' . $item->voice_msg) : NULL,
                        'cake_image' => (!empty($item->image)) ? url('public/orders/' . $item->image) : NULL,
                        'completed_image' => (!empty($item->completed_image)) ? Storage::url('app/public/' . $item->completed_image) : NULL,
                        'message_on_cake' => $item->message_on_cake,
                        'instruction' => $item->instruction,
                        'note' => $item->note,
                        'size' => $item->size,
                        'start_time' => $item->start_time,
                        'end_time' => $item->end_time,
                        'typeRate' => $priceRateName,
                        'is_photo' => $photo_rate,
                        // 		'delivery_date' =>\Carbon\Carbon::parse($item->delivery_date)->format('d-m-Y'),
                    ];
                }
            } else {
                $item = $order->customitem;
                $subcategory_name = SubCategoryModel::where('id', $item->sub_category_id)->value('name');
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
                    'item_id' => $item->id,
                    'subcategory' => $subcategory_name,
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
            $data['customer'] = Franchise::where('id', $order->franchises_id)->first(['name', 'address', 'mobile_no', 'email']);
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

    public function orders(Request $request)
    {
        $rules = [
            'date' => 'nullable',
        ];

        if ($this->ApiValidator($request->all(), $rules)) {
            if (Auth::guard('admin')->user()->type == 'Admin' || Auth::guard('admin')->user()->type == 'SuperAdmin') {

                if (!empty($request->date)) {
                    $orders = Order::where('delivery_date', 'like', '%' . $request->date . '%')->where('user_id', NULL)->orderBy('id', 'DESC')->limit(1000)->get();
                } else if (!empty($request->month)) {
                    $orders = Order::where('delivery_date', 'like', '%' . $request->month . '%')->where('user_id', NULL)->orderBy('id', 'DESC')->limit(1000)->get();
                } else {
                    $orders = Order::where('user_id', NULL)->orderBy('id', 'DESC')->limit(1000)->get();
                }
                $this->response['data'] = $this->orderListFields($orders);
            } elseif (Auth::guard('admin')->user()->type == 'Chef') {
                $order_id = AssignOrder::orderBy('order_id', 'DESC')->value('order_id');
                $orders = Order::where('id', $order_id)->orderBy('id', 'DESC')->limit(1000)->get();
                $this->response['data'] = $this->chefOrderListFields($orders);
            }

            $this->status = 200;
            $this->response['message'] = trans('api.orderlist', ['entity' => 'Order Listing']);
        }
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

    public function assignList(Request $request)
    {

        if (Auth::guard('admin')->user()->type == 'Admin') {

            $assignList = AssignOrder::orderBy('order_id', 'DESC')->limit(1000)->get();
            $uid = Auth::guard('admin')->user()->category_id;

            $this->status = 200;
            $this->response['data'] = $this->assignListFields($assignList, $uid);
            $this->response['message'] = trans('api.list', ['entity' => 'Assign Order Listing']);

        } else if (Auth::guard('admin')->user()->type == 'Chef') {
            $assignList = AssignOrder::orderBy('order_id', 'DESC')->limit(1000)->get();
            $uid = Auth::guard('admin')->user()->category_id;
            if ($uid == "0") {
                $this->status = 200;
                $this->response['data'] = $this->assignsListFields($assignList, $uid);
            } else if ($uid == "1") {
                $this->status = 200;
                $this->response['data'] = $this->assignListFields($assignList, $uid);
            }


            $this->response['message'] = trans('api.list', ['entity' => 'Assign Order Listing']);
        } else if (Auth::guard('admin')->user()->type == 'Deliveryboy') {
            $assignList = AssignOrder::where('delivery_boy_id', Auth::guard('admin')->user()->id)->orderBy('order_id', 'DESC')->get();
            $uid = NULL;

            $this->status = 200;
            $this->response['data'] = $this->assignListFields($assignList, $uid);
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
            // dd(\Carbon\Carbon::now('Asia/Calcutta'));

            $franchises_id = Order::where('id', $request->order_id)->value('franchises_id');
            # code...
            $orderNo = Order::where('id', $request->order_id)->value('order_no');
            $order_status = Order::where('id', $request->order_id)
                ->update([
                    'status' => $request->status,
                    'admin_status' => $request->status,

                ]);


            if ($request->status === 'confirmed') {
                $franchise_token = Franchise::where('id', $franchises_id)->value('push_token');
                $order_time = Order::where('id', $request->order_id)
                    ->update([
                        'accept_time' => \Carbon\Carbon::now('Asia/Calcutta'),

                    ]);

                if (!empty($franchise_token)) {
                    sendPushMessage($franchise_token, "Your Order " . $orderNo . " is being prepared.");
                }

                $admin_token = Admin::where('type', 'Admin')->value('push_token');
                if (!empty($admin_token)) {
                    sendPushMessage($admin_token, " Order " . $orderNo . " is being prepared.");
                }

            } else if ($request->status === 'preparing') {
                $franchise_token = Franchise::where('id', $franchises_id)->value('push_token');
                $order_time = Order::where('id', $request->order_id)
                    ->update([

                        'start_preparing_time' => \Carbon\Carbon::now('Asia/Calcutta')
                    ]);

                if (!empty($franchise_token)) {
                    sendPushMessage($franchise_token, "Your Order " . $orderNo . " is start preparing.");
                }

                $admin_token = Admin::where('type', 'Admin')->value('push_token');
                if (!empty($admin_token)) {
                    sendPushMessage($admin_token, " Order " . $orderNo . " is start preparing.");
                }

            } else if ($request->status === 'rejected') {
                $franchise_token = Franchise::where('id', $franchises_id)->value('push_token');
                if (!empty($franchise_token)) {
                    sendPushMessage($franchise_token, "Your Order " . $orderNo . " is Rejected.");
                }

                $admin_token = Admin::where('type', 'Admin')->value('push_token');

                if (!empty($admin_token)) {
                    sendPushMessage($admin_token, " Order " . $orderNo . " is Reject.");
                }
            } else if ($request->status === 'on_the_way') {
                $franchise_token = Franchise::where('id', $franchises_id)->value('push_token');
                $order_time = Order::where('id', $request->order_id)
                    ->update([

                        'way_to_delievered_time' => \Carbon\Carbon::now('Asia/Calcutta')
                    ]);
                if (!empty($franchise_token)) {
                    sendPushMessage($franchise_token, "Your Order " . $orderNo . " is on the way for delivery.");
                }

                $admin_token = Admin::where('type', 'Admin')->value('push_token');
                if (!empty($admin_token)) {
                    sendPushMessage($admin_token, " Order " . $orderNo . " is on the way for delivery.");
                }
            } else if ($request->status === 'completed') {

                $franchise_token = Franchise::where('id', $franchises_id)->value('push_token');

                $order_time = Order::where('id', $request->order_id)
                    ->update([
                        'stop_preparing_time' => \Carbon\Carbon::now('Asia/Calcutta'),

                    ]);
                if (!empty($franchise_token)) {
                    sendPushMessage($franchise_token, "Your Order " . $orderNo . " is completed.");
                }

                $admin_token = Admin::where('type', 'Admin')->value('push_token');
                if (!empty($admin_token)) {
                    sendPushMessage($admin_token, " Order " . $orderNo . " is completed.");
                }
            } else if ($request->status === 'delivered') {
                $order_time = Order::where('id', $request->order_id)
                    ->update([
                        'delieverd_time' => \Carbon\Carbon::now('Asia/Calcutta'),

                    ]);
                $franchise_token = Franchise::where('id', $franchises_id)->value('push_token');
                if (!empty($franchise_token)) {
                    sendPushMessage($franchise_token, "Your Order " . $orderNo . " is delivered.");
                }

                $admin_token = Admin::where('type', 'Admin')->value('push_token');
                if (!empty($admin_token)) {
                    sendPushMessage($admin_token, " Order " . $orderNo . " is delivered.");
                }
            } else if ($request->status === 'ready_for_delivery') {
                $franchise_token = Franchise::where('id', $franchises_id)->value('push_token');
                if (!empty($franchise_token)) {
                    sendPushMessage($franchise_token, "Your Order " . $orderNo . " is being prepared.");
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

            $franchises_id = Order::where('id', $request->order_id)->value('franchises_id');
            $orderNo = Order::where('id', $request->order_id)->value('order_no');

            if ($request->status === 'preparing') {
                $franchise_token = Franchise::where('id', $franchises_id)->value('push_token');
                $order_time = Order::where('id', $request->order_id)
                    ->update([

                        'start_preparing_time' => \Carbon\Carbon::now('Asia/Calcutta')
                    ]);

                if (!empty($franchise_token)) {
                    sendPushMessage($franchise_token, "Your Order " . $orderNo . " is start preparing.");
                }

                $admin_token = Admin::where('type', 'Admin')->value('push_token');
                if (!empty($admin_token)) {
                    sendPushMessage($admin_token, " Order " . $orderNo . " is start preparing.");
                }

            } else if ($request->status === 'completed') {

                $franchise_token = Franchise::where('id', $franchises_id)->value('push_token');

                $order_time = Order::where('id', $request->order_id)
                    ->update([
                        'stop_preparing_time' => \Carbon\Carbon::now('Asia/Calcutta'),

                    ]);
                if (!empty($franchise_token)) {
                    sendPushMessage($franchise_token, "Your Order " . $orderNo . " is completed.");
                }

                $admin_token = Admin::where('type', 'Admin')->value('push_token');
                if (!empty($admin_token)) {
                    sendPushMessage($admin_token, " Order " . $orderNo . " is completed.");
                }
            }

            // dd($user_token. "------" .$admin_token);

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


            $franchises_id = Order::where('id', $request->order_id)->value('franchises_id');
            $orderNo = Order::where('id', $request->order_id)->value('order_no');

            if ($request->status === 'delivered') {
                $order_time = Order::where('id', $request->order_id)
                    ->update([
                        'delieverd_time' => \Carbon\Carbon::now('Asia/Calcutta'),

                    ]);
                $franchise_token = Franchise::where('id', $franchises_id)->value('push_token');
                if (!empty($franchise_token)) {
                    sendPushMessage($franchise_token, "Your Order " . $orderNo . " is delivered.");
                }

                $admin_token = Admin::where('type', 'Admin')->value('push_token');
                if (!empty($admin_token)) {
                    sendPushMessage($admin_token, " Order " . $orderNo . " is delivered.");
                }
            } else if ($request->status === 'on_the_way') {
                $order_time = Order::where('id', $request->order_id)
                    ->update([

                        'way_to_delievered_time' => \Carbon\Carbon::now('Asia/Calcutta')
                    ]);
                $franchise_token = Franchise::where('id', $franchises_id)->value('push_token');
                if (!empty($franchise_token)) {
                    sendPushMessage($franchise_token, "Your Order " . $orderNo . " is being prepared.");
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
        return $this->return_response();
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
                $userToken = User::where('id', $order->franchises_id)->value('push_token');
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

    public function franchiseOrdersearch(Request $request)
    {
        $rules = [
            'search' => 'nullable',
        ];

        if ($this->ApiValidator($request->all(), $rules)) {
            if (!empty($request->search)) {
                $user = Franchise::where('name', 'like', '%' . $request->search . '%')->value('id');
                $orders = Order::where('franchises_id', $user)->get();
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
                    $order = CustomOrder::where(['order_id' => $$request->order_id])->update(['completed_image' => $request->completed_image->store('completedimage')]);
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
            $balance = Franchise::where('id', $order->franchises_id)->value('balance');

            $order->total_amount = $request->amount;
            $order->save();
            $balancetotal = $balance - $request->amount;
            $debititem = [
                'franchise_id' => $order->franchises_id,
                'debit' => $request->amount,
                'totalbalance' => $balancetotal,
            ];
            FranchiseBalance::create($debititem);
            Franchise::where('id', $request->franchises_id)->update(['balance' => $balancetotal]);


            $user_id = Order::where('id', $request->order_id)->value('franchises_id');
            $orderNo = Order::where('id', $request->order_id)->value('order_no');
            $user_token = Franchise::where('id', $user_id)->value('push_token');
            if (!empty($user_token)) {
                sendPushMessage($user_token, "Check and proceed for Payment for " . $orderNo);
            }
            $admin_token = Admin::where('type', 'Admin')->value('push_token');
            if (!empty($admin_token)) {
                sendPushMessage($admin_token, "New order received");
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
            // dd($aa);
            // $deliveryboy =Admin::where('type','Deliveryboy')->get('id');
            // dd($deliveryboy);
            foreach ($deliveryboy as $deliveryboyid) {
                // dd($deliveryboyid->id);
                $assignList = AssignOrder::where('delivery_boy_id', $deliveryboyid)->get('order_id');
                $ar = [];
                // dd($assignList->);

                foreach ($assignList as $key) {
                    // dd($key->order_id);
                    $ar[] = $key->order_id;
                }

                $this->response['data'][$deliveryboyid] = $this->totalFields($ar);
            }


            // dd($ar);

            $this->status = 200;

            $this->response['message'] = trans('api.list', ['entity' => 'Total Amount Order Listing']);
        }

        return $this->return_response();
    }

    public function addBalance(Request $request)
    {
        $rules = [
            'franchise_id' => 'required|exists:franchises,id',
            'amount' => 'required'
        ];

        if ($this->ApiValidator($request->all(), $rules)) {
            $balance = Franchise::where('id', $request->franchise_id)->value('balance');
            $balancetotal = $balance + $request->amount;

            $credit = [
                'franchise_id' => $request->franchise_id,
                'credit' => $request->amount,
                'totalbalance' => $balancetotal,
            ];

            Franchise::where('id', $request->franchises_id)->update(['balance' => $balancetotal]);
            FranchiseBalance::create($credit);

            $this->status = 200;
            $this->response['message'] = "Balance Credited";

        }
        return $this->return_response();
    }

    public function minusBalance(Request $request)
    {
        $rules = [
            'franchise_id' => 'required|exists:franchises,id',
            'amount' => 'required'
        ];

        if ($this->ApiValidator($request->all(), $rules)) {
            $balance = Franchise::where('id', $request->franchise_id)->value('balance');
            $balancetotal = $balance - $request->amount;

            $credit = [
                'franchise_id' => $request->franchise_id,
                'credit' => $request->amount,
                'totalbalance' => $balancetotal,
            ];

            Franchise::where('id', $request->franchises_id)->update(['balance' => $balancetotal]);
            FranchiseBalance::create($credit);

            $this->status = 200;
            $this->response['message'] = "Balance Credited";

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

    public function multipleorderStatus(Request $request)
    {
        if (Auth::guard('admin')->user()->type == 'Admin') {
            $rules = [
                'order_id' => 'required|exists:orders,id',
                'status' => 'required|in:confirmed,rejected,preparing,on_the_way,delivered,ready_for_delivery,completed',
                'admin_status' => 'required|in:confirmed,rejected,preparing,on_the_way,delivered,ready_for_delivery,completed',
            ];

            foreach ($request->order_id as $order_id) {
                $franchises_id = Order::where('id', $order_id)->value('franchises_id');
                # code...
                $orderNo = Order::where('id', $order_id)->value('order_no');
                $order_status = Order::where('id', $order_id)
                    ->update([
                        'status' => $request->status,
                        'admin_status' => $request->status,

                    ]);


                if ($request->status === 'confirmed') {
                    $franchise_token = Franchise::where('id', $franchises_id)->value('push_token');
                    if (!empty($franchise_token)) {
                        sendPushMessage($franchise_token, "Your Order " . $orderNo . " is being prepared.");
                    }

                    $admin_token = Admin::where('type', 'Admin')->value('push_token');
                    if (!empty($admin_token)) {
                        sendPushMessage($admin_token, " Order " . $orderNo . " is being prepared.");
                    }

                } else if ($request->status === 'rejected') {
                    $franchise_token = Franchise::where('id', $franchises_id)->value('push_token');
                    if (!empty($franchise_token)) {
                        sendPushMessage($franchise_token, "Your Order " . $orderNo . " is being prepared.");
                    }

                    $admin_token = Admin::where('type', 'Admin')->value('push_token');
                    if (!empty($admin_token)) {
                        sendPushMessage($admin_token, " Order " . $orderNo . " is Reject.");
                    }
                } else if ($request->status === 'on_the_way') {
                    $franchise_token = Franchise::where('id', $franchises_id)->value('push_token');
                    if (!empty($franchise_token)) {
                        sendPushMessage($franchise_token, "Your Order " . $orderNo . " is being prepared.");
                    }

                    $admin_token = Admin::where('type', 'Admin')->value('push_token');
                    if (!empty($admin_token)) {
                        sendPushMessage($admin_token, " Order " . $orderNo . " is on the way for delivery.");
                    }
                } else if ($request->status === 'delivered') {
                    $franchise_token = Franchise::where('id', $franchises_id)->value('push_token');
                    if (!empty($franchise_token)) {
                        sendPushMessage($franchise_token, "Your Order " . $orderNo . " is being prepared.");
                    }

                    $admin_token = Admin::where('type', 'Admin')->value('push_token');
                    if (!empty($admin_token)) {
                        sendPushMessage($admin_token, " Order " . $orderNo . " is delivered.");
                    }
                } else if ($request->status === 'ready_for_delivery') {
                    $franchise_token = Franchise::where('id', $franchises_id)->value('push_token');
                    if (!empty($franchise_token)) {
                        sendPushMessage($franchise_token, "Your Order " . $orderNo . " is being prepared.");
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

                $franchises_id = Order::where('id', $order_id)->value('franchises_id');
                $orderNo = Order::where('id', $order_id)->value('order_no');

                $franchise_token = Franchise::where('id', $franchises_id)->value('push_token');
                if (!empty($franchise_token)) {
                    sendPushMessage($franchise_token, "Your Order " . $orderNo . " is being prepared.");
                }

                $admin_token = Admin::where('type', 'Admin')->value('push_token');
                if (!empty($admin_token)) {
                    sendPushMessage($admin_token, " Order " . $orderNo . " is " . $request->status);
                }
            }
            // dd($user_token. "------" .$admin_token);

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

            $franchises_id = Order::where('id', $order_id)->value('franchises_id');
            $orderNo = Order::where('id', $order_id)->value('order_no');

            if ($request->status === 'delivered') {
                $franchise_token = Franchise::where('id', $franchises_id)->value('push_token');
                if (!empty($franchise_token)) {
                    sendPushMessage($franchise_token, "Your Order " . $orderNo . " is being prepared.");
                }

                $admin_token = Admin::where('type', 'Admin')->value('push_token');
                if (!empty($admin_token)) {
                    sendPushMessage($admin_token, " Order " . $orderNo . " is delivered.");
                }
            } else if ($request->status === 'on_the_way') {
                $franchise_token = Franchise::where('id', $franchises_id)->value('push_token');
                if (!empty($franchise_token)) {
                    sendPushMessage($franchise_token, "Your Order " . $orderNo . " is being prepared.");
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

    public function orderEdit(Request $request)
    {
        $rules = [
            'order_id' => 'required|exists:orders,id',
            'item_id' => 'required|exists:order_items,id',
        ];
        if ($this->ApiValidator($request->all(), $rules)) {
            $order = Order::where('id', $request->order_id)->first();
            $order_item = Order::where(['id' => $request->item_id, 'order_id' => $request->order_id])->first();

            $total_amount = floatval($order->total_amount) - floatval($order_item->amount);
            $product = Product::find($order_item->product_id);

            $category_id = SubCategoryModel::where('id', $product->subcategory_id)->value('category_id');
            $discount_price1 = (FranchisePrice::where(['franchise_id' => $order->franchises_id, 'category_id' => $category_id])->value('percentage'));
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
                if ($amount == $request->amount) {
                    if (!empty($request->image)) {

                        $image = $request->image;
                        // Storage::delete($order_item->image);
                        $newname = date('Ymd') . $image->getClientOriginalName();
                        $image->move(public_path('orders'), $newname);
                    }


                    $totalamount = $total_amount + floatval($amount);
                    $order_item->flavour_id = $request->flavour_id;
                    $order_item->amount = $request->amount;
                    $order_item->weight = $request->weight;
                    $order_item->size = $request->size;
                    $order_item->voice_msg = $request->voice_msg;
                    $order_item->image = $newname;
                    $order_item->message_on_cake = $request->message_on_cake;
                    $order_item->instruction = $request->instruction;
                    $order_item->is_photo = $request->is_photo;
                    $order_item->type_rate = json_encode($priceRate);
                    $order_item->customer_no = $request->customer_no;
                    $order_item->customer_name = $request->customer_name;
                    $order_item->save();

                    // $order->shipping_method = $request->shipping_method;
                    // $order->franchise_id = $request->franchise_id;
                    // $order->city_id = $request->city_id;
                    // $order->time_id = $request->time_id;
                    // $order->address = $request->address;
                    // $order->zip = $request->zip;
                    $order->delivery_date = $request->delivery_date;
                    $order->total_amount = $totalamount;
                    $order->save();

                    $this->status = 200;
                    $this->response['message'] = "Order Edit Successfully";

                } else {

                    $this->response['message'] = "Amount mismatched!";
                }
            } else {
                $discountamount = ((($product->price) * ($request->qty)) * $discount_price) / 100;
                $totaldis = ($product->price) * ($request->qty);
                $amount = $totaldis - $discountamount;
                if ($amount == $request->amount) {
                    $totalamount = $total_amount + floatval($amount);


                    $order_item->amount = $request->amount;
                    $order_item->qty = $request->qty;
                    $order_item->save();

                    // $order->shipping_method = $request->shipping_method;
                    // $order->franchise_id = $request->franchise_id;
                    // $order->city_id = $request->city_id;
                    // $order->time_id = $request->time_id;
                    // $order->address = $request->address;
                    // $order->zip = $request->zip;
                    $order->delivery_date = $request->delivery_date;
                    $order->total_amount = $totalamount;
                    $order->save();

                    $this->status = 200;
                    $this->response['message'] = "Order Edit Successfully";
                } else {
                    $this->response['message'] = "Amount mismatched!";
                }
            }
            return $this->return_response();
        }
    }

    public function statistic(Request $request){
        $order = Orders::where('delivery_date',Carbon::now()->format('Y-m-d'))->get();
        dd($order);
    }
    public function salesReturnHistory(Request $request){
        $rules = [
            'date' => 'nullable',
        ];
        if ($this->ApiValidator($request->all(), $rules)) {
            $sales_returns= SaleReturn::where('id','<>',0)->orderBy('id','Desc')->get();
            $this->status = 200;
            $this->response['sales_returns'] = $this->salereturnFields($sales_returns);
            $this->response['message'] = "Sales Return listing Successfully";
        }
        return $this->return_response();

    }



}
