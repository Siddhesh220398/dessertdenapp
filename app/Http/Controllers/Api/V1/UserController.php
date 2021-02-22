<?php

namespace App\Http\Controllers\Api\V1;

use App\Admin;
use App\Http\Controllers\Controller;
use App\Models\AssignOrder;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\CustomOrder;
use App\Models\Flavour;
use App\Models\Franchise;
use App\Models\Order;
use App\Models\OrderImage;
use App\Models\OrderItem;
use App\Models\PriceCategoryModel;
use App\Models\Product;
use App\Models\SubCategoryModel;
use App\Models\UserBalance;
use App\Models\Wishlist;
use App\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Razorpay\Api\Api;

class UserController extends Controller
{

    private function userFields($user)
    {
        return [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'mobile_no' => $user->mobile_no,
            'balance' => $user->balance,
            'is_balance' => $user->is_balance,
            'profile' => (!empty($user->profile) ? Storage::url($user->profile) : asset('theme/images/default_profile.jpg')),
        ];
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
                'voice_msg' => !empty($item->voice_msg) ? url('public/voice/' . $item->voice_msg) : NULL,
                'note' => !empty($item->note) ? $item->note : NULL,
                'delivery_date' => !empty($item->delivery_date) ? (\Carbon\Carbon::parse($item->delivery_date)->format('d-m-Y')) : NULL,

            ];
            $fields['total'] += $item->amount;
        }
        return $fields;
    }

    private function wishListFields($user_id)
    {
        $items = Wishlist::where('user_id', $user_id)->get();
        $fields = ['items' => []];
        foreach ($items as $item) {

            $product = Product::where('id', $item->product_id)->first();
            $subcategory = SubCategoryModel::where('id', $product->subcategory_id)->first();
            $fields['items'][] = [
                'item_id' => $item->id,
                'subcategory' => $subcategory->name,
                'subcategory_type' => $subcategory->subcat_type,
                'product_id' => $item->product_id,
                'product_name' => $product->name,
                'product_price' => $product->price,
                'quantity' => $product->quantity,
                'code' => $product->code,
                'image' => !empty($product->image) ? Storage::url($product->image) : NULL,
                'description' => $product->description,
                'weights' => !empty($product->weights()->pluck('weight')->toArray()) ? $product->weights()->pluck('weight')->toArray() : NULL,
                // 'types' => (!empty($prices)) ? $prices : [],
                'flavours' => !empty($product->flavours()->get()) ? $product->flavours()->get(['flavours.id', 'flavours.name', 'flavours.rate', 'is_default'])->toArray() : NULL,
                'default' => !empty($product->flavours()->where('is_default', 1)->first()) ? $product->flavours()->where('is_default', 1)->first(['flavours.id', 'flavours.name', 'flavours.rate'])->toArray() : NULL,
            ];

        }
        return $fields;
    }

    private function orderDetailFields($order)
    {
        $fields = [
            'order_id' => $order->id,
            'payment_method' => $order->payment_method,
            'order_no' => $order->order_no,
            'status' => $order->status,
            'delivery_date' => \Carbon\Carbon::parse($order->delivery_date)->format('d-m-Y'),
            'delivery_time' => \Carbon\Carbon::parse($order->time->startingtime)->format('h:i A') . ' to ' . \Carbon\Carbon::parse($order->time->endingtime)->format('h:i A'),
            'type' => $order->type,
            'note' => $order->note,
            'total_amount' => $order->total_amount
        ];

        $fields['items'] = [];
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
                $subcat_type = SubCategoryModel::where('id', $item->product->subcategory_id)->value('subcat_type');
                $fields['items'][] = [
                    'product_name' => $item->product->name,
                    'flavour' => !empty($item->flavour_id) ? $item->flavour->name : NULL,
                    'image' => Storage::url($item->product->image),
                    'amount' => $item->amount,
                    'subcat_type' => $subcat_type,
                    'weight' => !empty($item->weight) ? $item->weight : NULL,
                    'qty' => !empty($item->qty) ? $item->qty : NULL,
                    'voice_msg' => !empty($item->voice_msg) ? url('public/voice/' . $item->voice_msg) : NULL,
                    'cake_image' => (!empty($item->image)) ? url('public/orders/' . $item->image) : NULL,
                    'completed_image' => (!empty($item->completed_image)) ? Storage::url($item->completed_image) : NULL,
                    'message_on_cake' => $item->message_on_cake,
                    'instruction' => $item->instruction,
                    'is_photo' => $photo_rate,
                    'typeRate' => $priceRateName,
                    'size' => $item->size,
                    'note' => $item->note,
                    'delivery_date' => $item->delivery_date,
                    'customer_no' => !empty($item->customer_no) ? $item->customer_no : NULL,
                    'customer_name' => !empty($item->customer_name) ? $item->customer_name : NULL,
                ];
            }
        } else {
            $item = $order->customitem;
            $subcategory_name = SubCategoryModel::where('id', $item->sub_category_id)->value('name');
            $flavour_name = Flavour::where('id', $item->flavour_id)->first();
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
            $fields['items'][] = [
                'theme' => $item->theme,
                'item_id' => $item->id,
                'subcategory_id' => $item->subcategory_id,
                'subcategory' => $subcategory_name,
                'flavour_id' => $item->flavour_id,
                'flavour' => $flavour_name->name,
                'flavour_price' => $flavour_name->rate,
                'amount' => $item->amount,
                'weight' => $item->weight,
                'edit' => $item->edit,
                'reject_instruction' => $item->reject_instruction,
                'message_on_cake' => $item->message_on_cake,
                'instruction' => $item->instruction,
                'completed_image' => (!empty($item->completed_image)) ? Storage::url($item->completed_image) : NULL,
                'typeRate' => $priceRateName,
                'is_photo' => $photo_rate,
                'delivery_date' => \Carbon\Carbon::parse($item->delivery_date)->format('d-m-Y'),
                'customer_no' => !empty($request->customer_no) ? $request->customer_no : NULL,
                'customer_name' => !empty($request->customer_name) ? $request->customer_name : NULL,
            ];
            $fields['idea_images'] = [];
            $fields['cake_images'] = [];
            foreach ($order->images()->where(['type' => 'idea'])->get() as $item) {
                $fields['idea_images'][] = url('public/' . $item->image);;
            }
            foreach ($order->images()->where(['type' => 'cake'])->get() as $item) {
                $fields['cake_images'][] = url('public/' . $item->image);;
            }
        }
        return $fields;
    }

    private function orderListFields($orders)
    {
        $fields = [];

        foreach ($orders as $order) {
            $data = [
                'order_id' => $order->id,
                'payment_method' => $order->payment_method,
                'order_no' => $order->order_no,
                'status' => $order->status,
                'delivery_date' => \Carbon\Carbon::parse($order->delivery_date)->format('d-m-Y'),
                'delivery_time' => \Carbon\Carbon::parse($order->time->startingtime)->format('h:i A') . ' to ' . \Carbon\Carbon::parse($order->time->endingtime)->format('h:i A'),
                'type' => $order->type,
                'note' => $order->note,
                'total_amount' => $order->total_amount
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
                    $subcat_type = SubCategoryModel::where('id', $item->product->subcategory_id)->value('subcat_type');
                    $data['items'][] = [
                        'product_name' => $item->product->name,
                        'product_type' => $priceRateName,
                        'flavour' => !empty($item->flavour_id) ? $item->flavour->name : NULL,
                        'image' => Storage::url($item->product->image),
                        'amount' => $item->amount,
                        'subcat_type' => $subcat_type,
                        'weight' => !empty($item->weight) ? $item->weight : NULL,
                        'qty' => !empty($item->qty) ? $item->qty : NULL,
                        'voice_msg' => !empty($item->voice_msg) ? url('public/voice/' . $item->voice_msg) : NULL,
                        'cake_image' => (!empty($item->image)) ? url('public/orders/' . $item->image) : NULL,
                        'completed_image' => (!empty($item->completed_image)) ? Storage::url($item->completed_image) : NULL,
                        'message_on_cake' => $item->message_on_cake,
                        'instruction' => $item->instruction,
                        'customer_no' => !empty($item->customer_no) ? $item->customer_no : NULL,
                        'customer_name' => !empty($item->customer_name) ? $item->customer_name : NULL,
                        'size' => $item->size,
                        'note' => $item->note,
                        'delivery_date' => \Carbon\Carbon::parse($item->delivery_date)->format('d-m-Y'),
                        'is_photo' => $photo_rate,
                        'typeRate' => $priceRateName,
                    ];
                }
            }
            else {
                $item = $order->customitem;
                $subcategory_name = SubCategoryModel::where('id', $item->sub_category_id)->value('name');
                $flavour_name = Flavour::where('id', $item->flavour_id)->first();
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
                    'theme' => $item->theme,
                    'item_id' => $item->id,
                    'subcategory_id' => $item->subcategory_id,
                    'subcategory' => $subcategory_name,
                    'flavour_id' => $item->flavour_id,
                    'flavour' => $flavour_name->name,
                    'flavour_price' => $flavour_name->rate,
                    'amount' => $item->amount,
                    'weight' => $item->weight,
                    'edit' => $item->edit,
                    'reject_instruction' => $item->reject_instruction,
                    'message_on_cake' => $item->message_on_cake,
                    'instruction' => $item->instruction,
                    'completed_image' => (!empty($item->completed_image)) ? Storage::url($item->completed_image) : NULL,
                    'typeRate' => $priceRateName,
                    'is_photo' => $photo_rate,
                    'delivery_date' => \Carbon\Carbon::parse($item->delivery_date)->format('d-m-Y'),
                    'customer_no' => !empty($item->customer_no) ? $item->customer_no : NULL,
                    'customer_name' => !empty($item->customer_name) ? $item->customer_name : NULL,
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
        return $fields;
    }


    public function login(Request $request)
    {
        $rules = [
            'mobile_no' => 'required',
            'password' => 'required',
        ];

        if ($this->ApiValidator($request->all(), $rules)) {
            if (Auth::attempt(['mobile_no' => $request->mobile_no, 'password' => $request->password])) {
                $user = Auth::user();

                if (!empty($request->device_id)) {
                    $user->push_token = $request->device_id;
                    $user->save();
                }

                $data['user'] = $this->userFields($user);
                $data['token'] = $user->createToken('dessertden')->accessToken;
                $this->response['message'] = trans('api.login');
                $this->status = 200;
                $this->response['data'] = $data;
            } else {
                $this->status = 412;
                $this->response['message'] = trans('api.unauthenticated');
            }
        }
        return $this->return_response();
    }


    public function register(Request $request)
    {
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'mobile_no' => 'required|unique:users,mobile_no',
            'email' => 'nullable|unique:users,email',
            'password' => 'required',
        ];

        if ($this->ApiValidator($request->all(), $rules)) {

            $check_email = User::where('mobile_no', $request->mobile_no)->first();

            if (!empty($check_email)) {
                $this->response['message'] = "Mobile No is already registered";
                $this->status = 412;
            } else {
                $user = new User;
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->email = $request->email;
                $user->mobile_no = $request->mobile_no;
                $user->type = 'normal';
                $user->password = Hash::make($request->password);
                $user->save();
                $data['user'] = $this->userFields($user);
                $this->response['message'] = trans('api.register');
                $this->status = 200;
                $this->response['data'] = $data;
            }
        }
        return $this->return_response();
    }

    public function checkMobile(Request $request)
    {
        $rules = [
            'mobile_no' => 'required',
        ];

        if ($this->ApiValidator($request->all(), $rules)) {
            $exists = User::where('mobile_no', $request->mobile_no)->first();
            if ($exists) {
                $this->response['message'] = "Mobile number found.";
                $this->status = 200;
            } else {
                $this->status = 200;
                $this->response['message'] = "Mobile number not found.";
            }
        }
        return $this->return_response();
    }

    public function socialLogin(Request $request)
    {
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:users,email',
            'token' => 'required',
            'type' => 'required|in:fb,google'
        ];

        $user = User::where(['email' => $request->email, 'type' => $request->type])->first();


        if (!empty($user)) {
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->mobile_no = $request->mobile_no;
            $user->token = $request->token;
            $user->type = $request->type;
            $user->push_token = $request->device_id;
            $user->save();

            $data['user'] = $this->userFields($user);
            $data['token'] = $user->createToken('dessertden')->accessToken;
            $this->response['message'] = trans('api.login');
            $this->status = 200;


        } else {

            $user = new User();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->mobile_no = $request->mobile_no;
            $user->email = $request->email;
            $user->token = $request->token;
            $user->type = $request->type;
            $user->password = Hash::make(str_random(8));
            if (!empty($request->device_id)) {
                $user->push_token = $request->device_id;
            }
            $user->save();
            $data['user'] = $this->userFields($user);
            $data['token'] = $user->createToken('dessertden')->accessToken;
            $this->response['message'] = trans('api.login');
            $this->status = 200;

        }


        $this->response['data'] = $data;
        return $this->return_response();
    }

    public function updateProfile(Request $request)
    {
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'mobile_no' => 'required|unique:users,mobile_no,' . Auth::user()->id,
            'email' => 'nullable|unique:users,email,' . Auth::user()->id,
        ];

        if ($this->ApiValidator($request->all(), $rules)) {
            $user = Auth::user();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->mobile_no = $request->mobile_no;
            $user->save();
            $data['user'] = $this->userFields($user);
            $this->response['message'] = trans('api.update', ['entity' => 'Profile']);
            $this->status = 200;
            $this->response['data'] = $data;
        }
        return $this->return_response();
    }

    public function getProfile(Request $request)
    {
        $data['user'] = $this->userFields(Auth::user());
        $this->response['message'] = trans('api.list', ['entity' => 'Profile']);
        $this->status = 200;
        $this->response['data'] = $data;
        return $this->return_response();
    }

    public function editProfileImage(Request $request)
    {
        $rules = [
            'profile' => 'required|image'
        ];

        if ($this->ApiValidator($request->all(), $rules)) {
            $user = Auth::user();
            Storage::delete($user->profile);
            $user->profile = $request->file('profile')->store('users');
            $user->save();
            $this->response['data']['user'] = $this->userFields($user);
            $this->response['status'] = true;
            $this->status = 200;
        }
        return $this->return_response();
    }

    public function addToCart(Request $request)
    {

        $rules = [
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'flavour_id' => 'nullable|exists:flavours,id',
            'amount' => 'required|numeric',
            'weight' => 'nullable|numeric',
            'message_on_cake' => 'nullable',
            'instruction' => 'nullable'
        ];

        if ($this->ApiValidator($request->all(), $rules)) {

            $product = Product::find($request->product_id);

            $subcategory = SubCategoryModel::where('id', $product->subcategory_id)->first();
            $cart_type = Category::where('id', $subcategory->category_id)->value('type');
            if ($cart_type === 'bakery') {
                $cart = 1;
            } else if ($cart_type === 'cake') {
                $cart = 0;
            } else {
                $cart = 2;
            }

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

                $amount = (($flavourRate + $total) * $request->weight) + $photoprice;

                if ($amount == $request->amount) {

                    if (!empty($request->image)) {

                        $image = $request->image;
                        $newname = date('Ymd') . $image->getClientOriginalName();
                        $image->move(public_path('orders'), $newname);
                    }

                    if (!empty($request->voice_msg)) {
                        $voicename = $request->voice_msg->getClientOriginalName();
                        $request->voice_msg->move(public_path('voice'), $voicename);

                    }
                    $item = [
                        'user_id' => $request->user_id,
                        'product_id' => $request->product_id,
                        'flavour_id' => $request->flavour_id,
                        'amount' => $request->amount,
                        'weight' => $request->weight,
                        'size' => (!empty($request->size)) ? $request->size : NULL,
                        'image' => (!empty($newname)) ? $newname : NULL,
                        'message_on_cake' => $request->message_on_cake,
                        'instruction' => $request->instruction,
                        'customer_no' => !empty($request->customer_no) ? $request->customer_no : NULL,
                        'customer_name' => !empty($request->customer_name) ? $request->customer_name : NULL,
                        'voice_msg' => !empty($voicename) ? $voicename : NULL,
                        'cart_type' => $cart,
                        'note' => $request->note,
                        'delivery_date' => $request->delivery_dates,
                        'is_photo' => $request->photoprice_id,
                        'type_rate' => !empty($priceRate) ? (json_encode($priceRate)) : NULL,

                    ];
                    // 	dd($item);
                    Cart::Create($item);
                    $this->response['data'] = $this->cartFields($request->user_id);
                    $this->status = 200;
                    $this->response['message'] = trans('api.list', ['entity' => 'Cart']);
                } else {
                    $this->status = 412;
                    $this->response['message'] = "Amount mismatched!";
                }
            } else {
                $amount = ($product->price) * ($request->qty);
                if ($amount == $request->amount) {

                    $item = [
                        'user_id' => $request->user_id,
                        'product_id' => $request->product_id,
                        'amount' => $request->amount,
                        'qty' => $request->qty,
                        'cart_type' => $cart,
                        'delivery_date' => $request->delivery_dates,
                    ];
                    Cart::Create($item);
                    $this->response['data'] = $this->cartFields($request->user_id);
                    $this->status = 200;
                    $this->response['message'] = trans('api.list', ['entity' => 'Cart']);
                } else {
                    $this->status = 412;
                    $this->response['message'] = "Amount mismatched!";
                }
            }
        }
        return $this->return_response();
    }


    public function cartList(Request $request)
    {
        $rules = [
            'user_id' => 'required|exists:users,id',
        ];

        if ($this->ApiValidator($request->all(), $rules)) {
            $this->status = 200;
            $this->response['data'] = $this->cartFields($request->user_id);
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
            $product_price = Product::where('id', $item->product_id)->value('price');
            // dd($product_price);
            $amount = $request->qty * $product_price;
            // dd($amount);
            if ($amount == $request->amount) {
                $item->qty = $request->qty;
                $item->amount = $request->amount;
                $item->save();
                $this->status = 200;
                $this->response['data'] = $this->cartFields($item->user_id);
                $this->response['message'] = trans('api.list', ['entity' => 'Cart']);
            } else {
                $this->response['message'] = "Amount mismatch";
                $this->status = 412;
            }
        }
        return $this->return_response();
    }

    public function cartUpdate(Request $request)
    {
        $rules = [
            'item_id' => 'required|exists:carts,id',
            'message_on_cake' => 'nullable',
            'instruction' => 'nullable',
            'deliver_date' => 'date_format:d-m-Y'
        ];

        if ($this->ApiValidator($request->all(), $rules)) {
            $item = Cart::find($request->item_id);
            $items = Cart::where('id', $request->item_id)->update([

                'message_on_cake' => $request->message_on_cake,
                'instruction' => $request->instruction,
                'note' => $request->note,
                'delivery_date' => $request->delivery_dates

            ]);


// 			$item->save();
            $this->status = 200;
            $this->response['data'] = $this->cartFields($item->user_id);
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
            $user_id = $item->user_id;
            $item->delete();

            $this->status = 200;
            $this->response['data'] = $this->cartFields($user_id);
            $this->response['message'] = trans('api.list', ['entity' => 'Cart']);

        }
        return $this->return_response();
    }


    public function wishLists(Request $request)
    {
        $rules = [
            'product_id' => 'required|exists:products,id',
            'user_id' => 'required|exists:users,id'
        ];
        if ($this->ApiValidator($request->all(), $rules)) {
            $wishlist = Wishlist::where(['product_id' => $request->product_id, 'user_id' => $request->user_id])->first();
            // dd($wishlist);
            if (!empty($wishlist)) {
                // dd('he');
                $this->response['message'] = 'Product is already in wishlist';
                $this->response['data'] = $this->wishListFields($wishlist->user_id);
                $this->status = 200;
            } else {
                // dd('he1');

                $item = [
                    'product_id' => $request->product_id,
                    'user_id' => $request->user_id,
                ];
                Wishlist::Create($item);
                $this->response['data'] = $this->wishListFields($request->user_id);
                $this->status = 200;
                $this->response['message'] = trans('api.list', ['entity' => 'Wishlist']);
            }
        }
        return $this->return_response();

    }

    public function wishlistDetail(Request $request)
    {
        $rules = [

            'user_id' => 'required|exists:users,id'
        ];
        if ($this->ApiValidator($request->all(), $rules)) {
            $this->response['message'] = 'Wishlist are listing';
            $this->response['data'] = $this->wishListFields($request->user_id);
            $this->status = 200;
        }
        return $this->return_response();

    }

    public function wishListRemove(Request $request)
    {
        $rules = [
            'item_id' => 'nullable',
            'user_id' => 'nullable',
        ];

        if ($this->ApiValidator($request->all(), $rules)) {
            if ($request->item_id) {
                $item = Wishlist::find($request->item_id);
                $user_id = $item->user_id;
                $item->delete();

            }
            if (!empty($request->user_id)) {
                $items = Wishlist::where(['user_id' => $request->user_id, 'product_id' => $request->product_id])->delete();
                $user_id = $request->user_id;

            }


            $this->status = 200;
            $this->response['data'] = $this->wishListFields($user_id);
            $this->response['message'] = trans('api.list', ['entity' => 'Wishlist']);

        }
        return $this->return_response();
    }

    public function placeOrder(Request $request)
    {
        $rules = [
            'payment_method' => 'required|in:cod,online,balance',
            'shipping_method' => 'required|in:homedelivery,pickup',
            'franchise_id' => 'required_if:shipping_method,pickup|exists:franchises,id',
            'address' => 'required_if:shipping_method,homedelivery',
            'city_id' => 'required_if:shipping_method,homedelivery|exists:cities,id',
            'zip' => 'required_if:shipping_method,homedelivery',
            'delivery_date' => 'required|date_format:d-m-Y',
            'delivery_time_id' => 'required|exists:times,id',

            'user_id' => 'required|exists:users,id',
            'razorpay_payment_id' => 'required_if:payment_method,online'
        ];


        if ($this->ApiValidator($request->all(), $rules)) {
            $ttl = Cart::where(['user_id' => $request->user_id])->get('amount');
            $t = 0;
            // dd($ttl);
            foreach ($ttl as $tt) {
                $t = +$tt->amount;
            }
            $ite = Cart::where('user_id', $request->user_id)->groupBy('cart_type')->pluck('cart_type')->toArray();
            // $items = Cart::where('user_id', $request->user_id)->get();

            if (!empty($request->razorpay_payment_id)) {
                $payment_status = $this->verifyRazorOrder($request->razorpay_payment_id, $request->total_amount);
                if ($payment_status['status'] == false) {
                    $this->status = 200;
                    $this->response['message'] = $payment_status['error_message'];
                    return $this->return_response();
                }
            }
            foreach ($ite as $i => $value) {
                $items = Cart::where(['user_id' => $request->user_id, 'cart_type' => $value])->get();
// dd($t);
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
                            'is_photo' => (!empty($item->is_photo)) ? $item->is_photo : NULL,
                            'type_rate' => (!empty($item->type_rate)) ? $item->type_rate : NULL,
                            'image' => (!empty($item->image)) ? $item->image : NULL,
                            'message_on_cake' => !empty($item->message_on_cake) ? $item->message_on_cake : NULL,
                            'qty' => !empty($item->qty) ? $item->qty : NULL,
                            'instruction' => !empty($item->instruction) ? $item->instruction : NULL,
                            'voice_msg' => !empty($item->voice_msg) ? $item->voice_msg : NULL,
                            'customer_no' => !empty($item->customer_no) ? $item->customer_no : NULL,
                            'customer_name' => !empty($item->customer_name) ? $item->customer_name : NULL,
                            'note' => !empty($item->note) ? $item->note : NULL,
                            'delivery_date' => $item->delivery_date,
                        ]);
                        $order_total += $item->amount;

                    }

                }
                if (!empty($request->promocode)) {
                    $code = Coupon::where('code', $request->promocode)->first();
                    if (!empty($code)) {
                        if ($code->type == 'percentage') {
                            $discount = (($request->total_amount) * ($code->value)) / 100;
                            $order_total = $request->total_amount - $discount;
                        } else {
                            $order_total = ($request->total_amount) - ($code->value);
                        }

                    } else {

                        $this->response['message'] = "Invalid Code";
                    }
                }


                // if (!empty($request->razorpay_payment_id)) {
                // 	$payment_status = $this->verifyRazorOrder($request->razorpay_payment_id, $request->total_amount);
                // 	if ($payment_status['status'] == false) {
                // 		$this->status = 200;
                // 		$this->response['message'] = $payment_status['error_message'];
                // 		return $this->return_response();
                // 	}
                // }
                $last_id = Order::latest()->value('id');
                $last_id = (!empty($last_id) ? $last_id + 1 : 1);
                $order = new Order;
                $order->user_id = $request->user_id;
                $order->order_no = date('Ymd') . "/" . time() . "/" . $last_id;
                $order->shipping_method = $request->shipping_method;
                $order->franchise_id = $request->franchise_id;
                if ($request->franchise_id) {
                    $assign_order = AssignOrder::create(['order_id' => $last_id,
                        'franchise_id' => $request->franchise_id,
                    ]);
                }
                $order->city_id = $request->city_id;
                $order->address = $request->address;
                $order->zip = $request->zip;
                $order->delivery_date = \Carbon\Carbon::parse($request->delivery_date)->format('Y-m-d');
                $order->time_id = $request->delivery_time_id;
                $order->p_type = $value;
                $order->status = 'place_order';
                if (!empty($request->promocode)) {
                    $order->coupon_code = $request->promocode;
                    $order->coupon_value = $code->value;
                    $order->coupon_type = $code->type;
                }
                $order->note = $request->note;
                $order->type = 'Normal';
                $order->total_amount = $order_total;
                $order->payment_method = $request->payment_method;
                $order->razorpay_payment_id = $request->razorpay_payment_id;
                $order->payment_data = !empty($payment_status) ? json_encode($payment_status['payment_response']) : NULL;
                $order->save();
                $order->items()->saveMany($order_items);

                if ($request->payment_method === 'balance') {
                    $balance = User::where('id', $request->user_id)->value('balance');
                    $balancetotal = floatval($balance) - floatval($order_total);
                    $debititem = [
                        'user_id' => $request->user_id,
                        'debit' => floatval($order_total),
                        'totalbalance' => floatval($balancetotal),
                    ];
                    User::where('id', $request->user_id)->update(['balance' => $balancetotal]);
                    UserBalance::create($debititem);
                }
                Cart::where(['user_id' => $request->user_id, 'cart_type' => $value])->delete();

                if (!empty(User::where('id', $request->user_id)->value('push_token'))) {
                    sendPushMessage(User::where('id', $request->user_id)->value('push_token'), "Order placed successfully");
                }
                $admin_token = Admin::where('type', 'Admin')->value('push_token');
                if (!empty($admin_token)) {
                    sendPushMessage($admin_token, "New order received");
                }

                $this->status = 200;
                $this->response['message'] = trans('api.orderlist', ['entity' => 'Order']);
            }

        } else {
            $this->status = 412;
            $this->response['message'] = "Something wrong";
        }

        return $this->return_response();
    }


    public function customPlaceOrder(Request $request)
    {
        $rules = [
            'shipping_method' => 'required|in:pickup,homedelivery',
            'franchise_id' => 'required_if:shipping_method,pickup|exists:franchises,id',
            'address' => 'required_if:shipping_method,homedelivery',
            'city_id' => 'required_if:shipping_method,homedelivery|exists:cities,id',
            'zip' => 'required_if:shipping_method,homedelivery',
            'delivery_date' => 'required|date_format:d-m-Y',
            'delivery_time_id' => 'required|exists:times,id',
            'device_id' => 'nullable',
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
            $flavourRate = Flavour::where('id', $request->flavour_id)->value('rate');
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

            $photoprice = (!empty($photoprice_id)) ? $photoprice_id : 0;
            $amount = (($flavourRate + $total) * $request->weight) + $photoprice;
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
                    'theme' => !empty($request->theme) ? $request->theme : NULL,
                    'message_on_cake' => !empty($request->message_on_cake) ? $request->message_on_cake : NULL,
                    'instruction' => !empty($request->instruction) ? $request->instruction : NULL,
                    'type_rate' => !empty($priceRate) ? (json_encode($priceRate)) : NULL,
                    'is_photo' => $request->photoprice_id,
                    'amount' => $amount,
                    'customer_name' => $request->customer_name,
                    'customer_no' => $request->customer_no,
                    'delivery_date' => $request->delivery_date,
                ]);

                $last_id = Order::latest()->value('id');
                $last_id = (!empty($last_id) ? $last_id + 1 : 1);
                $order = new Order;
                $order->user_id = $request->user_id;
                $order->order_no = date('Ymd') . "/" . time() . "/" . $last_id;
                $order->shipping_method = $request->shipping_method;
                $order->franchise_id = $request->franchise_id;
                $order->type = 'Custom';
                $order->city_id = $request->city_id;
                $order->address = $request->address;
                $order->zip = $request->zip;
                $order->delivery_date = \Carbon\Carbon::parse($request->delivery_date)->format('Y-m-d');
                $order->time_id = $request->delivery_time_id;
                $order->status = 'place_order';

                $order->note = $request->note;
                $order->total_amount = $amount;
                $order->save();
                $order->customitem()->save($order_items);
                if (!empty($order_images)) {
                    $order->images()->saveMany($order_images);
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

            if ($request->payment_method === 'cod') {
                $status = 'cod_selected';
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

            if (!empty(User::where('id', $request->user_id)->value('push_token'))) {
                sendPushMessage(User::where('id', $request->user_id)->value('push_token'), "Payment Done Successfully");
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

    public function orderList(Request $request)
    {
        $orders = Order::where('user_id', $request->user_id)->get();
        // dd($orders);
        $this->status = 200;
        $this->response['data'] = $this->orderListFields($orders);
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
            $orders = Order::find($request->order_id);
            $this->status = 200;
            $this->response['data'] = $this->orderDetailFields($orders);
            $this->response['message'] = trans('api.orderlist', ['entity' => 'Order Listing']);
        }
        return $this->return_response();
    }

    public function applyCoupon(Request $request)
    {
        $rules = [
            'code' => 'required|exists:coupons,code',
            'total_amount' => 'required'
        ];

        if ($this->ApiValidator($request->all(), $rules)) {
            $code = Coupon::where('code', $request->code)->first();

            if ($code->type == 'percentage') {
                $discount = (($request->total_amount) * ($code->value)) / 100;
                $total = $request->total_amount - $discount;
                $this->status = 200;
                $this->response['data'] = ['discount' => $discount, 'discount_total' => $total];
                $this->response['message'] = "Coupon Apply Successfully";
            } else {
                $total = ($request->total_amount) - ($code->value);
                $this->response['data'] = ['discount' => $code->value, 'discount_total' => $total];
                $this->status = 200;
                $this->response['message'] = "Coupon Apply Successfully";
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

    public function editCustomorder(Request $request)
    {
// dd($request->status);
        $rules = [
            'shipping_method' => 'required|in:pickup,homedelivery',
            'franchise_id' => 'required_if:shipping_method,pickup|exists:franchises,id',
            'address' => 'required_if:shipping_method,homedelivery',
            'city_id' => 'required_if:shipping_method,homedelivery|exists:cities,id',
            'zip' => 'required_if:shipping_method,homedelivery',
            'delivery_date' => 'required|date_format:d-m-Y',
            'delivery_time_id' => 'required|exists:times,id',
            'category_id' => 'required|exists:categories,id',
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
                    'category_id' => $request->category_id,
                    'flavour_id' => $request->flavour_id,
                    'weight' => $request->weight,
                    'theme' => $request->theme,
                    'edit' => '0',
                    'message_on_cake' => $request->message_on_cake,
                    'instruction' => $request->instruction,
                    // 	'delivery_date' => $request->delivery_date,

                ]);

                // $orders->images()->saveMany($order_images);

                if (!empty(Auth::user()->push_token)) {
                    sendPushMessage(Auth::user()->push_token, "Order Edit successfully");
                }
                $admin_token = Admin::where('type', 'Admin')->value('push_token');
                if (!empty($admin_token)) {
                    sendPushMessage($admin_token, "Rejected Order Edited");
                }

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

    public function addVallet(Request $request)
    {
        $rules = [
            'user_id' => 'required|exists:user,id',
            'razorpay_payment_id' => 'required',
            'amount' => 'required',

        ];
        if ($this->ApiValidator($request->all(), $rules)) {
            // dd($request->all());
            if (!empty($request->razorpay_payment_id)) {
                $payment_status = $this->verifyRazorOrder($request->razorpay_payment_id, $request->amount);
                if ($payment_status['status'] == false) {
                    $this->response['message'] = $payment_status['error_message'];
                    return $this->return_response();
                }
            }
            $balance = User::where('id', $request->user_id)->value('balance');
            $balancetotal = $balance + $request->amount;
            // dd('balance total',$balancetotal);
            $credit = [
                'user_id' => $request->user_id,
                'credit' => $request->amount,
                'totalbalance' => $balancetotal,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'payment_data' => !empty($payment_status) ? json_encode($payment_status['payment_response']) : NULL,
            ];

            $b = User::where('id', $request->user_id)->update(['balance' => $balancetotal]);
            // dd($b);
            UserBalance::create($credit);

            $this->status = 200;
            $this->response['message'] = "Balance Credited";
        }
        return $this->return_response();

    }

}

