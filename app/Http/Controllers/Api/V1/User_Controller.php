<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Models\Cake;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\CustomOrder;
use App\Models\Order;
use App\Models\OrderImage;
use App\Models\OrderItem;
use App\User;
use App\Admin;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Razorpay\Api\Api;

class UserController extends Controller
{

  private function cartFields($device_id) {

    $items = Cart::where('device_id', $device_id)->get();

    $fields = ['total' => 0, 'items' => []];

    foreach ($items as $item) {

      $fields['items'][] = [

        'item_id' => $item->id,

        'cake_name'=>$item->cake->name,

        'flavour'=>$item->flavour->name,

        'image'=>Storage::url($item->cake->image),

        'amount'=>$item->amount,

        'weight'=>$item->weight,

        'message_on_cake'=>$item->message_on_cake,

        'instruction'=>$item->instruction   

      ];

      $fields['total'] += $item->amount;

    }

    return $fields;

  }



  private function orderListFields($orders) {

    $fields = [];

    foreach ($orders as $order) {

      $data = [

        'order_id' => $order->id,

        'order_no' => $order->order_no,

        'status'=>$order->status,

        'delivery_date' =>$order->delivery_date,

        'delivery_time'=> \Carbon\Carbon::parse($order->time->startingtime)->format('h:i A') .   ' to '.   \Carbon\Carbon::parse($order->time->endingtime)->format('h:i A'),

        'type' =>$order->type,

        'total_amount'=>$order->total_amount

      ];

      $data['items'] = [];

      if($order->type == 'Normal'){

        foreach ($order->items as $item) {

          $data['items'][] = [

            'cake_name'=>$item->cake->name,

            'flavour'=>$item->flavour->name,

            'image'=>Storage::url($item->cake->image),

            'amount'=>$item->amount,

            'weight'=>$item->weight,

            'message_on_cake'=>$item->message_on_cake,

            'instruction'=>$item->instruction   

          ];

        }

      }else{

        $item = $order->customitem;

        $data['items'][] = [

          'theme'=>$item->theme,

          'category'=>$item->category->name,

          'flavour'=>$item->flavour->name,

          'amount'=>$item->amount,

          'weight'=>$item->weight,

          'message_on_cake'=>$item->message_on_cake,

          'instruction'=>$item->instruction   

        ];

        $data['idea_images']=[];

        $data['cake_images']=[];



        foreach ($order->images()->where(['type' => 'idea'])->get() as $item) {

          $data['idea_images'][]= Storage::url($item->image);

        }

        foreach ($order->images()->where(['type' => 'cake'])->get() as $item) {

          $data['cake_images'][]= Storage::url($item->image);

        }

      }

      $fields[] = $data;

    }

    return $fields;

  }



  private function orderDetailFields($order) {

    $fields = [

      'order_id' => $order->id,

      'order_no' => $order->order_no,

      'status'=>$order->status,

      'delivery_date' =>$order->delivery_date,

      'delivery_time'=> \Carbon\Carbon::parse($order->time->startingtime)->format('h:i A') .   ' to '.   \Carbon\Carbon::parse($order->time->endingtime)->format('h:i A'),

      'type' =>$order->type,

      'total_amount'=>$order->total_amount

    ];



    $fields['items'] = [];

    if($order->type == 'Normal'){

      foreach ($order->items as $item) {

        $fields['items'][] = [

          'cake_name'=>$item->cake->name,

          'flavour'=>$item->flavour->name,

          'image'=>Storage::url($item->cake->image),

          'amount'=>$item->amount,

          'weight'=>$item->weight,

          'message_on_cake'=>$item->message_on_cake,

          'instruction'=>$item->instruction   

        ];

      }

    }else{

      $item = $order->customitem;

      $fields['items'][] = [

        'theme'=>$item->theme,

        'category'=>$item->category->name,

        'flavour'=>$item->flavour->name,

        'amount'=>$item->amount,

        'weight'=>$item->weight,

        'message_on_cake'=>$item->message_on_cake,

        'instruction'=>$item->instruction   

      ];

      $fields['images']=[];

      $fields['photo_images']=[];



      foreach ($order->images as $item) {

        $fields['images'][]=[

          'image'=> Storage::url($item->image),

        ];



      }

    }

    return $fields;

  }





  private function userFields($user) {

    return [

      'id'=>$user->id,  

      'first_name'=>$user->first_name,

      'last_name'=>$user->last_name,

      'email'=>$user->email,

      'mobile_no'=>$user->mobile_no,

      'profile'=>(!empty($user->profile) ? Storage::url($user->profile) : asset('theme/images/default_profile.jpg')),

    ];

  }



