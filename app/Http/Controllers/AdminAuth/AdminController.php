<?php

namespace App\Http\Controllers\Api\V1;

use App\Admin;
use App\Http\Controllers\Controller;
use App\Models\AdminToken;
use App\Models\AssignOrder;
use App\Models\CustomOrder;
use App\Models\Order;
use App\Models\OrderImage;
use App\Models\OrderItem;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\random;
use PDF;
class AdminController extends Controller
{

  private function adminFields($admin) {
    return [
      'id'=>$admin->id,  
      'name'=>$admin->name,
      'email'=>$admin->email,
      'mobile_no'=>$admin->mobile_no,
      'type'=>$admin->type,
      'profile'=>(!empty($admin->profile) ? $admin->profile : asset('theme/images/default_profile.jpg')),
    ];
  }

  private function chefFields($chefs) {
    $fields = [];
    foreach ($chefs as $chef) {
      $fields[] =[
        'id'=>$chef->id,  
        'name'=>$chef->name,
        'email'=>$chef->email,
        'mobile_no'=>$chef->mobile_no,
        'type'=>$chef->type,
        'profile'=>(!empty($chef->profile) ? $chef->profile : asset('theme/images/default_profile.jpg')),
      ];
    }

    return $fields;
  } 
  private function deliveryBoyFields($deliveryBoys) {
    $fields = [];
    foreach ($deliveryBoys as $deliveryBoy) {
      $fields[] =[
        'id'=>$deliveryBoy->id,  
        'name'=>$deliveryBoy->name,
        'email'=>$deliveryBoy->email,
        'mobile_no'=>$deliveryBoy->mobile_no,
        'type'=>$deliveryBoy->type,
        'profile'=>(!empty($deliveryBoy->profile) ? $deliveryBoy->profile : asset('theme/images/default_profile.jpg')),
      ];
    }

    return $fields;
  } 

  private function assignListFields($assignListitem) {

    $fields = [];

    foreach ($assignListitem as $assignOrder) {
      $order = $assignOrder->order;
      $data = [
        'order_id' => $order->id,
        'order_no' => $order->order_no,
        'status'=>$order->status,
        'delivery_date' =>$order->delivery_date,
        'delivery_time'=> \Carbon\Carbon::parse($order->time->startingtime)->format('h:i A') .   ' to '.   \Carbon\Carbon::parse($order->time->endingtime)->format('h:i A'),
        'adminstatus'=>$order->admin_status,
        'type'=>$order->type,
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
          'category'=>$item->category->name,
          'flavour'=>$item->flavour->name,
          'amount'=>$item->amount,
          'weight'=>$item->weight,
          'theme'=>$item->theme,
          'message_on_cake'=>$item->message_on_cake,
          'instruction'=>$item->instruction   
        ];
        $data['idea_images']=[];
        $data['cake_images']=[];
        // dd($order->images);
        foreach ($order->images()->where(['type' => 'idea'])->get() as $item) {
         $data['idea_images'][]= Storage::url($item->image);
       }
       foreach ($order->images()->where(['type' => 'cake'])->get() as $item) {
         $data['cake_images'][]= Storage::url($item->image);
       }
     }

     $data['customer'] = $order->user()->first(['first_name', 'last_name', 'mobile_no', 'email']);

     $data['chef'] =[
      'name'=> $assignOrder->admin->name,
      'type'=>$assignOrder->admin->type
    ];

    if(!empty($assignOrder->delivery_boy_id)){
      $deliveryboy= Admin::where('id',$assignOrder->delivery_boy_id)->first();
      $data['Delivery Boy'] =[
        'name'=> $deliveryboy->name,
        'type'=>$deliveryboy->type
      ];
    }

    $data['instruction'] = $assignOrder->instruction;
    $fields[] = $data;
  }


