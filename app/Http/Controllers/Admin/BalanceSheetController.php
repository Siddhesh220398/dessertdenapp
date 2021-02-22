<?php

namespace App\Http\Controllers\Admin;

use App\Franchise;
use App\Http\Controllers\Controller;
use App\Models\BalanceSheet;
use Illuminate\Http\Request;

class BalanceSheetController extends Controller
{
    public function index()
    {
        return view('admin.pages.balances.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $franchises = Franchise::all();
        return view('admin.pages.balances.create', compact('franchises'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'bank' => 'required',
            'franchise_id' => 'required',
            'date' => 'required',

        ];
        $this->validateForm($request->all(), $rules);

        $balance = new BalanceSheet;
        $balance->franchise_id = $request->franchise_id;
        $balance->bank = $request->bank;
        $balance->date = $request->date;
        $balance->cash_type = $request->cash_type;
        $balanced = Franchise::where('id', $request->franchise_id)->value('balance');
        if ($request->type === 'Debit') {
            $balancetotal = $balanced - $request->cash;
            Franchise::where('id', $request->franchise_id)->update(['balance' => $balancetotal]);
            $balance->debit = $request->cash;
            $balance->totalbalance = $balancetotal;
        } else {
            $balancetotal = $balanced + $request->cash;
            Franchise::where('id', $request->franchise_id)->update(['balance' => $balancetotal]);
            $balance->credit = $request->cash;
            $balance->totalbalance = $balancetotal;
        }
        $balance->narration = $request->narration;
        $balance->save();

        flash('BalanceSheet added successfully.')->success();
        return redirect()->route('admin.balances.index');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Category $category
     * @return \Illuminate\Http\Response
     */
    public function show(BalanceSheet $balances)
    {
        abort(404);
    }

    public function edit(BalanceSheet $balance)
    {
        $franchises = Franchise::all();
        $b = 0;
        if ($balance->credit < 0) {
            $b = $balance->debit;
        } else {
            $b = $balance->credit;
        }
        return view('admin.pages.balances.edit', compact('balance', 'b', 'franchises'));

    }


    public function update(Request $request, BalanceSheet $balance)
    {
        $rules = [
            'cash' => 'required',

        ];
        $this->validateForm($request->all(), $rules);
        $balanced = Franchise::where('id', $balance->franchise_id)->value('balance');
        if ($balance->type === 'Debit') {
            $balancetotal1 = $balanced + $balance->debit;
            $balancetotal = $balancetotal1 - $request->cash;
            Franchise::where('id', $request->franchise_id)->update(['balance' => $balancetotal]);
            $balance->debit = $request->cash;
            $balance->totalbalance = $balancetotal;
        } else {
            $balancetotal1 = $balanced - $balance->credit;
            $balancetotal = $balancetotal1 + $request->cash;
            Franchise::where('id', $request->franchise_id)->update(['balance' => $balancetotal]);
            $balance->credit = $request->cash;
            $balance->totalbalance = $balancetotal;
        }

        $balance->save();
        flash('BalanceSheet Updated successfully.')->success();
        return redirect()->route('admin.balances.index');
    }

    public function destroy(Request $request, $id)
    {
        if (!empty($request->action) && $request->action == 'delete_all') {
            $content = ['status' => 204, 'message' => "something went wrong"];
            BalanceSheet::destroy(explode(',', $request->ids));
            $content['status'] = 200;
            $content['message'] = "BalanceSheets deleted successfully.";
            return response()->json($content);
        } else {
            $balance=  BalanceSheet::where('id',$id)->first();
//            dd($balance);
            $balanced = Franchise::where('id', $balance->franchise_id)->value('balance');
            if ($balance->debit !=0) {
                $balancetotal1 = $balanced + $balance->debit;

                Franchise::where('id', $balance->franchise_id)->update(['balance' => $balancetotal1]);
//                $balance->debit = $request->cash;
//                $balance->totalbalance = $balancetotal;
            } else {
                $balancetotal1 = $balanced - $balance->credit;

                Franchise::where('id', $balance->franchise_id)->update(['balance' => $balancetotal1]);
//                $balance->credit = $request->cash;
//                $balance->totalbalance = $balancetotal;
            }
            BalanceSheet::destroy($id);
            if (request()->ajax()) {
                $content = array('status' => 200, 'message' => "BalanceSheet deleted successfully.");
                return response()->json($content);
            } else {
                flash('BalanceSheet deleted successfully.')->success();
                return redirect()->route('admin.banners.index');
            }
        }
    }

    /**
     * Listing the all resources from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $balances = BalanceSheet::where('user_id', null)->orderBy('id', 'DESC');

        if ($search != '') {
            $balances->where(function ($query) use ($search) {
                $query->where("serial", "like", "%{$search}%");
            });
        }
        $count = $balances->count();

        $records["recordsTotal"] = $count;
        $records["recordsFiltered"] = $count;
        $records['data'] = array();

        $balances = $balances->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order)->get();

        foreach ($balances as $balance) {
            $params = array(
                'url' => route('admin.balances.update', $balance->id),
                'checked' => ($balance->active == 0) ? "checked" : "",
                'getaction' => '',
                'class' => '',
                'id' => $balance->id
            );

            if ($balance->totalBalance < 0) {
                $totalBalance = ($balance->totalBalance * (-1)) . ' DB';
            } else {
                $totalBalance = ($balance->totalBalance) . ' Cr';
            }
            $records['data'][] = [
                'checkbox' => view('admin.shared.checkbox')->with('id', $balance->id)->render(),
                'franchise_id' => Franchise::where('id', $balance->franchise_id)->value('name'),
                'date' => $balance->date,
                'narration' => $balance->narration,
                'bank' => $balance->bank,
                'credit' => $balance->credit,
                'debit' => $balance->debit,
                'cash_type' => $balance->cash_type,
                'totalBalance' => $totalBalance,
                'active' => view('admin.shared.switch')->with(['params' => $params])->render(),
                'action' => view('admin.shared.actions')->with('id', $balance->id)->render(),
            ];
        }
        return $records;
    }
}
