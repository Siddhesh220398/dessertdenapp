<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {

        return view('admin.pages.coupons.index');
    }

    public function create()
    {
        return view('admin.pages.coupons.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'code' => 'required',
            'type' => 'required',
            'value' => 'required',
            'expiryDate' => 'required',
            'maxCoupon' => 'required',
            'userAllowed' => 'required',

        ];
        $this->validateForm($request->all(), $rules);

        $coupon = new Coupon;
        $coupon->name = $request->name;
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->expiryDate = \Carbon\Carbon::parse($request->expiryDate)->format('Y-m-d');
        $coupon->maxCoupon = $request->maxCoupon;
        $coupon->userAllowed = $request->userAllowed;

        $coupon->save();

        flash('Coupons added successfully.')->success();
        return redirect()->route('admin.coupons.index');
    }

    public function show(Coupon $coupon)
    {
        abort(404);
    }

    public function edit(Coupon $coupon)
    {

        return view('admin.pages.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        if(!empty($request->action) && $request->action == 'change_status'){
            $content = ['status'=>204, 'message'=>"something went wrong"];
            $coupon->active = ($request->value == 'y' ? 0 : 1);
            $coupon->save();
            $content['status']=200;
            $content['message'] = "Status updated successfully.";
            return response()->json($content);
        }else{
            $rules = [
                'name' => 'required|string',
                'code' => 'required',
                'type' => 'required',
                'value' => 'required',
                'expiryDate' => 'required',
                'maxCoupon' => 'required',
                'userAllowed' => 'required',

            ];
            $this->validateForm($request->all(), $rules);
            
            $coupon->name = $request->name;
            $coupon->code = $request->code;
            $coupon->type = $request->type;
            $coupon->value = $request->value;
            $coupon->expiryDate = \Carbon\Carbon::parse($request->expiryDate)->format('Y-m-d');
            $coupon->maxCoupon = $request->maxCoupon;
            $coupon->userAllowed = $request->userAllowed;

            $coupon->save();

            flash('coupon updated successfully.')->success();
               return redirect()->route('admin.coupons.index');
        }
    }

      public function destroy(Request $request, $id)
    {
        if(!empty($request->action) && $request->action == 'delete_all'){
            $content = ['status'=>204, 'message'=>"something went wrong"];
            Coupon::destroy(explode(',',$request->ids));
            $content['status']=200;
            $content['message'] = "Coupons deleted successfully.";
            return response()->json($content);
        }else{    
            Coupon::destroy($id);
            if(request()->ajax()){
                $content = array('status'=>200, 'message'=>"Coupon deleted successfully.");
                return response()->json($content);
            }else{
                flash('Coupon deleted successfully.')->success();
                return redirect()->route('admin.coupons.index');
            }
        }
    }



    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $coupons = Coupon::where('id', '<>', 0)->orderBy('id','DESC');

        if($search != ''){
            $coupons->where(function($query) use ($search){
                $query->where("name", "like", "%{$search}%");
            });
        }
        $count = $coupons->count();

        $records["recordsTotal"] = $count;
        $records["recordsFiltered"] = $count;
        $records['data'] = array();

        $coupons = $coupons->offset($offset)->limit($limit)->orderBy($sort_column,$sort_order)->get();

        foreach ($coupons as $coupon) {
            $params = array(
                'url'=>route('admin.coupons.update',$coupon->id),
                'checked'=> ($coupon->active == 0) ? "checked" : "",
                'getaction'=>'',
                'class'=>'',
                'id' => $coupon->id
            );
            $records['data'][] = [
                'checkbox'=>view('admin.shared.checkbox')->with('id',$coupon->id)->render(),
                'name' => $coupon->name,
                'code'=>$coupon->code,
                'type'=>$coupon->type,
                'value'=>$coupon->value,
                'expiryDate'=>$coupon->expiryDate,
                'maxCoupon'=>$coupon->maxCoupon,
                'userAllowed'=>$coupon->userAllowed,
                'active' => view('admin.shared.switch')->with(['params'=> $params])->render(),
                'action' => view('admin.shared.actions')->with('id', $coupon->id)->render(),
            ];
        }
        return $records; 
    }
}
