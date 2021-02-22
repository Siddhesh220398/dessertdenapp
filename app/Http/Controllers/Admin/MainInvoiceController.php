<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use App\Franchise;
use App\Http\Controllers\Controller;
use App\Models\BalanceSheet;
use App\Models\CustomOrder;
use App\Models\FranchiseBalance;
use App\Models\Invoice;
use App\Models\MainInvoice;
use App\Models\MainInvoiceItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;

class MainInvoiceController extends Controller
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

    public function getOrder(Request $request)
    {
        if (!empty($request->user_id)) {
            $orders = Order::where(['user_id' => $request->user_id, 'is_invoice' => 0, 'delivery_date' => $request->invdate])->where('franchise_id', NULL)->orderBy('id', 'Desc')->get();
        }
        if (!empty($request->franchise_id)) {

            $order = Order::where(['franchises_id' => $request->franchise_id, 'is_invoice' => 0])->orWhere('franchise_id', $request->franchise_id)->orderBy('id', 'Desc')->get();
            $orders = $order->where('delivery_date', Carbon::parse($request->invdate)->format('Y-m-d'));
        }

        $franchises = Franchise::all();
        $users = User::all();

        $delivery_boys = Admin::where('type', 'Deliveryboy')->get();
        return view('admin.pages.invoices.add', compact('users', 'franchises', 'orders', 'delivery_boys'));

    }

    public function getOrderedit(Request $request)
    {
        $item = MainInvoiceItem::where('id', $request->id)->first();
        $invoice_id = $item->invoice_id;
        $order = Order::where('id', $item->order_id)->first();
        if ($order->type == "Normal") {
            $order_item = OrderItem::where('id', $item->item_id)->first();
            $order_item->is_invoice = 0;
            $order_item->invoice_no = '';
            $order_item->save();
            $orders = OrderItem::where('order_id', $item->order_id)->where('is_invoice', 1)->count('id');
            if ($orders) {
                $orders_ = Order::where('id', $item->order_id)->update(['is_invoice' => 0]);

            }
        } else {
            $order_item = CustomOrder::where('id', $item->item_id)->first();
            $order_item->is_invoice = 0;
            $order_item->invoice_no = '';
            $order_item->save();
            $orders_ = Order::where('id', $item->order_id)->update(['is_invoice' => 0]);
        }
        $item->delete();
        $items = MainInvoiceItem::where('invoice_id', $invoice_id)->count('id');
        if ($items = 0) {
            MainInvoice::where('id', $invoice_id)->delete();
            flash('Invoice Item delete successfully.')->success();
            return view('admin.pages.invoices.index');
        } else {
            $invoices = MainInvoice::where('id', $invoice_id)->first();
            $invoice = MainInvoice::where('id', $invoice_id)->first();
            $franchises = Franchise::all();
            $users = User::all();
            $delivery_boys = Admin::where('type', 'Deliveryboy')->get();
            flash('Invoice Item delete successfully.')->success();
            return response()->json(['status' => 'success']);

        }


    }

    public function store(Request $request)
    {

        $last_id = MainInvoice::latest()->value('id');
        $last_id = (!empty($last_id) ? $last_id + 1 : 1);
        $total_amount = 0;
        $invoice = MainInvoice::create(['invoice_no' => $request->invoice_no, 'invoice_date' => $request->invoice_date, 'deliveryboy_id' => $request->deliveryboy_id, 'cash_debit' => $request->cash_debit, 'user_id' => $request->user_id, 'franchise_id' => $request->franchise_id, 'admin_id' => Auth::guard('admin')->user()->id]);
        foreach ($request->item_id as $order_id => $value) {

            foreach ($value as $item_id) {

                if (Order::where('id', $order_id)->value('type') === "Normal") {
                    $amount = OrderItem::where('id', $item_id)->value('amount');
                    $total_amount = $total_amount + $amount;

                } else if (Order::where('id', $order_id)->value('type') === "Custom") {
                    $amount = Order::where('id', $order_id)->value('total_amount');
                    $total_amount = $total_amount + $amount;
                }
//                dd($total_amount);

                $invoice_item = new MainInvoiceItem;
                $invoice_item->invoice_id = $last_id;
                $invoice_item->item_id = $item_id;
                $invoice_item->order_id = $order_id;
                $invoice_item->save();


                if (Order::where('id', $order_id)->value('type') === "Normal") {
                    OrderItem::where('id', $item_id)->update(['is_invoice' => 1, 'invoice_no' => $request->invoice_no]);
                    $order_item = OrderItem::where('order_id', $order_id)->where('is_invoice', 0)->count('id');
//                    dd($order_item);
                    if ($order_item == 0) {
                        $orders_ = Order::where('id', $order_id)->update(['is_invoice' => 1]);

                    }


                } else if (Order::where('id', $order_id)->value('type') === "Custom") {
                    CustomOrder::where('id', $item_id)->update(['is_invoice' => 1, 'invoice_no' => $request->invoice_no]);
                    Order::where('id', $order_id)->update(['is_invoice' => 1, 'invoice_no' => $request->invoice_no]);
                }
            }
        }
        if ($request->cash_debit == "Debit") {
            if (!empty($request->user_id)) {
                BalanceSheet::create([
                    'user_id' => $request->user_id,
                    'date' => $request->invoice_date,
                    'narration' => 'Invoice Number' . $request->invoice_no,
                    'debit' => $total_amount,
                ]);
            }
            if (!empty($request->franchise_id)) {
                $franchise_balance = FranchiseBalance::where('id', $request->franchise_id)->first();
                $franchise_balance->balance = $franchise_balance->balance - $total_amount;
                $franchise_balance->save();
                BalanceSheet::create([
                    'franchise_id' => $request->franchise_id,
                    'date' => $request->invoice_date,
                    'narration' => 'Invoice Number' . $request->invoice_no,
                    'debit' => $total_amount,
                    'totalBalance' => $franchise_balance->balance - $total_amount,
                ]);
            }
        }
        $invoice->total_amount = $total_amount;
        $invoice->save();
        flash('Invoice created successfully.')->success();
        return redirect()->route('admin.invoices.show', $last_id);
    }

    public function show($invoice)
    {
        $invoices = MainInvoice::where('id', $invoice)->first();
        return view('admin.pages.invoices.view', compact('invoices', 'invoice'));
    }

    public function edit(MainInvoice $invoice)
    {

        $invoices = MainInvoice::where('id', $invoice->id)->first();
        $franchises = Franchise::all();
        $users = User::all();
        $delivery_boys = Admin::where('type', 'Deliveryboy')->get();

        return view('admin.pages.invoices.edit', compact('invoices', 'invoice', 'franchises', 'delivery_boys'));
    }

    public function print(Request $request)
    {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 200);
        $invoice = $request->invoice_no;
        $invoices = MainInvoice::where('id', $invoice)->first();
        $img = 'http://143.110.188.148/dessertdenapp/theme/images/logo.png';
        $pdf = PDF::setOptions(['logOutputFile' => storage_path('logs/log.htm'), 'tempDir' => storage_path('logs/'), 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('admin.ddinvoice', compact('invoice', 'invoices', 'img'))->setPaper('a5', 'landscape');

        return $pdf->stream();
//        return $pdf->download('invoice.pdf');

    }

    public function update(Request $request, MainInvoice $invoice)
    {
        if (!empty($request->deliveryboy_id)) {
            $invoice->deliveryboy_id = $request->deliveryboy_id;
            $invoice->save();
        }
        if (!empty($request->invoice_date)) {
            $invoice->invoice_date = $request->invoice_date;
            $invoice->save();
        }
        $total_amount = 0;
        foreach ($request->item_id as $order_id => $value) {

            foreach ($value as $item_id) {

                if (Order::where('id', $order_id)->value('type') === "Normal") {
                    $amount = OrderItem::where('id', $item_id)->value('amount');
                    $total_amount = $total_amount + $amount;

                } else if (Order::where('id', $order_id)->value('type') === "Custom") {
                    $amount = Order::where('id', $order_id)->value('total_amount');
                    $total_amount = $total_amount + $amount;
                }
//                dd($total_amount);

                $invoice_item = new MainInvoiceItem;
                $invoice_item->invoice_id = $invoice->id;
                $invoice_item->item_id = $item_id;
                $invoice_item->order_id = $order_id;
                $invoice_item->save();


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
        if ($invoice->cash_debit == "Debit") {
            if (!empty($invoice->user_id)) {
                BalanceSheet::create([
                    'user_id' => $request->user_id,
                    'date' => $request->invoice_date,
                    'narration' => 'Invoice Number' . $request->invoice_no,
                    'debit' => $total_amount,
                ]);
            }
            if (!empty($invoice->franchise_id)) {
                $franchise_balance = Franchise::where('id', $invoice->franchise_id)->first();

                $franchise_balance->balance = $franchise_balance->balance - $total_amount;
                $franchise_balance->save();
                BalanceSheet::create([
                    'franchise_id' => $invoice->franchise_id,
                    'date' => $invoice->invoice_date,
                    'narration' => 'Invoice Number' . $invoice->invoice_no,
                    'debit' => $total_amount,
                    'totalBalance' => $franchise_balance->balance - $total_amount,
                ]);
            }
        }
        $invoice->total_amount = $total_amount;
        $invoice->save();
        flash('Invoice created successfully.')->success();
        return redirect()->route('admin.invoices.index');
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
        $invoices = MainInvoice::where('id', '<>', 0)->orderBy('id', 'DESC');


        if ($search != '') {
            $invoices->where(function ($query) use ($search) {
                $query->where("invoice_no", "like", "%{$search}%");
                $franchise = Franchise::where('name', "like", "%{$search}%")->value('id');
                if (empty($franchise)) {
                    $user = User::where('first_name', "like", "%{$search}%")->value('id');
//                    dd($user);
                    $query->orWhere('user_id', $user);
                } else {
                    $query->orWhere('franchises_id', $franchise);
                }
//                if(Carbon::createFromFormat('dd-mm-yy', $search) !== false){
//
//                    $query->orWhere("invoice_date",Carbon::parse($search)->format('Y-m-d'));
//                }else{
//                    $query=$query;
//                }
            });
        }
        $count = $invoices->count();

        $records["recordsTotal"] = $count;
        $records["recordsFiltered"] = $count;
        $records['data'] = array();

        $invoices = $invoices->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order)->get();

        foreach ($invoices as $invoice) {
            $params = array(
                'url' => route('admin.invoices.update', $invoice->id),
                'checked' => ($invoice->active == 0) ? "checked" : "",
                'getaction' => '',
                'class' => '',
                'id' => $invoice->invoice_no
            );
            $records['data'][] = [
                'checkbox' => view('admin.shared.checkbox')->with('id', $invoice->id)->render(),
                'invoice_no' => $invoice->invoice_no,
                'total_amount' => $invoice->total_amount,
                'cash_debit' => $invoice->cash_debit,
                'admin' => Admin::where('id', $invoice->admin_id)->value('name'),
                'deliveryboy' => Admin::where('id', $invoice->deliveryboy_id)->value('name'),
                'invoice_date' => Carbon::parse($invoice->invoice_date)->format('d-m-Y'),
                'customer' => !empty($invoice->user_id) ? User::where('id', $invoice->user_id)->value('first_name') . '  ' . User::where('id', $invoice->user_id)->value('last_name') : Franchise::where('id', $invoice->franchise_id)->value('name'),
                'active' => view('admin.shared.switch')->with(['params' => $params])->render(),
                'action' => view('admin.shared.actions')->with('id', $invoice->id)->render(),
            ];
        }
        return $records;
    }
}