  return $fields;
} 
private function chefOrderListFields($orders) {

  $fields = [];

  foreach ($orders as $order) {
    $data = [
      'order_id' => $order->id,
      'order_no' => $order->order_no,
      'status'=>$order->status,
      'adminstatus'=>$order->admin_status,
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
        'category'=>$item->category->name,
        'flavour'=>$item->flavour->name,
        'amount'=>$item->amount,
        'weight'=>$item->weight,
        'theme'=>$item->theme,
        'message_on_cake'=>$item->message_on_cake,
        'instruction'=>$item->instruction   
      ];
      $data['idea_images']=[];
      $data['cake_images']=[];
        // dd($order->images);
      foreach ($order->images()->where(['type' => 'idea'])->get() as $item) {
       $data['idea_images'][]= Storage::url($item->image);
     }
     foreach ($order->images()->where(['type' => 'cake'])->get() as $item) {
       $data['cake_images'][]= Storage::url($item->image);
     }
   }

   $data['instruction'] = AssignOrder::where('order_id',$order->id)->value('instruction');
   $fields[] = $data;
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
      'adminstatus'=>$order->admin_status,
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
        'category'=>$item->category->name,
        'flavour'=>$item->flavour->name,
        'amount'=>$item->amount,
        'weight'=>$item->weight,
        'theme'=>$item->theme,
        'message_on_cake'=>$item->message_on_cake,
        'instruction'=>$item->instruction   
      ];
      $data['idea_images']=[];
      $data['cake_images']=[];
        // dd($order->images);
      foreach ($order->images()->where(['type' => 'idea'])->get() as $item) {
       $data['idea_images'][]= Storage::url($item->image);
     }
     foreach ($order->images()->where(['type' => 'cake'])->get() as $item) {
       $data['cake_images'][]= Storage::url($item->image);
     }
   }
   $data['customer'] = $order->user()->first(['first_name', 'last_name', 'mobile_no', 'email']);
   $fields[] = $data;
 }


 return $fields;
}


