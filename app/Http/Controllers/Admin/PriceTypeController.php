<?php

namespace App\Http\Controllers\Admin;

use App\Models\PriceType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PriceTypeController extends Controller
{
    public function index()
    {
        return view('admin.pages.pricetypes.index');
    }

    public function create()
    {
        return view('admin.pages.pricetypes.create');
    }


    public function store(Request $request)
    {
        $rules = [
            'type' => 'required',

        ];

        $this->validateForm($request->all(), $rules);

        $price = new PriceType();
        $price->type = $request->type;
        $price->percentage = $request->percentage;
        $price->save();
        flash('Price Type added successfully.')->success();
        return redirect()->route('admin.pricetypes.index');
    }


    public function show(PriceType $price)
    {
        abort(404);
    }


    public function edit(PriceType $price)
    {
        return view('admin.pages.pricetypes.edit', compact('price'));
    }

    public function update(Request $request, PriceType $price)
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
                'type' => 'required',

            ];
            $this->validateForm($request->all(), $rules);
            $price->type = $request->type;
            $price->percentage = $request->percentage;
            $price->save();

            flash('Price Type updated successfully.')->success();
            return redirect()->route('admin.pricetypes.index');
        }
    }


    public function destroy(Request $request, $id)
    {
        if(!empty($request->action) && $request->action == 'delete_all'){
            $content = ['status'=>204, 'message'=>"something went wrong"];
            // Storage::delete(Times::whereIn('id',explode(',',$request->ids))->pluck('image')->toArray());
            PriceType::destroy(explode(',',$request->ids));
            $content['status']=200;
            $content['message'] = "Prices deleted successfully.";
            return response()->json($content);
        }else{
            // Storage::delete(Banners::whereIn('id',explode(',',$id))->pluck('image')->toArray());
            PriceType::destroy($id);
            if(request()->ajax()){
                $content = array('status'=>200, 'message'=>"Price deleted successfully.");
                return response()->json($content);
            }else{
                flash('Prices deleted successfully.')->success();
                return redirect()->route('admin.pricetypes.index');
            }
        }
    }


    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $pricetypes = PriceType::where('id', '<>', 0)->orderBy('id','DESC');

        if($search != ''){
            $pricetypes->where(function($query) use ($search){
                $query->where("id", "like", "%{$search}%");
            });
        }
        $count = $pricetypes->count();

        $records["recordsTotal"] = $count;
        $records["recordsFiltered"] = $count;
        $records['data'] = array();

        $pricetypes = $pricetypes->offset($offset)->limit($limit)->orderBy($sort_column,$sort_order)->get();

        foreach ($pricetypes as $price) {
            $params = array(
                'url'=>route('admin.pricetypes.update',$price->id),
                'checked'=> ($price->active == 0) ? "checked" : "",
                'getaction'=>'',
                'class'=>'',
                'id' => $price->id
            );
            $records['data'][] = [
                'checkbox'=>view('admin.shared.checkbox')->with('id',$price->id)->render(),
                'type' => $price->type,
                'percentage' => $price->percentage,

                'active' => view('admin.shared.switch')->with(['params'=> $params])->render(),
                'action' => view('admin.shared.actions')->with('id', $price->id)->render(),
            ];
        }
        return $records;
    }
}
