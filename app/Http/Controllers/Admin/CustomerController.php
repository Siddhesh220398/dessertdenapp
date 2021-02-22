<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\UserBalance;
use App\User;
use Illuminate\Http\Request;


class CustomerController extends Controller
{
    public function index()
    {

        return view('admin.pages.customers.index');
    }

    public function create()
    {
        abort(404);
    }

    public function store(Request $request)
    {
        abort(404);
    }

    public function show(User $customer)
    {
        $balances = UserBalance::where('user_id', $customer->id)->get();
        $orders = Order::where('user_id', $customer->id)->get();
        return view('admin.pages.customers.view', compact('customer', 'balances', 'orders'));
    }

    public function edit(User $customer)
    {

        return view('admin.pages.customers.edit', compact('customer'));

    }

    public function update(Request $request, User $customer)
    {
//        dd($request->all());
        if (!empty($request->action) && $request->action == 'change_status') {
            $content = ['status' => 204, 'message' => "something went wrong"];
            $customer->is_balance = ($request->value == 'y' ? 1 : 0);
            $customer->save();
            $content['status'] = 200;
            $content['message'] = "Status updated successfully.";
            return response()->json($content);
        } else {
            $rules = [
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'mobile_no' => 'required',
                'email' => 'required',
            ];
            $this->validateForm($request->all(), $rules);
            $customer->first_name = $request->first_name;
            $customer->last_name = $request->last_name;
            $customer->email = $request->email;
            $customer->mobile_no = $request->mobile_no;


            if (!empty($request->profile)) {
                $customer->profile = $request->file('profile')->store('users');
            }
            $customer->save();

            flash('Customer updated successfully.')->success();
            return redirect()->route('admin.customers.index');
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!empty($request->action) && $request->action == 'delete_all') {
            $content = ['status' => 204, 'message' => "something went wrong"];
            User::destroy(explode(',', $request->ids));
            $content['status'] = 200;
            $content['message'] = "User deleted successfully.";
            return response()->json($content);
        } else {
            User::destroy($id);
            if (request()->ajax()) {
                $content = array('status' => 200, 'message' => "User deleted successfully.");
                return response()->json($content);
            } else {
                flash('User deleted successfully.')->success();
                return redirect()->route('admin.customers.index');
            }
        }
    }


    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $customers = User::where('id', '<>', 0);

        if ($search != '') {
            $customers->where(function ($query) use ($search) {
                $query->where("first_name", "like", "%{$search}%");
            });
        }
        $count = $customers->count();

        $records["recordsTotal"] = $count;
        $records["recordsFiltered"] = $count;
        $records['data'] = array();

        $customers = $customers->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order)->get();

        foreach ($customers as $customer) {
            $params = array(
                'url' => route('admin.customers.update', $customer->id),
                'checked' => ($customer->active == 0) ? "" : "checked",
                'getaction' => '',
                'class' => '',
                'id' => $customer->id
            );
            $balances = array(
                'url' => route('admin.customers.update', $customer->id),
                'checked' => ($customer->is_balance == 0) ? "" : "checked",
                'getaction' => '',
                'class' => '',
                'id' => $customer->id
            );
            $records['data'][] = [
                'checkbox' => view('admin.shared.checkbox')->with('id', $customer->id)->render(),
                'first_name' => $customer->first_name,
                'last_name' => $customer->last_name,
                'email' => $customer->email,
                'mobile_no' => $customer->mobile_no,
                // 'profile'=>'<img src="' . Storage::url($customer->profile). '" alt="Image" class="img-thumbnail" />',
                'balance' => view('admin.shared.switch')->with(['params' => $balances])->render(),
                'active' => view('admin.shared.switch')->with(['params' => $params])->render(),
                'action' => view('admin.shared.actions')->with('id', $customer->id)->render(),
            ];
        }
        return $records;
    }
}