public function login(Request $request){
  $rules = [
    'email' => 'required|exists:admins,email',
    'password'=>'required',   
  ];


  if ($this->ApiValidator($request->all(), $rules)) {
   $admin=Admin::where('email',$request->email)->first();
   if (!empty($admin)) {
     if (Hash::check($request->password, $admin->password)) {
      $data['admin'] = $this->adminFields($admin);
      $token = Str::random(80);
      AdminToken::updateOrCreate(['admin_id' => $admin->id], ['admin_id' => $admin->id, 'token' => $token]);
      if (!empty($request->device_id)) {
        $admin->push_token = $request->device_id;
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
  $rules =[
    'date'=>'nullable',
  ];

  if ($this->ApiValidator($request->all(), $rules)) {
    if (Auth::guard('admin')->user()->type == 'Admin') {

      if(!empty($request->date)){
        $orders = Order::where('delivery_date','like', '%' . $request->date . '%')->get();        
      }else if(!empty($request->month)){
        $orders = Order::where('delivery_date','like', '%' . $request->month . '%')->get();    
      }else if(!empty($request->year)){
        $orders = Order::where('delivery_date','like', '%' . $request->year . '%')->get();    
      }
      else{
        $orders = Order::get();
      }
      $this->response['data'] = $this->orderListFields($orders);
    } elseif (Auth::guard('admin')->user()->type == 'Chef') {
      $order_id=AssignOrder::value('order_id');  
      $orders = Order::where('id',$order_id)->get();
      // dd($orders);
      $this->response['data'] = $this->chefOrderListFields($orders);
    }

    $this->status = 200;
    $this->response['message'] = trans('api.orderlist', ['entity' => 'Order Listing']);
  }
  return $this->return_response();
}

public function chefList(Request $request)
{
  $chef= Admin::where('type','Chef')->get();
  $this->status = 200;
  $this->response['data'] = $this->chefFields($chef);
  $this->response['message'] = trans('api.list', ['entity' => 'Chef Listing']);
  return $this->return_response(); 
}

public function chefAssign(Request $request)
{

 $rules = [
  'order_id' => 'required|exists:orders,id',
  'admin_id'=>'required|exists:admins,id',
  'instruction' =>'required',   
];

if ($this->ApiValidator($request->all(), $rules)) {
  $exists= AssignOrder::where('order_id',$request->order_id)->count();
  if(!$exists){
    $assign_order= new AssignOrder;
    $assign_order->order_id = $request->order_id;

    $assign_order->admin_id = $request->admin_id;
    $assign_order->instruction = $request->instruction;
    $assign_order->save();
    $this->status = 200;
    $this->response['message'] = trans('api.assign', ['entity' => 'Order']);
  }else{
    $this->response['message'] = "Order already assigned.";
  }
}
return $this->return_response(); 
}

public function assignList(Request $request)
{

  if(Auth::guard('admin')->user()->type == 'Admin'){

    $assignList=AssignOrder::get();
    $this->status = 200;
    $this->response['data'] = $this->assignListFields($assignList);
    $this->response['message'] = trans('api.list', ['entity' => 'Assign Order Listing']);

  }else if(Auth::guard('admin')->user()->type == 'Chef'){
    $assignList=AssignOrder::where('admin_id',Auth::guard('admin')->user()->id)->get();
    $this->status = 200;
    $this->response['data'] = $this->assignListFields($assignList);
    $this->response['message'] = trans('api.list', ['entity' => 'Assign Order Listing']);   
  }else if(Auth::guard('admin')->user()->type == 'Deliveryboy'){
    $assignList=AssignOrder::where('admin_id',Auth::guard('admin')->user()->id)->get();
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
      'status'=>'required|in:confirmed,rejected,preparing,on_the_way,delivered',
      'admin_status'  =>'required|in:confirmed,rejected,preparing,on_the_way,delivered,ready_for_delivery',
    ];

    $order_status=Order::where('id',$request->order_id)
    ->update([
      'status'=> $request->status,
      'admin_status'=> $request->status,
      
    ]);
  } else if(Auth::guard('admin')->user()->type == 'Chef'){
    $rules = [
      'order_id' => 'required|exists:orders,id',
      'admin_status'=>'required|in:received,rejected,preparing,completed,on_the_way,delivered',  
    ];

    $order_status=Order::where('id',$request->order_id)
    ->update([
      'admin_status'=> $request->admin_status
    ]);
  } else if(Auth::guard('admin')->user()->type == 'Deliveryboy'){
    $rules = [
      'order_id' => 'required|exists:orders,id',
      'admin_status'=>'required|in:on_the_way,delivered',  
    ];

    $order_status=Order::where('id',$request->order_id)
    ->update([
      'admin_status'=> $request->admin_status
    ]);
  }

  $this->status = 200;
  $this->response['message'] = trans('api.change', ['entity' => 'Status']);
  return $this->return_response(); 
  
}

public function deliveryBoyList(Request $request)
{
  $deliveryBoy= Admin::where('type','Deliveryboy')->get();
  $this->status = 200;
  $this->response['data'] = $this->deliveryBoyFields($deliveryBoy);
  $this->response['message'] = trans('api.list', ['entity' => 'Delivery Boy Listing']);
  return $this->return_response(); 
}


public function deliveryBoyAssign(Request $request)
{

 $rules = [
  'order_id' => 'required|exists:assign_orders,order_id',    
  'delivery_boy_id'=> 'required|exists:admins,id',  
];

if ($this->ApiValidator($request->all(), $rules)) {

  $assign_order=AssignOrder::where('order_id',$request->order_id)
  ->update([
    'delivery_boy_id'=> $request->delivery_boy_id,
  ]);

  $this->status = 200;
  $this->response['message'] = trans('api.assign', ['entity' => 'Order']);

}
return $this->return_response(); 
}

public function generatePdf(Request $request)
{

  ini_set('memory_limit', -1);
  ini_set('max_execution_time', 200); 
  $rules = [
    'order_id' => 'required|exists:custom_orders,order_id',    
  ];
  if ($this->ApiValidator($request->all(), $rules)){

    $customeorder = CustomOrder::where('order_id', $request->order_id)->first();

    $staffname=AssignOrder::where('order_id', $request->order_id)->value('admin_id');
    
    $name=Admin::where('id',$staffname)->value('name');
    
    $customeorder_image =OrderImage::where(['order_id'=> $request->order_id,'type'=>'cake'])->value('image');
    $customeorder_imageidea =OrderImage::where(['order_id'=> $request->order_id,'type'=>'idea'])->value('image');
    
    // $image= Storage::url($customeorder_image);
    $image= $customeorder_image;
    // dd($image);
    
    $imageidea=$customeorder_imageidea;

// return view('admin.pdfgenerate', compact('customeorder','image','imageidea','name'));
    $pdf = PDF::loadView('admin.pdfgenerate', compact('customeorder','image','imageidea','name'));
    $filename=$request->order_id.'.pdf';
    $pdf->save('generatepdf/'.$filename);

    $customeorders = CustomOrder::where('order_id',$request->order_id)->update([
      'pdf' =>'generatepdf/'.$filename,
    ]);

    if($customeorders){
      $this->response['message'] = "Success";
    }else{
      $this->response['message'] = "er";
    }
    return $this->return_response();
  }
}

}
