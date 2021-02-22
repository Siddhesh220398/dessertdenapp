<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use App\Franchise;
use App\Http\Controllers\Controller;
use App\Models\AssignOrder;
use App\Models\City;
use App\Models\CustomOrder;
use App\Models\Flavour;
use App\Models\Order;
use App\Models\OrderImage;
use App\Models\OrderItem;
use App\Models\PriceCategoryModel;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PDF;

class OrderController extends Controller
{
    public function index()
    {
        return view('admin.pages.orders.index');
    }

    public function print(Request $request)
    {
        $logo = public_path('theme/images/logo.png');
        $order = Order::where('id', $request->order_id)->first();
        if ($order->type == "Normal") {
            $delivery_man_id = AssignOrder::where('order_id', $request->order_id)->value('delivery_boy_id');
            $delivery_man = Admin::where('id', $delivery_man_id)->value('name');
            $delivery_man = !empty($delivery_man) ? $delivery_man : '';
            $pdf = PDF::loadView('admin.invoice', compact('order', 'delivery_man', 'logo'))->setPaper('A5', 'landscape');
            return $pdf->stream();
        } else {
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
            $pdf = PDF::loadView('admin.pdfgenerate', compact('customeorder', 'image', 'imageidea', 'name'));
            return $pdf->stream();
        }

    }

    public function filter()
    {
        $users = User::all();
        $franchises = Franchise::all();
        $orders = Order::where('p_type', 0)->get();
        return view('admin.pages.orders.filter', compact('users', 'franchises', 'orders'));
    }

    public function search(Request $request)
    {
        $users = User::all();
        $franchises = Franchise::all();
        $order = Order::where('id', '<>', null);
//        dd($request->all());
        if (!empty($request->user_id)) {
            $order->where('user_id', $request->user_id);
        }
        if (!empty($request->franchises_id)) {
            $order->where('franchises_id', $request->franchises_id);
        }
        if (!empty($request->type)) {
            $order->where('type', $request->type);
        }
        if ($request->delivery_date) {
            $order->where('delivery_date', $request->delivery_date);
        }
        if (!empty($request->payment_method)) {
            $order->where('payment_method', $request->payment_method);
        }
        if (!empty($request->status)) {
            $order->where('status', $request->status);
        }
        if (!empty($request->p_type)) {
            if ($request->p_type == "Cake")
                $order->where('p_type', 0);
            else if ($request->p_type == "Bakery")
                $order->where('p_type', 1);
            else
                $order->where('p_type', 2);

        }


        if (!empty($request->tty)) {
            if ($request->tty == "today")
                $order->where('delivery_date', \Carbon\Carbon::now()->format('Y-m-d'));
            if ($request->tty == "tomorrow")
                $order->where('delivery_date', '>', \Carbon\Carbon::now()->format('Y-m-d'));
            if ($request->tty == "yesterday")
                $order->where('delivery_date', '<', \Carbon\Carbon::now()->format('Y-m-d'));
        }
        $orders = $order->get();
//        dd($orders);
        return view('admin.pages.orders.filter', compact('users', 'franchises', 'orders'));
    }

    public function store(Request $request)
    {
        abort(404);
    }

    public function show(Order $order)
    {
        $order_image = '';
        if ($order->type == "Normal") {
            $order_item = OrderItem::where('order_id', $order->id)->get();
        } else {
            $order_item = CustomOrder::where('order_id', $order->id)->first();
            $order_image = OrderImage::where('order_id', $order->id)->get();
        }

        return view('admin.pages.orders.view', compact('order', 'order_item', 'order_image'));
    }

    public function edit(Order $order)
    {

        $franchises = Franchise::all();
        $types = PriceCategoryModel::where('price_id', '<>', 2)->get();
        $photos = PriceCategoryModel::where('price_id', 2)->get();
        $users = User::all();
        $flavours = Flavour::all();
        $cities = City::where('active', 1)->get();
        return view('admin.pages.orders.edit', compact('order', 'photos', 'types', 'flavours', 'franchises', 'cities', 'users'));

    }

