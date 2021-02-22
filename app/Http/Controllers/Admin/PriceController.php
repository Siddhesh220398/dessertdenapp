<?php

namespace App\Http\Controllers;

// use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PriceModel;

class PriceController extends Controller
{
    public function index()
    {
        return view('admin.pages.prices.index');
    }

    public function create()
    {
        return view('admin.pages.prices.create');
    }

    
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            
        ];
        dd($request->name);
        $this->validateForm($request->all(), $rules);
        
        $price = new PriceModel();
        $price->name = $request->name;
        
        $price->save();
        flash('Prices added successfully.')->success();
        return redirect()->route('admin.prices.index');
    }

   
    public function show(PriceModel $price)
    {
        abort(404);
    }

     
  public function edit(PriceModel $price)
    {
        return view('admin.pages.prices.edit', compact('price'));
    }
    
    public function update(Request $request, PriceModel $price)
    {
    	
        if(!empty($request->action) && $request->action == 'change_status'){
            $content = ['status'=>204, 'message'=>"something went wrong"];
            $price->active = ($request->value == 'y' ? 0 : 1);
            $price->save();
            $price['status']=200;
            $content['message'] = "Status updated successfully.";
            return response()->json($content);
        }else{
            $rules = [
                'name' => 'required',
               
            ];
            $this->validateForm($request->all(), $rules);
            
            $price->name = $request->name;
           
            $price->save();
            
            flash('Prices updated successfully.')->success();
            return redirect()->route('admin.prices.index');
        }
    }

    
    public function destroy(Request $request, $id)
    {
        if(!empty($request->action) && $request->action == 'delete_all'){
            $content = ['status'=>204, 'message'=>"something went wrong"];
            // Storage::delete(Times::whereIn('id',explode(',',$request->ids))->pluck('image')->toArray());
            PriceModel::destroy(explode(',',$request->ids));
            $content['status']=200;
            $content['message'] = "Prices deleted successfully.";
            return response()->json($content);
        }else{    
            // Storage::delete(Banners::whereIn('id',explode(',',$id))->pluck('image')->toArray());
            PriceModel::destroy($id);
            if(request()->ajax()){
                $content = array('status'=>200, 'message'=>"Price deleted successfully.");
                return response()->json($content);
            }else{
                flash('Prices deleted successfully.')->success();
                return redirect()->route('admin.prices.index');
            }
        }
    }

   
    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $prices = PriceModel::where('id', '<>', 0)->orderBy('id','DESC');
        
        if($search != ''){
            $prices->where(function($query) use ($search){
                $query->where("id", "like", "%{$search}%");
            });
        }
        $count = $prices->count();

        $records["recordsTotal"] = $count;
        $records["recordsFiltered"] = $count;
        $records['data'] = array();

        $prices = $prices->offset($offset)->limit($limit)->orderBy($sort_column,$sort_order)->get();

        foreach ($prices as $price) {
            $params = array(
                'url'=>route('admin.prices.update',$price->id),
                'checked'=> ($price->active == 0) ? "checked" : "",
                'getaction'=>'',
                'class'=>'',
                'id' => $price->id
            );
            $records['data'][] = [
                'checkbox'=>view('admin.shared.checkbox')->with('id',$price->id)->render(),
                'name' => $price->name,
               
                'active' => view('admin.shared.switch')->with(['params'=> $params])->render(),
                'action' => view('admin.shared.actions')->with('id', $price->id)->render(),
            ];
        }
        return $records; 
    }
}
