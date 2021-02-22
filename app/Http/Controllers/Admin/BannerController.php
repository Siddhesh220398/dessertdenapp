<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Banners;
use Illuminate\Support\Facades\Storage;


class BannerController extends Controller
{
      public function index()
    {
        return view('admin.pages.banners.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.pages.banners.create');
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
            'serial' => 'required|int',
            'image' => 'required|image',
        ];
        $this->validateForm($request->all(), $rules);
        
        $banners = new Banners;
        $banners->serial = $request->serial;
        $banners->image = $request->file('image')->store('banners');
        $banners->save();

        flash('Banners added successfully.')->success();
        return redirect()->route('admin.banners.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Banners $banners)
    {
        abort(404);
    }
public function edit(Banners $banners)
    {
        abort(404);
    }

 
    public function update(Request $request,Banners $banner)
    {
     abort(404);   
    }
   
    public function destroy(Request $request, $id)
    {
        if(!empty($request->action) && $request->action == 'delete_all'){
            $content = ['status'=>204, 'message'=>"something went wrong"];
            Storage::delete(Banners::whereIn('id',explode(',',$request->ids))->pluck('image')->toArray());
            Banners::destroy(explode(',',$request->ids));
            $content['status']=200;
            $content['message'] = "Banners deleted successfully.";
            return response()->json($content);
        }else{    
            Storage::delete(Banners::whereIn('id',explode(',',$id))->pluck('image')->toArray());
            Banners::destroy($id);
            if(request()->ajax()){
                $content = array('status'=>200, 'message'=>"Banners deleted successfully.");
                return response()->json($content);
            }else{
                flash('Banners deleted successfully.')->success();
                return redirect()->route('admin.banners.index');
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
        $banners = Banners::where('id', '<>', 0)->orderBy('id','DESC');
        
        if($search != ''){
            $banners->where(function($query) use ($search){
                $query->where("serial", "like", "%{$search}%");
            });
        }
        $count = $banners->count();

        $records["recordsTotal"] = $count;
        $records["recordsFiltered"] = $count;
        $records['data'] = array();

        $banners = $banners->offset($offset)->limit($limit)->orderBy($sort_column,$sort_order)->get();

        foreach ($banners as $banner) {
            $params = array(
                'url'=>route('admin.banners.update',$banner->id),
                'checked'=> ($banner->active == 0) ? "checked" : "",
                'getaction'=>'',
                'class'=>'',
                'id' => $banner->id
            );
            $records['data'][] = [
                'checkbox'=>view('admin.shared.checkbox')->with('id',$banner->id)->render(),
                'serial' => $banner->serial,
                'image' => '<img src="' . Storage::url($banner->image) . '" alt="Image" class="img-thumbnail" />',
                'active' => view('admin.shared.switch')->with(['params'=> $params])->render(),
                'action' => view('admin.shared.actions')->with('id', $banner->id)->render(),
            ];
        }
        return $records; 
    }
}
