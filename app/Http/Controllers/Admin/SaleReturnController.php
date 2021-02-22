<?php

namespace App\Http\Controllers\Admin;

use App\Franchise;
use App\Models\Banners;
use App\Models\CustomOrder;
use App\Models\Order;
use App\Models\OrderItem;
use App\SaleReturn;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SaleReturnController extends Controller
{
    public function index()
    {
        return view('admin.pages.salereturns.index');
    }
    public function create()
    { abort(404);}
    public function show(SaleReturn $salereturn)
    {
        return view('admin.pages.salereturns.view',compact('salereturn'));
    }
    public function edit(SaleReturn $salereturn)
    {
        $order=Order::where('id',$salereturn->order_id)->first();
        $order_item=null;
        $custom_order=null;
        if($order->type == "Normal"){
            $order_item=OrderItem::where('id',$salereturn->item_id)->first();
            $flavour=($order_item->flavour_id)?$order_item->flavour->name:null;
        }else{
            $custom_order=CustomOrder::where('id',$salereturn->item_id)->first();
            $flavour=($custom_order->flavour_id)?$custom_order->flavour->name:null;
        }
        $customer=Franchise::where('id',$salereturn->franchise_id)->value('name');
        return view('admin.pages.salereturns.edit',compact('salereturn','customer','order','order_item','custom_order','flavour'));
    }

    public function update(Request $request, SaleReturn $salereturn)
    {

        $salereturn->percentage = $request->percentage;
        $salereturn->reason_a = $request->reason_a;

        $salereturn->save();

            flash('Sale Return updated successfully.')->success();
            return redirect()->route('admin.salereturns.index');

    }
    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $salereturns = SaleReturn::where('id', '<>', 0)->orderBy('id', 'DESC');

        if ($search != '') {
            $salereturns->where(function ($query) use ($search) {
                $query->where("id", "like", "%{$search}%");
                $query->where("order_id", "like", "%{$search}%");

            });
        }
        $count = $salereturns->count();

        $records["recordsTotal"] = $count;
        $records["recordsFiltered"] = $count;
        $records['data'] = array();

        $salereturns = $salereturns->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order)->get();

        foreach ($salereturns as $salereturn) {
            $params = array(
                'url' => route('admin.salereturns.update', $salereturn->id),
                'checked' => ($salereturn->active == 0) ? "checked" : "",
                'getaction' => '',
                'class' => '',
                'id' => $salereturn->id
            );

        $order=Order::where('id',$salereturn->order_id)->first();
            if($order->type == "Normal"){
                $order_item=OrderItem::where('id',$salereturn->item_id)->first();
                $flavour=($order_item->flavour_id)?$order_item->flavour->name:null;
            }else{
                $custom_order=CustomOrder::where('id',$salereturn->item_id)->first();
                $flavour=($custom_order->flavour_id)?$custom_order->flavour->name:null;
            }
            $records['data'][] = [
                'checkbox' => view('admin.shared.checkbox')->with('id', $salereturn->id)->render(),
                'order_id' => Order::where('id',$salereturn->order_id)->value('order_no'),
                'item' => ($order_item)?$order_item->product->name.' | ' . $flavour : $custom_order->subcategory->name.'|'. $flavour ,
                'order_date' => Carbon::parse($order->delivery_date)->format('d-m-Y'),
                'date' => Carbon::parse($salereturn->date)->format('d-m-Y'),
                'customer' => Franchise::where('id',$salereturn->franchise_id)->value('name'),
                'status' => $salereturn->status,
                'reason_f' => $salereturn->reason_f,


                'active' => view('admin.shared.switch')->with(['params' => $params])->render(),
                'action' => view('admin.shared.actions')->with('id', $salereturn->id)->render(),
            ];
        }
        return $records;
    }
}
