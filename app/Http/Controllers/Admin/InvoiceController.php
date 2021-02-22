<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use App\Franchise;
use App\Http\Controllers\Controller;
use App\Models\CustomOrder;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;

class InvoiceController extends Controller
{
    public function index()
    {
        return view('admin.pages.invoices.index');
    }

    public function create()
    {
        $franchises = Franchise::all();
        $users = User::all();
        $delivery_boys = Admin::where('type', 'Deliveryboy')->get();
        $orders = '';
        return view('admin.pages.invoices.create', compact('users', 'franchises', 'orders', 'delivery_boys'));
    }

    public function store(Request $request)
    {
        $invoice_series = App\Models\Invoice::latest()->value('i_id');
        $last_id = (!empty($invoice_series) ? $invoice_series + 1 : 1);
        foreach ($request->item_id as $order_id => $value) {

            foreach ($value as $item_id) {
                $invoice = new Invoice;
                $invoice->invoice_no = $request->invoice_no;
                $invoice->i_id = $last_id;
                $invoice->invoice_date = $request->invoice_date;
                $invoice->deliveryboy_id = $request->deliveryboy_id;
                if (!empty($request->hsn)) {
                    $invoice->hsn = $request->hsn[$item_id];
                }
                if (!empty($request->discount)) {
                    $invoice->discount = $request->discount[$item_id];
                }
                if (!empty($request->gst)) {

                    $invoice->gst = $request->gst[$item_id];
                }
                if (!empty($request->cgst)) {
                    $invoice->cgst = $request->cgst[$item_id];
                }
                if (!empty($request->sgst)) {
                    $invoice->sgst = $request->sgst[$item_id];
                }

                $invoice->item_id = $item_id;
                $invoice->order_id = $order_id;
                $invoice->deliveryboy_id = $request->deliveryboy_id;
                if (!empty($request->user_id)) {
                    $invoice->user_id = $request->user_id;
                } else {
                    $invoice->franchise_id = $request->franchise_id;
                }
                $invoice->admin_id = Auth::guard('admin')->user()->id;
                $invoice->save();
                if (Order::where('id', $order_id)->value('type') === "Normal") {
                    OrderItem::where('id', $item_id)->update(['is_invoice' => 1]);
                    $order_item = OrderItem::where('order_id', $order_id)->where('is_invoice', 0)->count('id');
//                    dd($order_item);
                    if ($order_item == 0) {
                        $orders_ = Order::where('id', $order_id)->update(['is_invoice' => 1]);

                    }


                } else if (Order::where('id', $order_id)->value('type') === "Custom") {
                    CustomOrder::where('id', $item_id)->update(['is_invoice' => 1]);
                    Order::where('id', $order_id)->update(['is_invoice' => 1]);
                }
            }
        }


        flash('Invoice created successfully.')->success();
        return redirect()->route('admin.invoices.index');
    }

    public function getOrder(Request $request)
    {
        if (!empty($request->user_id)) {
            $orders = Order::where(['user_id' => $request->user_id, 'is_invoice' => 0, 'delivery_date' => $request->invdate])->orderBy('id', 'Desc')->get();
        }

        if (!empty($request->franchise_id)) {
            $orders = Order::where(['franchises_id' => $request->franchise_id, 'is_invoice' => 0, 'delivery_date' => $request->invdate])->orderBy('id', 'Desc')->get();
        }
        $franchises = Franchise::all();
        $users = User::all();

        $delivery_boys = Admin::where('type', 'Deliveryboy')->get();
        return view('admin.pages.invoices.add', compact('users', 'franchises', 'orders', 'delivery_boys'));

    }

    public function show($invoice)
    {
        $invoices = Invoice::where('invoice_no', $invoice)->get();
//        dd($invoices);
        return view('admin.pages.invoices.view', compact('invoices', 'invoice'));
    }

    public function edit(Order $order)
    {

        abort(404);
    }

    public function print(Request $request)
    {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 200);
        $invoice = $request->invoice_no;
        $invoices = Invoice::where('id', $invoice)->get();
dd($invoices);
        $pdf = PDF::loadView('admin.ddinvoice', compact('invoice', 'invoices'))->setPaper('a5', 'landscape');

        return $pdf->stream();
//        return $pdf->download('invoice.pdf');

    }

    public function update(Request $request, Order $order)
    {
        abort(404);

    }

    public function destroy(Request $request, $id)
    {
        if (!empty($request->action) && $request->action == 'delete_all') {
            $content = ['status' => 204, 'message' => "something went wrong"];
            Invoice::destroy(explode(',', $request->ids));
            $content['status'] = 200;
            $content['message'] = "Order deleted successfully.";
            return response()->json($content);
        } else {
            Invoice::destroy($id);
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
        $invoices = Invoice::where('id', '<>', 0)->groupBy('invoice_no')->get('invoice_no');
//        dd($invoices);

        if ($search != '') {
            $invoices->where(function ($query) use ($search) {
                $query->where("invoice_no", "like", "%{$search}%");
            });
        }
        $count = $invoices->count();

        $records["recordsTotal"] = $count;
        $records["recordsFiltered"] = $count;
        $records['data'] = array();

//        $invoices = $invoices->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order)->get();

        foreach ($invoices as $invoice) {
            $params = array(
                'url' => route('admin.invoices.update', $invoice->invoice_no),
                'checked' => ($invoice->active == 0) ? "checked" : "",
                'getaction' => '',
                'class' => '',
                'id' => $invoice->invoice_no
            );
            $records['data'][] = [
                'checkbox' => view('admin.shared.checkbox')->with('id', $invoice->invoice_no)->render(),
                'invoice_no' => $invoice->invoice_no,
                'active' => view('admin.shared.switch')->with(['params' => $params])->render(),
                'action' => view('admin.shared.actions')->with('id', $invoice->invoice_no)->render(),
            ];
        }
        return $records;
    }
}
