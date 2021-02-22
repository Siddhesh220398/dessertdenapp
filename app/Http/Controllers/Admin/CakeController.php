<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cake;
use App\Models\Category;
use App\Models\Flavour;
use App\Models\PriceModel;
use App\Models\Weight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CakeController extends Controller
{
    public function index()
    {

        return view('admin.pages.cakes.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $prices =PriceModel::where('active',1)->get();
        $flavours = FLavour::where('active',1)->get();
         $categories = Category::where('active',1)->get();
        return view('admin.pages.cakes.create',compact('flavours','categories','prices'));
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
            'name' => 'required',
            'description' => 'required',
            'code' => 'required',
            'image' => 'required|image',
            'weights' => 'nullable|array',
            'flavours' => 'nullable|array',
        ];
        $this->validateForm($request->all(), $rules);
        
        $cake = new Cake;
        $cake->name = $request->name;  
        $cake->description = $request->description;  
        $cake->code = $request->code;
        $cake->image = $request->file('image')->store('cake');
        $cake->save();

        if (!empty($request->weights)) {
            $weights = [];
            foreach ($request->weights as $key => $value) {
                $weights[] = new Weight(['weight' => $value]);
            }
            $cake->weights()->saveMany($weights);
        }

        if (!empty($request->flavours)) {
            $input_flavours = [];
            foreach ($request->flavours as $flavour) {
                if (!empty($request->is_default) && $request->is_default == $flavour) {
                    $input_flavours[$flavour] = ['is_default' => 1];
                } else {
                    $input_flavours[$flavour] = ['is_default' => 0];
                }
            }
            $cake->flavours()->sync($input_flavours);
        }

        if (!empty($request->categories)) {
            $cake->categories()->sync($request->categories);
        }
        if (!empty($request->types)) {
            $cake->prices()->sync($request->types);
        
        }

        flash('Cakes added successfully.')->success();
        return redirect()->route('admin.cakes.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Cake $cakes)
    {
        abort(404);
    }

    public function edit(Cake $cake)
    {
        $prices =PriceModel::where('active',1)->get();
        $flavours = Flavour::where('active',1)->get();
        $categories = Category::where('active',1)->get();
        $cake_price=$cake->prices()->pluck('price_model_id')->toArray();
        // dd($cake_price);
        $cake_flavours = $cake->flavours()->pluck('flavours.id')->toArray();
        $cake_default_flavour = $cake->flavours()->where('is_default', 1)->first()->id ?? 0;
        $cake_categories = $cake->categories()->pluck('categories.id')->toArray();
        return view('admin.pages.cakes.edit', compact('cake','flavours', 'cake_flavours','categories', 'cake_categories', 'cake_default_flavour','prices','cake_price'));
    }

 
    public function update(Request $request,Cake $cake)
    {
        if(!empty($request->action) && $request->action == 'change_status'){
            $content = ['status'=>204, 'message'=>"something went wrong"];
            $cake->active = ($request->value == 'y' ? 0 : 1);
            $cake->save();
            $content['status']=200;
            $content['message'] = "Status updated successfully.";
            return response()->json($content);
        }else{
            $rules = [
                'name' => 'required',
                'description' => 'required',
                'code' => 'required',
                'image' => 'nullable|image'
            ];
            $this->validateForm($request->all(), $rules);
            
            $cake->name = $request->name;
            if (!empty($request->image)) {
                Storage::delete($cake->image);
                $cake->image = $request->file('image')->store('cake');
            }
            $cake->save();

            if (!empty($request->weights)) {
                $weights = [];
                foreach ($request->weights as $key => $value) {
                    $weights[] = new Weight(['weight' => $value]);
                }
                $cake->weights()->delete();
                $cake->weights()->saveMany($weights);
            }

            if (!empty($request->flavours)) {
                $input_flavours = [];
                foreach ($request->flavours as $flavour) {
                    if (!empty($request->is_default) && $request->is_default == $flavour) {
                        $input_flavours[$flavour] = ['is_default' => 1];
                    } else {
                        $input_flavours[$flavour] = ['is_default' => 0];
                    }
                }
                $cake->flavours()->sync($input_flavours);
            }

            if (!empty($request->categories)) {
                $cake->categories()->sync($request->categories);
            }

             if (!empty($request->types)) {
            $cake->prices()->sync($request->types);
            }


            flash('Cake updated successfully.')->success();
            return redirect()->route('admin.cakes.index');
        } 
    }
   
    public function destroy(Request $request, $id)
    {
        if(!empty($request->action) && $request->action == 'delete_all'){
            $content = ['status'=>204, 'message'=>"something went wrong"];
            Storage::delete(Cake::whereIn('id',explode(',',$request->ids))->pluck('image')->toArray());
            Cake::destroy(explode(',',$request->ids));
            $content['status']=200;
            $content['message'] = "Cake deleted successfully.";
            return response()->json($content);
        }else{    
            Storage::delete(Cake::whereIn('id',explode(',',$id))->pluck('image')->toArray());
            Cake::destroy($id);
            if(request()->ajax()){
                $content = array('status'=>200, 'message'=>"Cake deleted successfully.");
                return response()->json($content);
            }else{
                flash('Cake deleted successfully.')->success();
                return redirect()->route('admin.cakes.index');
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
        $cakes = Cake::where('id', '<>', 0);
        
        if($search != ''){
            $cakes->where(function($query) use ($search){
                $query->where("name", "like", "%{$search}%");
            });
        }
        $count = $cakes->count();

        $records["recordsTotal"] = $count;
        $records["recordsFiltered"] = $count;
        $records['data'] = array();

        $cakes = $cakes->offset($offset)->limit($limit)->orderBy($sort_column,$sort_order)->get();

        foreach ($cakes as $cake) {
            $params = array(
                'url'=>route('admin.categories.update',$cake->id),
                'checked'=> ($cake->active == 0) ? "checked" : "",
                'getaction'=>'',
                'class'=>'',
                'id' => $cake->id
            );
            $records['data'][] = [
                'checkbox'=>view('admin.shared.checkbox')->with('id',$cake->id)->render(),
                'name' => $cake->name,
                'description' => $cake->description,
                'code' => $cake->code,
                'image' => '<img src="' . Storage::url($cake->image) . '" alt="Image" class="img-thumbnail" />',
                'active' => view('admin.shared.switch')->with(['params'=> $params])->render(),
                'action' => view('admin.shared.actions')->with('id', $cake->id)->render(),
            ];
        }
        return $records; 
    }
}
