<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Times;
use Illuminate\Support\Facades\Storage;

class TimeController extends Controller
{
     public function index()
    {
        return view('admin.pages.times.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.pages.times.create');
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
            'startingtime' => 'required',
            'endingtime' => 'required',
            
        ];
        $this->validateForm($request->all(), $rules);
        
        $times = new Times();
        $times->startingtime = $request->startingtime;
        $times->endingtime = $request->endingtime;
        $times->hours = $request->hours;
        $times->save();

        flash('Times added successfully.')->success();
        return redirect()->route('admin.times.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Times $times)
    {
        abort(404);
    }

     
  public function edit(Times $time)
    {
        return view('admin.pages.times.edit', compact('time'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Times $time)
    {
    	
        if(!empty($request->action) && $request->action == 'change_status'){
            $content = ['status'=>204, 'message'=>"something went wrong"];
            $time->active = ($request->value == 'y' ? 0 : 1);
            $time->save();
            $time['status']=200;
            $content['message'] = "Status updated successfully.";
            return response()->json($content);
        }else{
            $rules = [
                'startingtime' => 'required',
                'endingtime' => 'required'
            ];
            $this->validateForm($request->all(), $rules);
            
            $time->startingtime = $request->startingtime;
            $time->endingtime = $request->endingtime;
            $time->hours = $request->hours;
            
            $time->save();
            
            flash('Times updated successfully.')->success();
            return redirect()->route('admin.times.index');
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
            // Storage::delete(Times::whereIn('id',explode(',',$request->ids))->pluck('image')->toArray());
            Times::destroy(explode(',',$request->ids));
            $content['status']=200;
            $content['message'] = "Times deleted successfully.";
            return response()->json($content);
        }else{    
            // Storage::delete(Banners::whereIn('id',explode(',',$id))->pluck('image')->toArray());
            Times::destroy($id);
            if(request()->ajax()){
                $content = array('status'=>200, 'message'=>"Times deleted successfully.");
                return response()->json($content);
            }else{
                flash('Times deleted successfully.')->success();
                return redirect()->route('admin.times.index');
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
        $times = Times::where('id', '<>', 0)->orderBy('id','DESC');
        
        if($search != ''){
            $times->where(function($query) use ($search){
                $query->where("id", "like", "%{$search}%");
            });
        }
        $count = $times->count();

        $records["recordsTotal"] = $count;
        $records["recordsFiltered"] = $count;
        $records['data'] = array();

        $times = $times->offset($offset)->limit($limit)->orderBy($sort_column,$sort_order)->get();

        foreach ($times as $time) {
            $params = array(
                'url'=>route('admin.times.update',$time->id),
                'checked'=> ($time->active == 0) ? "checked" : "",
                'getaction'=>'',
                'class'=>'',
                'id' => $time->id
            );
            $records['data'][] = [
                'checkbox'=>view('admin.shared.checkbox')->with('id',$time->id)->render(),
                'startingtime' => $time->startingtime,
                'endingtime' => $time->endingtime,
                'hours' => $time->hours,
                'active' => view('admin.shared.switch')->with(['params'=> $params])->render(),
                'action' => view('admin.shared.actions')->with('id', $time->id)->render(),
            ];
        }
        return $records; 
    }
}
