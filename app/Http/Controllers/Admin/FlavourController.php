<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Flavour;

class FlavourController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.pages.flavours.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.pages.flavours.create');
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
            'flavourname' => 'required|string',
            'rate' => 'required',
            'description' => 'required|string',
           
        ];
        $this->validateForm($request->all(), $rules);
        
        $flavour = new Flavour;
        $flavour->name = $request->flavourname;
        $flavour->description = $request->description;
        $flavour->rate = $request->rate;
        
        $flavour->save();

        flash('Flavour added successfully.')->success();
        return redirect()->route('admin.flavours.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Flavour $flavour)
    {

        return view('admin.pages.flavours.edit', compact('flavour'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Flavour $flavour)
    {
    	
        if(!empty($request->action) && $request->action == 'change_status'){
            $content = ['status'=>204, 'message'=>"something went wrong"];
            $flavour->active = ($request->value == 'y' ? 0 : 1);
            $flavour->save();
            $content['status']=200;
            $content['message'] = "Status updated successfully.";
            return response()->json($content);
        }else{
            $rules = [
                'flavourname' => 'required|string',
                
            ];
            $this->validateForm($request->all(), $rules);
            
            $flavour->name = $request->flavourname;
            $flavour->description = $request->description;
            $flavour->rate = $request->rate;
            
            $flavour->save();
            
            flash('Flavour updated successfully.')->success();
            return redirect()->route('admin.flavours.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if(!empty($request->action) && $request->action == 'delete_all'){
            $content = ['status'=>204, 'message'=>"something went wrong"];
         
            Flavour::destroy(explode(',',$request->ids));
            $content['status']=200;
            $content['message'] = "Flavours deleted successfully.";
            return response()->json($content);
        }else{    
            
            Flavour::destroy($id);
            if(request()->ajax()){
                $content = array('status'=>200, 'message'=>"Flavours deleted successfully.");
                return response()->json($content);
            }else{
                flash('Flavours deleted successfully.')->success();
                return redirect()->route('admin.flavours.index');
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
        $flavours = Flavour::where('id', '<>', 0)->orderBy('id','DESC');
        
        if($search != ''){
            $flavours->where(function($query) use ($search){
                $query->where("name", "like", "%{$search}%");
            });
        }
        $count = $flavours->count();

        $records["recordsTotal"] = $count;
        $records["recordsFiltered"] = $count;
        $records['data'] = array();

        $flavours = $flavours->offset($offset)->limit($limit)->orderBy($sort_column,$sort_order)->get();

        foreach ($flavours as $flavour) {
            $params = array(
                'url'=>route('admin.flavours.update',$flavour->id),
                'checked'=> ($flavour->active == 0) ? "checked" : "",
                'getaction'=>'',
                'class'=>'',
                'id' => $flavour->id
            );
            $records['data'][] = [
                'checkbox'=>view('admin.shared.checkbox')->with('id',$flavour->id)->render(),
                'name' => $flavour->name,
                'description' => $flavour->description,
                'rate' => $flavour->rate,                             
                'active' => view('admin.shared.switch')->with(['params'=> $params])->render(),
                'action' => view('admin.shared.actions')->with('id', $flavour->id)->render(),
            ];
        }
        return $records; 
    }
}
