<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssignOrder;
use Illuminate\Http\Request;

class AssignOrderController extends Controller
{
    public function listing(Request $request)
{
	dd($request->staff_id);
    extract($this->DTFilters($request->all()));
    $assignOrder = AssignOrder::where('admin_id','Admin');

    if($search != ''){
        $staffs->where(function($query) use ($search){
            $query->where("name", "like", "%{$search}%");
        });
    }
    $count = $staffs->count();

    $records["recordsTotal"] = $count;
    $records["recordsFiltered"] = $count;
    $records['data'] = array();

    $staffs = $staffs->offset($offset)->limit($limit)->orderBy($sort_column,$sort_order)->get();

    foreach ($staffs as $staff) {
        $params = array(
            'url'=>route('admin.staffs.update',$staff->id),
            'checked'=> ($staff->active == 0) ? "checked" : "",
            'getaction'=>'',
            'class'=>'',
            'id' => $staff->id
        );
        $records['data'][] = [
            'checkbox'=>view('admin.shared.checkbox')->with('id',$staff->id)->render(),
            'name' => $staff->name,
            'type' => $staff->type,
            'mobile' => $staff->mobile,
            'email' => $staff->email,
            'profile' => '<img src="' . ($staff->profile) . '" alt="Image" class="img-thumbnail" />',
            'active' => view('admin.shared.switch')->with(['params'=> $params])->render(),
            'action' => view('admin.shared.actions')->with('id', $staff->id)->render(),
        ];
    }
    return $records; 
}
}
