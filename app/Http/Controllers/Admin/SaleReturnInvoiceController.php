<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Admin;
use App\Franchise;
use App\Models\MainInvoice;
use App\Models\SaleReturnInvoice;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SaleReturnInvoiceController extends Controller
{
    public function index()
    {
        return view('admin.pages.salereturninvoices.index');
    }
    public function create()
    {
        return view('admin.pages.salereturninvoices.create');
    }
    public function store(Request $request)
    {}
    public function show($salereturninvoice)
    {}
    public function edit(SaleReturnInvoice $salereturninvoice)
    {
    }


    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $invoices = SaleReturnInvoice::where('id', '<>', 0)->orderBy('id', 'DESC');


        if ($search != '') {
            $invoices->where(function ($query) use ($search) {
                $query->where("sale_return_invoice_no", "like", "%{$search}%");
                $franchise = Franchise::where('name', "like", "%{$search}%")->value('id');
                    $query->orWhere('franchises_id', $franchise);

            });
        }
        $count = $invoices->count();

        $records["recordsTotal"] = $count;
        $records["recordsFiltered"] = $count;
        $records['data'] = array();

        $invoices = $invoices->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order)->get();

        foreach ($invoices as $invoice) {
            $params = array(
                'url' => route('admin.salereturninvoices.update', $invoice->id),
                'checked' => ($invoice->active == 0) ? "checked" : "",
                'getaction' => '',
                'class' => '',
                'id' => $invoice->invoice_no
            );
            $records['data'][] = [
                'checkbox' => view('admin.shared.checkbox')->with('id', $invoice->id)->render(),
                'invoice_no' => $invoice->sale_return_invoice_no,
                'total_amount' => $invoice->total_amount,
                'cash_debit' => $invoice->cash_debit,
                'admin' => Admin::where('id', $invoice->admin_id)->value('name'),
                'invoice_date' => Carbon::parse($invoice->invoice_date)->format('d-m-Y'),
                'customer' =>  Franchise::where('id', $invoice->franchise_id)->value('name'),
                'active' => view('admin.shared.switch')->with(['params' => $params])->render(),
                'action' => view('admin.shared.actions')->with('id', $invoice->id)->render(),
            ];
        }
        return $records;
    }
}