  public function addToCart(Request $request) {

    $rules = [

      'device_id' => 'required',

      'cake_id' => 'required|exists:cakes,id',

      'flavour_id' => 'required|exists:flavours,id',

      'amount'=>'required|numeric',

      'weight'=>'required|numeric',

      'message_on_cake'=>'required',

      'instruction'=>'required'    		

    ];



    if ($this->ApiValidator($request->all(), $rules)) {

      $cake = Cake::find($request->cake_id);

      $flavourRate = $cake->flavours()->where('flavours.id',$request->flavour_id)->value('rate');

      if(!empty($flavourRate)) {

        $amount = $flavourRate * $request->weight;



        if($amount == $request->amount){

          $item = [

            'device_id' => $request->device_id,

            'cake_id' => $request->cake_id,

            'flavour_id' => $request->flavour_id,

            'amount'=>$request->amount,

            'weight'=>$request->weight,

            'message_on_cake'=>$request->message_on_cake,

            'instruction'=>$request->instruction 

          ];

          Cart::Create($item);

          $this->status = 200;

          $this->response['data'] = $this->cartFields($request->device_id);

          $this->response['message'] = trans('api.list', ['entity' => 'Cart']);

        } else {

          $this->response['message'] = "Amount mismatched!";      

        } 

      } else {

        $this->response['message'] = "Flavour not available for the cake!";      

      }

    }

    return $this->return_response();

  }



  public function cartList(Request $request){

    $rules = [

      'device_id' => 'required',

    ];



    if ($this->ApiValidator($request->all(), $rules)) {



      $this->status = 200;

      $this->response['data'] = $this->cartFields($request->device_id);

      $this->response['message'] = trans('api.list', ['entity' => 'Cart']);



    }     

    return $this->return_response();

  }



  public function cartRemove(Request $request){

    $rules = [

      'item_id' => 'required|exists:carts,id',

    ];



    if ($this->ApiValidator($request->all(), $rules)) {

      $item = Cart::find($request->item_id);

      $device_id = $item->device_id;

      $item->delete();



      $this->status = 200;

      $this->response['data'] = $this->cartFields($device_id);

      $this->response['message'] = trans('api.list', ['entity' => 'Cart']);



    }     

    return $this->return_response();

  }



  public function cartUpdate(Request $request){

    $rules = [

      'item_id' => 'required|exists:carts,id',

      'message_on_cake'=>'required',

      'instruction'=>'required'   

    ];



    if ($this->ApiValidator($request->all(), $rules)) {

      $item = Cart::find($request->item_id);

      $item->message_on_cake= $request->message_on_cake;

      $item->instruction=$request->instruction;

      $item->save();

      $this->status = 200;

      $this->response['data'] = $this->cartFields($item->device_id);

      $this->response['message'] = trans('api.list', ['entity' => 'Cart']);

    }     

    return $this->return_response();

  }



  public function login(Request $request){

    $rules = [

      'mobile_no' => 'required|exists:users,mobile_no',

      'password'=>'required',   

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

        $this->status = 401;

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

      'email' => 'nullable',

      'password'=>'required',

    ];



    if ($this->ApiValidator($request->all(), $rules)) {

      $user = new User;

      $user->first_name = $request->first_name;

      $user->last_name = $request->last_name;

      $user->email = $request->email;

      $user->mobile_no = $request->mobile_no;

      $user->password = Hash::make($request->password);

      $user->save();

      $data['user'] = $this->userFields($user);

      $this->response['message'] = trans('api.register');

      $this->status = 200;

      $this->response['data'] = $data;

    }

    return $this->return_response();

  }



  public function checkMobile(Request $request) {

    $rules = [

      'mobile_no' => 'required', 

    ];



    if ($this->ApiValidator($request->all(), $rules)) {

      $exists = User::where('mobile_no', $request->mobile_no)->first();

      if ($exists) {

        $this->response['message'] = "Mobile number found.";

        $this->status = 200;

      } else {

        $this->response['message'] = "Mobile number not found.";  

      }

    }     

    return $this->return_response();

  }



  public function socialLogin(Request $request) {

    $rules = [

      'first_name' => 'required', 

      'last_name' => 'required', 

      'email' => 'required', 

      'token' => 'required',

      'type' => 'required|in:fb,google'

    ];



    $user = User::where('email', $request->email)->first();

    if (!empty($user)) {

      $postArray = $request->only(['first_name', 'last_name', 'token', 'type']);

      $user->update($postArray);

    } else {

      $postArray = $request->only(['first_name', 'last_name', 'token', 'type', 'email']);

      $postArray['password'] = Hash::make(str_random(8));

      $user = User::create($postArray);

    }



    if (!empty($request->device_id)) {

      $user->push_token = $request->device_id;

      $user->save();

    }



    $data['user'] = $this->userFields($user);

    $data['token'] = $user->createToken('dessertden')->accessToken;

    $this->response['message'] = trans('api.login');

    $this->status = 200;

    $this->response['data'] = $data;

    return $this->return_response();

  }