    public function update(Request $request, Order $order)
    {
//        dd($request->all());
        $order->delivery_date = $request->delivery_date;
        $order->save();

        if ($order->type == "Normal") {

            if ($request->weight) {
                if ($request->isphoto) {
                    foreach ($request->isphoto as $isphoto => $value) {
                        $order_items = OrderItem::where('id', $isphoto)->update([
                            'is_photo' => $value,
                        ]);

                    }
                }
                if (!empty($request->type_rate)) {
                    foreach ($request->type_rate as $type_ids => $value) {
//                        dd($type_ids);
                        $total = 0;
                        $priceRate = [];
                        foreach ($value as $type_id) {
                            $typeRate = PriceCategoryModel::where('id', $type_id)->value('price');
                            array_push($priceRate, $type_id);
                            if (!empty($typeRate)) {
                                $total += $typeRate;
                            }
                        }
                        $order_itemss = OrderItem::where('id', $type_ids)->update([
                            'type_rate' => json_encode($priceRate),
                        ]);

                    }
                }

                foreach ($request->weight as $weight => $value) {
                    $order_items = OrderItem::where('id', $weight)->update(
                        ['weight' => $value,]
                    );

                }


                foreach ($order->items as $item) {
                    $flavourRate = Flavour::where('id', $item->flavour_id)->value('rate');
                    $priceRate = [];
                    $totals = 0;
                    if (!empty($item->type_rate)) {
                        $type_ids = json_decode($item->type_rate);
                        foreach ($type_ids as $type_id) {
                            $typeRate = PriceCategoryModel::where('id', $type_id)->value('price');
//                            array_push($priceRate, $type_id);
                            if (!empty($typeRate)) {
                                $totals += $typeRate;
                            }
                        }
                    }

                    if (!empty($item->is_photo)) {
                        $photoprice_id = PriceCategoryModel::where('id', $item->is_photo)->value('price');
                    }

                    $photoprice = (!empty($photoprice_id)) ? $photoprice_id : 0;
                    $amount = (($flavourRate + $totals) * $item->weight) + $photoprice;
//                    dd('amouny=> '.$amount. '($flavourRate + $totals)=>' .($flavourRate + $totals).'$photoprice=>'.$photoprice);
                    OrderItem::where('id', $item->id)->update(['amount' => 0]);

                    OrderItem::where('id', $item->id)->update(['amount' => $amount]);


                }
            } else {
                foreach ($request->qty as $qty => $value) {
                    $order_items = OrderItem::where('id', $qty)->first();

                    $realamount = $order_items->amount / $order_items->qty;
                    $order_items->amount = $value * $realamount;
                    $order_items->qty = $value;
                    $order_items->save();
                }
            }
            $order->total_amount = OrderItem::where('order_id', $order->id)->sum('amount');
            $order->save();
        } else {
            if ($request->weight) {
                if ($request->isphoto) {
                    foreach ($request->isphoto as $isphoto => $value) {
//                        dd($request->isphoto);
                        $order_items = CustomOrder::where('id', $isphoto)->update([
                            'is_photo' => $value,
                        ]);

                    }
                }
                if (!empty($request->type_rate)) {
                    foreach ($request->type_rate as $type_ids => $value) {
//                        dd($type_ids);
                        $total = 0;
                        $priceRate = [];
                        foreach ($value as $type_id) {
                            $typeRate = PriceCategoryModel::where('id', $type_id)->value('price');
                            array_push($priceRate, $type_id);
                            if (!empty($typeRate)) {
                                $total += $typeRate;
                            }
                        }
                        $order_itemss = CustomOrder::where('id', $type_ids)->update([
                            'type_rate' => json_encode($priceRate),
                        ]);

                    }
                }

                foreach ($request->weight as $weight => $value) {
                    $order_items = CustomOrder::where('id', $weight)->update(
                        ['weight' => $value,]
                    );

                }


                $item = $order->customitem;
                $flavourRate = Flavour::where('id', $item->flavour_id)->value('rate');
                $priceRate = [];
                $totals = 0;
                if (!empty($item->type_rate)) {
                    $type_ids = json_decode($item->type_rate);
                    foreach ($type_ids as $type_id) {
                        $typeRate = PriceCategoryModel::where('id', $type_id)->value('price');
//                            array_push($priceRate, $type_id);
                        if (!empty($typeRate)) {
                            $totals += $typeRate;
                        }
                    }
                }

                if (!empty($item->is_photo)) {
                    $photoprice_id = PriceCategoryModel::where('id', $item->is_photo)->value('price');
                }

                $photoprice = (!empty($photoprice_id)) ? $photoprice_id : 0;
                $amount = (($flavourRate + $totals) * $item->weight) + $photoprice;
//                    dd('amouny=> '.$amount. '($flavourRate + $totals)=>' .($flavourRate + $totals).'$photoprice=>'.$photoprice);
                CustomOrder::where('id', $item->id)->update(['amount' => 0]);

                CustomOrder::where('id', $item->id)->update(['amount' => $amount]);


            }

            $order->total_amount = CustomOrder::where('order_id', $order->id)->sum('amount');
            $order->save();
        }


        return view('admin.pages.orders.index');

    }

