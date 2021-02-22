<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Franchise;
use App\Models\FranchisePrice;
use Illuminate\Http\Request;

class FranchisePriceController extends Controller
{

    public function index()
    {
        return view('admin.pages.franchisesprice.index');
    }

    public function create()
    {
    	$franchises = Franchise::where('active', 1)->get();
    	$categories=Category::where('active',1)->get();
        return view('admin.pages.franchisesprice.create',compact('franchises','categories'));
    }

    public function store(Request $request)
    {
        $rules = [
            'category_id' => 'required',
            'franchise_id' => 'required',
            'percentage' => 'required',
        ];
        $this->validateForm($request->all(), $rules);
        
        $franchise = new FranchisePrice;
        $franchise->category_id = $request->category_id;
        $franchise->franchise_id = $request->franchise_id;
        $franchise->percentage = $request->percentage;
        $franchise->save();

        flash('franchise Price added successfully.')->success();
        return redirect()->route('admin.franchisesprice.index');
    }

    public function show(FranchisePrice $franchiseprice)
    {
        abort(404);
    }

    public function edit(FranchisePrice $franchisesprice)
    {
    	$franchises = Franchise::where('active', 1)->get();
    	$categories=Category::where('active',1)->get();
        return view('admin.pages.franchisesprice.edit', compact('franchisesprice', 'franchises','categories'));
    }

    public function update(Request $request, FranchisePrice $franchisesprice)
    {
        // dd($id);
        if(!empty($request->action) && $request->action == 'change_status'){

            $content = ['status'=>204, 'message'=>"something went wrong"];
            $franchises=FranchisePrice::where('id',$franchisesprice->id)->update(['active' => ($request->value == 'y' ? 0 : 1)]) ;       
          
          

            $content['status']=200;
            $content['message'] = "Status updated successfully.";
            return response()->json($content);
        }else{
            $rules = [
            'category_id' => 'required',
            'franchise_id' => 'required',
            'percentage' => 'required',
        	];

            $this->validateForm($request->all(), $rules);
             
            FranchisePrice::where('id',$franchisesprice->id)->update(['category_id'=>$request->category_id,'franchise_id' => $request->franchise_id,'percentage'=> $request->percentage]);
           
            
            flash('Franchise updated successfully.')->success();
            return redirect()->route('admin.franchisesprice.index');
        }
    }

    public function destroy(Request $request, $id)
    {
        if(!empty($request->action) && $request->action == 'delete_all'){
            $content = ['status'=>204, 'message'=>"something went wrong"];
            FranchisePrice::destroy(explode(',',$request->ids));
            $content['status']=200;
            $content['message'] = "Franchises deleted successfully.";
            return response()->json($content);
        }else{    
            FranchisePrice::destroy($id);
            if(request()->ajax()){
                $content = array('status'=>200, 'message'=>"franchise deleted successfully.");
                return response()->json($content);
            }else{
                flash('franchise deleted successfully.')->success();
                return redirect()->route('admin.franchisesprice.index');
            }
        }
    }

    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $franchises = FranchisePrice::where('id', '<>', 0)->orderBy('id','DESC');
      
        if($search != ''){
            // $id=Franchise::where("name", "like", "%{$search}%")->value('id');
            $franchises->where(function($query) use ($search){
                $query->where("id", "like", "%{$search}%");
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
                'url'=>route('admin.franchisesprice.update',$franchise->id),
                'checked'=> ($franchise->active == 0) ? "checked" : "",
                'getaction'=>'',
                'class'=>'',
                'id' => $franchise->id
            );
            // dd($params);
            $records['data'][] = [
                'checkbox'=>view('admin.shared.checkbox')->with('id',$franchise->id)->render(),
                'franchise_id' => $franchise->franchise->name,
    			'category_id' => $franchise->category->name,
    			'percentage' => $franchise->percentage,
    			'active' => view('admin.shared.switch')->with(['params'=> $params])->render(),
                'action' => view('admin.shared.actions')->with('id', $franchise->id)->render(),
            ];
        }
        return $records; 
    }
}