  public function placeOrder(Request $request){

    $rules = [

      'payment_method' => 'required|in:cod,online',

      'shipping_method' => 'required|in:pickup,homedelivery',

      'franchise_id'=>'required_if:shipping_method,pickup|exists:franchises,id',

      'address'=>   'required_if:shipping_method,homedelivery',

      'city_id'=>   'required_if:shipping_method,homedelivery|exists:cities,id',

      'zip'=>   'required_if:shipping_method,homedelivery',

      'delivery_date'=>   'required|date_format:d-m-Y',

      'delivery_time_id'=>   'required|exists:times,id',

      'device_id'=> 'required',

      'razorpay_payment_id'=> 'required_if:payment_method,online'

    ];



    if ($this->ApiValidator($request->all(), $rules)) {

      $items = Cart::where('device_id', $request->device_id)->get();



      if (!empty($items)) {

        $order_items = [];

        $order_total = 0;

        foreach ($items as $item) {

          $order_items[] = new OrderItem([

            'cake_id' => $item->cake_id,

            'flavour_id' => $item->flavour_id,

            'amount' => $item->amount,

            'weight' => $item->weight,

            'message_on_cake' => $item->message_on_cake,

            'instruction' => $item->instruction,

          ]);

          $order_total += $item->amount;

        }



        if (!empty($request->razorpay_payment_id)) {

          $payment_status = $this->verifyRazorOrder($request->razorpay_payment_id, $order_total);

          if ($payment_status['status'] == false) {

            $this->response['message'] = $payment_status['error_message'];

            return $this->return_response();

          }

        }



        $last_id = Order::latest()->value('id');

        $last_id = (!empty($last_id) ? $last_id+1 : 1);

        $order = new Order;

        $order->user_id= Auth::id();

        $order->order_no = date('Ymd') . "/" . time() . "/" . $last_id;

        $order->shipping_method=$request->shipping_method;

        $order->franchise_id=$request->franchise_id;

        $order->city_id=$request->city_id;

        $order->address=$request->address;

        $order->zip=$request->zip;

        $order->delivery_date=\Carbon\Carbon::parse($request->delivery_date)->format('Y-m-d');

        $order->time_id=$request->delivery_time_id;



        $order->status = 'place_order';

        $order->type = 'Normal';

        $order->total_amount = $order_total;

        $order->payment_method = $request->payment_method;

        $order->razorpay_payment_id = $request->razorpay_payment_id;

        $order->payment_data = !empty($payment_status) ? json_encode($payment_status['payment_response']) : NULL;

        $order->save();

        $order->items()->saveMany($order_items);

        Cart::where('device_id', $request->device_id)->delete();



        if (!empty(Auth::user()->push_token)) {

          sendPushMessage(Auth::user()->push_token, "Order placed successfully");

        }

        $admin_token = Admin::where('type', 'Admin')->value('push_token');

        if (!empty($admin_token)) {

          sendPushMessage($admin_token, "New order received");

        }



        $this->status = 200;

        $this->response['message'] = trans('api.orderlist' , ['entity' => 'Order']);

      }else {

        $this->response['message'] = trans('api.orderlist' , ['entity' => 'Order']);  

      }





    }

    return $this->return_response();

  }



  public function orderList(Request $request){    

    $orders=Order::where('user_id', Auth::id())->get();     

    $this->status = 200;

    $this->response['data'] = $this->orderListFields($orders);

    $this->response['message'] = trans('api.orderlist', ['entity' => 'Order Listing']);



    return $this->return_response();

  }



