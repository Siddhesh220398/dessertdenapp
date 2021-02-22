<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BalanceSheet;
use App\Models\City;
use App\Franchise;
use App\Models\FranchiseBalance;
use App\Models\Order;
use App\Models\UserBalance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class FranchiseController extends Controller
{
   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   public function index()
   {
    return view('admin.pages.franchises.index');
}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    	$cities = City::where('active', 1)->get();
        return view('admin.pages.franchises.create',compact('cities'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'mobile_no' => 'required',
            'address' => 'required',
            'password' => 'required',

        ];
        $this->validateForm($request->all(), $rules);

        $franchise = new Franchise;
        $franchise->name = $request->name;
        $franchise->address = $request->address;
        $franchise->mobile_no = $request->mobile_no;
        $franchise->email = $request->email;
        $franchise->password = Hash::make($request->password);
        $franchise->viewpassword = $request->password;
        $franchise->balance = $request->balance;
        $franchise->gstn_no = $request->gstn_no;
        $franchise->city_id = $request->city_id;
        $franchise->is_visible = $request->is_visible;
       if(!empty($request->balance) && $request->balance == 0){
        BalanceSheet::create([
            'franchise_id'=>$franchise->id,
            'date'=>Carbon::now()->format('Y-m-d'),
            'narration'=>'Add Balance',
            'credit'=>$request->balance,
        ]);
       }
        $franchise->save();

        flash('franchise added successfully.')->success();
        return redirect()->route('admin.franchises.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function show(Franchise $franchise)
    {
        $firstDayofPreviousMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $lastDayofPreviousMonth = Carbon::now()->endOfMonth()->format('Y-m-d');
        $balances = BalanceSheet::where('franchise_id', $franchise->id)->where('created_at','>=',$firstDayofPreviousMonth)->where('created_at','<=',$lastDayofPreviousMonth)->orderBy('created_at','Desc')->get();
//        dd($firstDayofPreviousMonth);
        $orders = Order::where('franchises_id', $franchise->id)->where('delivery_date','>=',$firstDayofPreviousMonth)->where('delivery_date','<=',$lastDayofPreviousMonth)->orderBy('id','DESC')->get();
//     dd($orders);
        return view('admin.pages.franchises.view', compact('franchise', 'balances', 'orders'));
    }


    public function edit(Franchise $franchise)
    {
    	$cities = City::where('active', 1)->get();
        return view('admin.pages.franchises.edit', compact('franchise', 'cities'));
    }


    public function update(Request $request, Franchise $franchise)
    {
        if(!empty($request->action) && $request->action == 'change_status'){
            $content = ['status'=>204, 'message'=>"something went wrong"];
            $franchise->active = ($request->value == 'y' ? 0 : 1);
            $franchise->save();
            $content['status']=200;
            $content['message'] = "Status updated successfully.";
            return response()->json($content);
        }else{
            $rules = [
             'name' => 'required|string',
             'mobile_no' => 'required',
             'city_id' => 'required',
             'address' => 'required',
             'password' => 'required',
         ];
         $this->validateForm($request->all(), $rules);


         $franchise->name = $request->name;
         $franchise->address = $request->address;
         $franchise->mobile_no = $request->mobile_no;
         $franchise->city_id = $request->city_id;
         $franchise->is_visible = $request->is_visible;
         $franchise->email = $request->email;
         $franchise->password = Hash::make($request->password);
         $franchise->viewpassword = $request->password;
            $franchise->gstn_no = $request->gstn_no;
         if($franchise->balance !=$request->balance){
         $franchise->balance =$franchise->balance+ $request->balance;
         $franchise->save();
            BalanceSheet::create([
                'franchise_id'=>$franchise->id,
                'date'=>Carbon::now()->format('Y-m-d'),
                'narration'=>'Add Balance',
                'credit'=>$request->balance,
                'totalBalance'=>$franchise->balance+ $request->balance,
            ]);
        }
            $franchise->save();
         flash('Franchise updated successfully.')->success();
         return redirect()->route('admin.franchises.index');
     }
 }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if(!empty($request->action) && $request->action == 'delete_all'){
            $content = ['status'=>204, 'message'=>"something went wrong"];
            Franchise::destroy(explode(',',$request->ids));
            $content['status']=200;
            $content['message'] = "Franchises deleted successfully.";
            return response()->json($content);
        }else{
            Franchise::destroy($id);
            if(request()->ajax()){
                $content = array('status'=>200, 'message'=>"franchise deleted successfully.");
                return response()->json($content);
            }else{
                flash('franchise deleted successfully.')->success();
                return redirect()->route('admin.franchises.index');
            }
        }
    }

    /**
     * Listing the all resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $franchises = Franchise::where('id', '<>', 0)->orderBy('id','DESC');

        if($search != ''){
            $franchises->where(function($query) use ($search){
                $query->where("name", "like", "%{$search}%");
            });
        }
        $count = $franchises->count();

        $records["recordsTotal"] = $count;
        $records["recordsFiltered"] = $count;
        $records['data'] = array();

        $franchises = $franchises->offset($offset)->limit($limit)->orderBy($sort_column,$sort_order)->get();

        foreach ($franchises as $franchise)

        {
            $params = array(
                'url'=>route('admin.franchises.update',$franchise->id),
                'checked'=> ($franchise->active == 0) ? "checked" : "",
                'getaction'=>'',
                'class'=>'',
                'id' => $franchise->id
            );
            $records['data'][] = [
                'checkbox'=>view('admin.shared.checkbox')->with('id',$franchise->id)->render(),
                'name' => $franchise->name,
                'address' => $franchise->address,
                'mobile_no' => $franchise->mobile_no,
                'city_id' => City::where('id',$franchise->city_id)->value('name'),
                'email' => $franchise->email,
                'balance' => $franchise->balance,

                'active' => view('admin.shared.switch')->with(['params'=> $params])->render(),
                'action' => view('admin.shared.actions')->with('id', $franchise->id)->render(),
            ];
        }
        return $records;
    }
}