    public function destroy(Request $request, $id)
    {
        if (!empty($request->action) && $request->action == 'delete_all') {
            $content = ['status' => 204, 'message' => "something went wrong"];
            Order::destroy(explode(',', $request->ids));
            $content['status'] = 200;
            $content['message'] = "Order deleted successfully.";
            return response()->json($content);
        } else {
            Order::destroy($id);
            if (request()->ajax()) {
                $content = array('status' => 200, 'message' => "Order deleted successfully.");
                return response()->json($content);
            } else {
                flash('Order deleted successfully.')->success();
                return redirect()->route('admin.orders.index');
            }
        }
    }


    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $orders = Order::where('id', '<>', 0)->orderBy('id', 'DESC');

        if ($search != '') {
            $orders->where(function ($query) use ($search) {
                $query->where("id", "like", "%{$search}%");
                $query->where("order_no", "like", "%{$search}%");
                $query->where("type", "like", "%{$search}%");
            });
        }
        $count = $orders->count();

        $records["recordsTotal"] = $count;
        $records["recordsFiltered"] = $count;
        $records['data'] = array();

        $orders = $orders->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order)->get();

        foreach ($orders as $order) {
            $params = array(
                'url' => route('admin.orders.update', $order->id),
                'checked' => ($order->active == 0) ? "checked" : "",
                'getaction' => '',
                'class' => '',
                'id' => $order->id
            );

            if ($order->type == "Normal") {
                $photo_cake = OrderItem::where('order_id', $order->id)->where('is_photo', '<>', '')->count('id');
            } else {
                $photo_customcake = CustomOrder::where('order_id', $order->id)->where('is_photo', '<>', '')->count('id');
            }
            $records['data'][] = [
                'checkbox' => view('admin.shared.checkbox')->with('id', $order->id)->render(),
                'order_no' => $order->order_no,
                'customer' => !empty($order->user_id) ? User::where('id', $order->user_id)->value('first_name') . '  ' . User::where('id', $order->user_id)->value('last_name') : $order->franchises->name,
                'shipping_method' => $order->shipping_method,
                'photo_cake' => !empty($photo_cake) ? $photo_cake : 0,
                'city' => !empty($order->city_id) ? $order->city->name : '',
                'address' => $order->address,
                'zip' => $order->zip,
                'delivery_date' => Carbon::parse($order->delivery_date)->format('d-m-Y'),
                'type' => $order->type,
                'payment_method' => $order->payment_method,
                'status' => $order->status,


                'active' => view('admin.shared.switch')->with(['params' => $params])->render(),
                'action' => view('admin.shared.actions')->with('id', $order->id)->render(),
            ];
        }
        return $records;
    }
}