  public function orderDetail(Request $request){

    $rules =[

      'order_id' =>'required',

    ];



    if ($this->ApiValidator($request->all(), $rules)) {

      $id=$request->order_id;      

      $orders=Order::find($request->order_id); 

      $this->status = 200;

      $this->response['data'] = $this->orderDetailFields($orders);

      $this->response['message'] = trans('api.orderlist', ['entity' => 'Order Listing']);

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



  public function updateProfile(Request $request){

    $rules = [

      'first_name' => 'required',

      'last_name' => 'required',

      'mobile_no' => 'required|unique:users,mobile_no,'. Auth::user()->id,

      'email' => 'required|unique:users,email,'. Auth::user()->id,

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



  public function editProfileImage(Request $request) {

    $rules = [

      'profile' => 'required|image'

    ];



    if($this->ApiValidator($request->all(), $rules)) {

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



  public function customPlaceOrder(Request $request){

    $rules = [

      'shipping_method' => 'required|in:pickup,homedelivery',

      'franchise_id'=>'required_if:shipping_method,pickup|exists:franchises,id',

      'address'=>   'required_if:shipping_method,homedelivery',

      'city_id'=>   'required_if:shipping_method,homedelivery|exists:cities,id',

      'zip'=>   'required_if:shipping_method,homedelivery',

      'delivery_date'=>   'required|date_format:d-m-Y',

      'delivery_time_id'=>   'required|exists:times,id',

      'device_id'=>'required',

      'category_id'=>'required|exists:categories,id',

      'flavour_id'=>'required|exists:flavours,id',

      'weight' => 'required',

      'theme'=>'required',

      'message_on_cake' => 'required',

      'instruction' => 'required',

      'idea' => 'nullable|array',

      'idea.*' => 'image',

      'cake' => 'nullable|array',

      'cake.*' => 'image',

    ];



    if ($this->ApiValidator($request->all(), $rules)) {

      $order_images = [];



      foreach ($request->idea as $image) {

          $newname=$image->getClientOriginalName();

          $order_images[] = new OrderImage(['image' => $image->move(public_path('orders'), $newname),'type' => 'idea']);

      }



      foreach ($request->cake as $image) {

        $newname=$image->getClientOriginalName();

        $order_images[] = new OrderImage(['image' =>$image->move(public_path('orders'), $newname),'type' => 'cake']);

      }



      $order_items = new CustomOrder([

        'category_id'=>$request->category_id,

        'flavour_id'=>$request->flavour_id,

        'weight' => $request->weight,

        'theme'=>$request->theme,

        'message_on_cake' => $request->message_on_cake,

        'instruction' => $request->instruction,



      ]);



      $last_id = Order::latest()->value('id');

      $last_id = (!empty($last_id) ? $last_id+1 : 1);

      $order = new Order;

      $order->user_id= Auth::id();

      $order->order_no = date('Ymd') . "/" . time() . "/" . $last_id;

      $order->shipping_method=$request->shipping_method;

      $order->franchise_id=$request->franchise_id;

      $order->type='Custom';

      $order->city_id=$request->city_id;

      $order->address=$request->address;

      $order->zip=$request->zip;

      $order->delivery_date=\Carbon\Carbon::parse($request->delivery_date)->format('Y-m-d');

      $order->time_id=$request->delivery_time_id;

      $order->status = 'place_order';

      $order->total_amount = 0;





      $order->save();

      $order->customitem()->save($order_items);

      $order->images()->saveMany($order_images);

   

      $this->status = 200;

      $this->response['message'] = trans('api.orderlist' , ['entity' => 'Order']); 

    }

    return $this->return_response();

  }



  public function applyCoupon(Request $request){

    $rules=[

      'code'=> 'required|exists:coupons,code',

      'total_amount'=>'required'

    ];



    if ($this->ApiValidator($request->all(), $rules)){

      $code=Coupon::where('code',$request->code)->first();



      if($code->type == 'percentage'){

        $discount= (($request->total_amount)*($code->value))/100;

        $total=$request->total_amount-$discount;

         $this->status = 200;

        $this->response['data']=['discount' => $discount,'discount_total' => $total];

         $this->response['message'] ="Coupon Apply Successfully";

      }else{

        $total= ($request->total_amount)-($code->value);

        $this->response['data']=['discount' => $code->value,'discount_total' => $total];

        $this->status = 200;

        $this->response['message'] ="Coupon Apply Successfully";

      }

    }

      return $this->return_response();

  }



  public function verifyRazorOrder($payment_id, $amount) {

      $api = new Api(env('RAZORPAY_API_KEY', ''), env('RAZORPAY_API_SECRET',''));

      

      try {

          $captured_payment    =   $api->payment->fetch($payment_id)->capture(array('amount'=>($amount * 100)));

          // $payment    =   $api->payment->fetch($payment_id);

          $data = [];

          $data['payment_response'] = $captured_payment->toArray();

          if( $captured_payment->status == 'captured' ) {

              if( $captured_payment->amount == ($amount * 100) ) {

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

      }

      catch (\Exception $e){

          $data['status'] = false;

          $data['error_message'] = $e->getMessage();

          $data['payment_response'] = NULL;

          return $data;

      }

  }



  public function sendTestPush(Request $request)

  {

    $user = Admin::find(1);

    sendPushMessage($user->push_token, "Order placed successfully");

  }



}

