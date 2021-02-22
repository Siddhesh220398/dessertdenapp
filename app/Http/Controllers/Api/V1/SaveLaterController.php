<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\PriceCategoryModel;
use App\Models\Product;
use App\Models\SaveLater;
use App\Models\SubCategoryModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SaveLaterController extends Controller
{
    private function savelaterFields($user_id)
    {
        $items = SaveLater::where('user_id', $user_id)->get();
        $fields = ['total' => 0, 'items' => []];
        foreach ($items as $item) {

            $product = Product::where('id', $item->product_id)->first();
            if (!empty($product->subcategory_id)) {
                $subcategory = SubCategoryModel::where('id', $product->subcategory_id)->first();
            }
            $priceRateName = [];
            if (!empty($item->type_rate)) {
                $type_ids = json_decode($item->type_rate);
                $ar = [];
                foreach ($type_ids as $key => $value) {

                    $priceRate = PriceCategoryModel::where('id', $value)->first();
                    array_push($priceRateName, $priceRate);
                    // $ar['data']=[
                    // 	'name'=>$priceRate->cat_name,
                    // 	'price'=>$priceRate->price,
                    // ];
                    // dd($value);
                }
                // $priceRateName=$ar['data'];
            }

            $photo_rate = [];
            if (!empty($item->is_photo)) {
                $photoRate = PriceCategoryModel::where('id', $item->is_photo)->first();
                $photo_rate = [
                    'name' => $photoRate->cat_name,
                    'price' => $photoRate->price,
                ];

            }
            $fields['items'][] = [
                'item_id' => $item->id,
                'subcategory' => $subcategory->name,
                'subcategory_type' => $subcategory->subcat_type,
                'product_name' => $product->name,
                'product_price' => $product->price,
                'producttype' => $priceRateName,
                'is_photo' => $photo_rate,
                'quantity' => $product->quantity,
                'flavour' => !empty($item->flavour->name) ? $item->flavour->name : NULL,
                'image' => !empty($product->image) ? Storage::url($product->image) : NULL,
                'amount' => $item->amount,
                'cart_type' => $item->cart_type,
                'weight' => !empty($item->weight) ? $item->weight : NULL,
                'size' => !empty($item->size) ? $item->size : NULL,
                'cake_image' => (!empty($item->image)) ? url('public/orders/' . $item->image) : NULL,
                'message_on_cake' => !empty($item->message_on_cake) ? $item->message_on_cake : NULL,
                'instruction' => !empty($item->instruction) ? $item->instruction : NULL,
                'quantity' => !empty($item->qty) ? $item->qty : NULL,
                'customer_no' => !empty($item->customer_no) ? $item->customer_no : NULL,
                'customer_name' => !empty($item->customer_name) ? $item->customer_name : NULL,
//                'voice_msg' => !empty($item->voice_msg) ? url('public/voice/' . $item->voice_msg) : NULL,
                'note' => !empty($item->note) ? $item->note : NULL,
//                'delivery_date' => !empty($item->delivery_date) ? (\Carbon\Carbon::parse($item->delivery_date)->format('d-m-Y')) : NULL,

            ];
            $fields['total'] += $item->amount;
        }
        return $fields;
    }

    private function savelatersFields($franchise_id)
    {
        $items = SaveLater::where('franchise_id', $franchise_id)->get();
        $fields = ['total' => 0, 'items' => []];
        foreach ($items as $item) {

            $product = Product::where('id', $item->product_id)->first();
            if (!empty($product->subcategory_id)) {
                $subcategory = SubCategoryModel::where('id', $product->subcategory_id)->first();
            }
            $priceRateName = [];
            if (!empty($item->type_rate)) {
                $type_ids = json_decode($item->type_rate);
                $ar = [];
                foreach ($type_ids as $key => $value) {

                    $priceRate = PriceCategoryModel::where('id', $value)->first();
                    array_push($priceRateName, $priceRate);
                    // $ar['data']=[
                    // 	'name'=>$priceRate->cat_name,
                    // 	'price'=>$priceRate->price,
                    // ];
                    // dd($value);
                }
                // $priceRateName=$ar['data'];
            }

            $photo_rate = [];
            if (!empty($item->is_photo)) {
                $photoRate = PriceCategoryModel::where('id', $item->is_photo)->first();
                $photo_rate = [
                    'name' => $photoRate->cat_name,
                    'price' => $photoRate->price,
                ];

            }
            $fields['items'][] = [
                'item_id' => $item->id,
                'subcategory' => $subcategory->name,
                'subcategory_type' => $subcategory->subcat_type,
                'product_name' => $product->name,
                'product_price' => $product->price,
                'producttype' => $priceRateName,
                'is_photo' => $photo_rate,
                'quantity' => $product->quantity,
                'flavour' => !empty($item->flavour->name) ? $item->flavour->name : NULL,
                'image' => !empty($product->image) ? Storage::url($product->image) : NULL,
                'amount' => $item->amount,
                'cart_type' => $item->cart_type,
                'weight' => !empty($item->weight) ? $item->weight : NULL,
                'size' => !empty($item->size) ? $item->size : NULL,
                'cake_image' => (!empty($item->image)) ? url('public/orders/' . $item->image) : NULL,
                'message_on_cake' => !empty($item->message_on_cake) ? $item->message_on_cake : NULL,
                'instruction' => !empty($item->instruction) ? $item->instruction : NULL,
                'quantity' => !empty($item->qty) ? $item->qty : NULL,
                'customer_no' => !empty($item->customer_no) ? $item->customer_no : NULL,
                'customer_name' => !empty($item->customer_name) ? $item->customer_name : NULL,
//                'voice_msg' => !empty($item->voice_msg) ? url('public/voice/' . $item->voice_msg) : NULL,
                'note' => !empty($item->note) ? $item->note : NULL,
//                'delivery_date' => !empty($item->delivery_date) ? (\Carbon\Carbon::parse($item->delivery_date)->format('d-m-Y')) : NULL,

            ];
            $fields['total'] += $item->amount;
        }
        return $fields;
    }

    public function saveLater(Request $request)
    {

        $rules = [
            'item_id' => 'required|exists:carts,id',
        ];

        if ($this->ApiValidator($request->all(), $rules)) {

            $cart = Cart::where('id', $request->item_id)->first();
            if ($request->user_id) {
                $item = [
                    'user_id' => $request->user_id,
                    'product_id' => $cart->product_id,
                    'flavour_id' => $cart->flavour_id,
                    'amount' => $cart->amount,
                    'qty' => $cart->qty,
                    'weight' => $cart->weight,
                    'size' => (!empty($cart->size)) ? $cart->size : NULL,
                    'image' => (!empty($newname)) ? $newname : NULL,
                    'message_on_cake' => $cart->message_on_cake,
                    'instruction' => $cart->instruction,
                    'customer_no' => !empty($cart->customer_no) ? $cart->customer_no : NULL,
                    'customer_name' => !empty($cart->customer_name) ? $cart->customer_name : NULL,
//                    'voice_msg' => !empty($cart->voice_msg) ? $cart->voice_msg : NULL,
                    'cart_type' => $cart->cart_type,
                    'note' => $cart->note,
                    'is_photo' => $cart->photoprice_id,
                    'type_rate' => !empty($cart->type_rate) ? (json_encode($cart->type_rate)) : NULL,

                ];
                // 	dd($item);
                SaveLater::Create($item);
                Cart::where('id', $request->item_id)->delete();
                $this->response['data'] = $this->savelaterFields($request->user_id);
                $this->status = 200;
                $this->response['message'] = trans('api.list', ['entity' => 'Save']);
            } else {
                $item = [
                    'franchise_id' => $request->franchise_id,
                    'product_id' => $cart->product_id,
                    'flavour_id' => $cart->flavour_id,
                    'amount' => $cart->amount,
                    'qty' => $cart->qty,
                    'weight' => $cart->weight,
                    'size' => (!empty($cart->size)) ? $cart->size : NULL,
                    'image' => (!empty($newname)) ? $newname : NULL,
                    'message_on_cake' => $cart->message_on_cake,
                    'instruction' => $cart->instruction,
                    'customer_no' => !empty($cart->customer_no) ? $cart->customer_no : NULL,
                    'customer_name' => !empty($cart->customer_name) ? $cart->customer_name : NULL,
//                    'voice_msg' => !empty($cart->voice_msg) ? $cart->voice_msg : NULL,
                    'cart_type' => $cart->cart_type,
                    'note' => $cart->note,
                    'is_photo' => $cart->photoprice_id,
                    'type_rate' => !empty($cart->type_rate) ? (json_encode($cart->type_rate)) : NULL,

                ];

                SaveLater::Create($item);
                Cart::where('id', $request->item_id)->delete();
                $this->response['data'] = $this->savelatersFields($request->franchise_id);
                $this->status = 200;
                $this->response['message'] = trans('api.list', ['entity' => 'Save Later']);
            }
        } else {
            $this->status = 412;
            $this->response['message'] = "Something is wrong!";
        }


        return $this->return_response();
    }

    private function cartFields($user_id)
    {
        $items = Cart::where('user_id', $user_id)->get();
        $fields = ['total' => 0, 'items' => []];
        foreach ($items as $item) {

            $product = Product::where('id', $item->product_id)->first();
            if (!empty($product->subcategory_id)) {
                $subcategory = SubCategoryModel::where('id', $product->subcategory_id)->first();
            }
            $priceRateName = [];
            if (!empty($item->type_rate)) {
                $type_ids = json_decode($item->type_rate);
                $ar = [];
                foreach ($type_ids as $key => $value) {

                    $priceRate = PriceCategoryModel::where('id', $value)->first();
                    array_push($priceRateName, $priceRate);
                    // $ar['data']=[
                    // 	'name'=>$priceRate->cat_name,
                    // 	'price'=>$priceRate->price,
                    // ];
                    // dd($value);
                }
                // $priceRateName=$ar['data'];
            }

            $photo_rate = [];
            if (!empty($item->is_photo)) {
                $photoRate = PriceCategoryModel::where('id', $item->is_photo)->first();
                $photo_rate = [
                    'name' => $photoRate->cat_name,
                    'price' => $photoRate->price,
                ];

            }
            $fields['items'][] = [
                'item_id' => $item->id,
                'subcategory' => $subcategory->name,
                'subcategory_type' => $subcategory->subcat_type,
                'product_name' => $product->name,
                'product_price' => $product->price,
                'producttype' => $priceRateName,
                'is_photo' => $photo_rate,
                'quantity' => $product->quantity,
                'flavour' => !empty($item->flavour->name) ? $item->flavour->name : NULL,
                'image' => !empty($product->image) ? Storage::url($product->image) : NULL,
                'amount' => $item->amount,
                'cart_type' => $item->cart_type,
                'weight' => !empty($item->weight) ? $item->weight : NULL,
                'size' => !empty($item->size) ? $item->size : NULL,
                'cake_image' => (!empty($item->image)) ? url('public/orders/' . $item->image) : NULL,
                'message_on_cake' => !empty($item->message_on_cake) ? $item->message_on_cake : NULL,
                'instruction' => !empty($item->instruction) ? $item->instruction : NULL,
                'quantity' => !empty($item->qty) ? $item->qty : NULL,
                'customer_no' => !empty($item->customer_no) ? $item->customer_no : NULL,
                'customer_name' => !empty($item->customer_name) ? $item->customer_name : NULL,
//                'voice_msg' => !empty($item->voice_msg) ? url('public/voice/' . $item->voice_msg) : NULL,
                'note' => !empty($item->note) ? $item->note : NULL,
//                'delivery_date' => !empty($item->delivery_date) ? (\Carbon\Carbon::parse($item->delivery_date)->format('d-m-Y')) : NULL,

            ];
            $fields['total'] += $item->amount;
        }
        return $fields;
    }

    private function cartsFields($franchise_id)
    {
        $items = Cart::where('franchise_id', $franchise_id)->get();
        $fields = ['total' => 0, 'items' => []];
        foreach ($items as $item) {

            $product = Product::where('id', $item->product_id)->first();
            if (!empty($product->subcategory_id)) {
                $subcategory = SubCategoryModel::where('id', $product->subcategory_id)->first();
            }
            $priceRateName = [];
            if (!empty($item->type_rate)) {
                $type_ids = json_decode($item->type_rate);
                $ar = [];
                foreach ($type_ids as $key => $value) {

                    $priceRate = PriceCategoryModel::where('id', $value)->first();
                    array_push($priceRateName, $priceRate);
                    // $ar['data']=[
                    // 	'name'=>$priceRate->cat_name,
                    // 	'price'=>$priceRate->price,
                    // ];
                    // dd($value);
                }
                // $priceRateName=$ar['data'];
            }

            $photo_rate = [];
            if (!empty($item->is_photo)) {
                $photoRate = PriceCategoryModel::where('id', $item->is_photo)->first();
                $photo_rate = [
                    'name' => $photoRate->cat_name,
                    'price' => $photoRate->price,
                ];

            }
            $fields['items'][] = [
                'item_id' => $item->id,
                'subcategory' => $subcategory->name,
                'subcategory_type' => $subcategory->subcat_type,
                'product_name' => $product->name,
                'product_price' => $product->price,
                'producttype' => $priceRateName,
                'is_photo' => $photo_rate,
                'quantity' => $product->quantity,
                'flavour' => !empty($item->flavour->name) ? $item->flavour->name : NULL,
                'image' => !empty($product->image) ? Storage::url($product->image) : NULL,
                'amount' => $item->amount,
                'cart_type' => $item->cart_type,
                'weight' => !empty($item->weight) ? $item->weight : NULL,
                'size' => !empty($item->size) ? $item->size : NULL,
                'cake_image' => (!empty($item->image)) ? url('public/orders/' . $item->image) : NULL,
                'message_on_cake' => !empty($item->message_on_cake) ? $item->message_on_cake : NULL,
                'instruction' => !empty($item->instruction) ? $item->instruction : NULL,
                'quantity' => !empty($item->qty) ? $item->qty : NULL,
                'customer_no' => !empty($item->customer_no) ? $item->customer_no : NULL,
                'customer_name' => !empty($item->customer_name) ? $item->customer_name : NULL,
//                'voice_msg' => !empty($item->voice_msg) ? url('public/voice/' . $item->voice_msg) : NULL,
                'note' => !empty($item->note) ? $item->note : NULL,
//                'delivery_date' => !empty($item->delivery_date) ? (\Carbon\Carbon::parse($item->delivery_date)->format('d-m-Y')) : NULL,

            ];
            $fields['total'] += $item->amount;
        }
        return $fields;
    }

    public function addToCart(Request $request)
    {

        $rules = [
            'item_id' => 'required|exists:save_laters,id',
        ];

        if ($this->ApiValidator($request->all(), $rules)) {

            $cart = SaveLater::where('id', $request->item_id)->first();
            if ($request->user_id) {
                $item = [
                    'user_id' => $request->user_id,
                    'product_id' => $cart->product_id,
                    'flavour_id' => $cart->flavour_id,
                    'amount' => $cart->amount,
                    'qty' => $cart->qty,
                    'weight' => $cart->weight,
                    'size' => (!empty($cart->size)) ? $cart->size : NULL,
                    'image' => (!empty($newname)) ? $newname : NULL,
                    'message_on_cake' => $cart->message_on_cake,
                    'instruction' => $cart->instruction,
                    'customer_no' => !empty($cart->customer_no) ? $cart->customer_no : NULL,
                    'customer_name' => !empty($cart->customer_name) ? $cart->customer_name : NULL,
//                    'voice_msg' => !empty($cart->voice_msg) ? $cart->voice_msg : NULL,
                    'cart_type' => $cart->cart_type,
                    'note' => $cart->note,
                    'is_photo' => $cart->photoprice_id,
                    'type_rate' => !empty($cart->type_rate) ? (json_encode($cart->type_rate)) : NULL,

                ];
                // 	dd($item);
                Cart::Create($item);
                SaveLater::where('id', $request->item_id)->delete();
                $this->response['data'] = $this->cartFields($request->user_id);
                $this->status = 200;
                $this->response['message'] = trans('api.list', ['entity' => 'Cart']);
            } else {
                $item = [
                    'franchise_id' => $request->franchise_id,
                    'product_id' => $cart->product_id,
                    'flavour_id' => $cart->flavour_id,
                    'amount' => $cart->amount,
                    'qty' => $cart->qty,
                    'weight' => $cart->weight,
                    'size' => (!empty($cart->size)) ? $cart->size : NULL,
                    'image' => (!empty($newname)) ? $newname : NULL,
                    'message_on_cake' => $cart->message_on_cake,
                    'instruction' => $cart->instruction,
                    'customer_no' => !empty($cart->customer_no) ? $cart->customer_no : NULL,
                    'customer_name' => !empty($cart->customer_name) ? $cart->customer_name : NULL,
//                    'voice_msg' => !empty($cart->voice_msg) ? $cart->voice_msg : NULL,
                    'cart_type' => $cart->cart_type,
                    'note' => $cart->note,
                    'is_photo' => $cart->photoprice_id,
                    'type_rate' => !empty($cart->type_rate) ? (json_encode($cart->type_rate)) : NULL,

                ];
                // 	dd($item);
                Cart::Create($item);
                SaveLater::where('id', $request->item_id)->delete();
                $this->response['data'] = $this->cartsFields($request->franchise_id);
                $this->status = 200;
                $this->response['message'] = trans('api.list', ['entity' => 'Cart']);
            }
        } else {
            $this->status = 412;
            $this->response['message'] = "Something is wrong!";
        }


        return $this->return_response();
    }

    public function getSaveLater(Request $request)
    {
        $rules = [
            'user_id' => 'nullable|exists:users,id',
        ];
        if ($this->ApiValidator($request->all(), $rules)) {
            if ($request->user_id) {
                $this->response['data'] = $this->savelaterFields($request->user_id);
                $this->status = 200;
                $this->response['message'] = trans('api.list', ['entity' => 'Save']);
            } else {
                $this->response['data'] = $this->savelatersFields($request->franchise_id);
                $this->status = 200;
                $this->response['message'] = trans('api.list', ['entity' => 'Save']);

            }
        }
        return $this->return_response();
    }
}
