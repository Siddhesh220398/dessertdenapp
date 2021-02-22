<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use DB;

class StaffController extends Controller
{
    public function index()
    {
        return view('admin.pages.staffs.index');
    }


    public function create()
    {
        $sections=DB::table('sections')->get();
        return view('admin.pages.staffs.create',compact('sections'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'mobile' => 'required',
            'email' => 'required',
            'user_type' => 'required',
            'profile' => 'sometimes|image|mimes:jpeg,jpg,png',
        ];
        $this->validateForm($request->all(), $rules);

        $section_array =[];
        foreach ($request->section as $section =>$key){
//            dd($key['permission']);
        $section_array[$section]['permissions']= implode(',',$key['permissions']);
        }

        $staff = new Admin;
        $staff->name = $request->name;
        $staff->email = $request->email;
        $staff->mobile = $request->mobile;
        $staff->category_id = $request->category_id;
        $staff->password = Hash::make($request->password);
        $staff->view_password = $request->password;
        $staff->permissions=serialize($section_array);
        $staff->type = $request->user_type;

        if (!empty($request->profile)) {
            $staff->profile = $request->file('profile')->store('admins');
        }
        $staff->save();

        flash('Staff added successfully.')->success();
        return redirect()->route('admin.staffs.index');
    }

    public function show($staffs)
    {
        $staff = Admin::where('id', $staffs)->first();
        return view('admin.pages.staffs.view', compact('staff'));
    }


    public function edit(Admin $staff)
    {
        $sections=DB::table('sections')->get();
            $section_array=[];
//        dd(unserialize($staff->permissions));
        if($staff->permissions){
        $section_ar =unserialize($staff->permissions);
        $section_array=[];
            foreach ($section_ar as $sr => $key){
                $section_array[$sr]['permissions']=explode(',',$key['permissions']);
            }
        }
//        dd($section_array);
        // $categories = Category::where('active',1)->get();
        return view('admin.pages.staffs.edit', compact('staff','section_array','sections'));
    }


    public function update(Request $request, Admin $staff)
    {
        if (!empty($request->action) && $request->action == 'change_status') {
            $content = ['status' => 204, 'message' => "something went wrong"];
            $staff->active = ($request->value == 'y' ? 0 : 1);
            $staff->save();
            $content['status'] = 200;
            $content['message'] = "Status updated successfully.";
            return response()->json($content);
        } else {
            $rules = [
                'name' => 'required|string',
                'mobile' => 'required',
                'email' => 'required',
            ];
            $this->validateForm($request->all(), $rules);
            
            if($request->section){
            $section_array =[];
                foreach ($request->section as $section =>$key){
                $section_array[$section]['permissions']= implode(',',$key['permissions']);
                }
                
            }

            $staff->name = $request->name;
            $staff->email = $request->email;
            $staff->mobile = $request->mobile;
            $staff->category_id = $request->category_id;
            $staff->password = Hash::make($request->password);
            $staff->view_password = $request->password;
             $staff->permissions=serialize($section_array);

            if (!empty($request->profile)) {
                $staff->profile = $request->file('profile')->store('admins');
            }
            $staff->save();

            flash('Staff updated successfully.')->success();
            return redirect()->route('admin.staffs.index');
        }
    }


    public function destroy(Request $request, $id)
    {
        if (!empty($request->action) && $request->action == 'delete_all') {
            $content = ['status' => 204, 'message' => "something went wrong"];
            Admin::destroy(explode(',', $request->ids));
            $content['status'] = 200;
            $content['message'] = "Staff deleted successfully.";
            return response()->json($content);
        } else {
            Admin::destroy($id);
            if (request()->ajax()) {
                $content = array('status' => 200, 'message' => "Staff deleted successfully.");
                return response()->json($content);
            } else {
                flash('Staff deleted successfully.')->success();
                return redirect()->route('admin.staffs.index');
            }
        }
    }


    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $staffs = Admin::where('id', '<>', 0)->orderBy('id', 'DESC');

        if ($search != '') {
            $staffs->where(function ($query) use ($search) {
                $query->where("name", "like", "%{$search}%");
            });
        }
        $count = $staffs->count();

        $records["recordsTotal"] = $count;
        $records["recordsFiltered"] = $count;
        $records['data'] = array();

        $staffs = $staffs->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order)->get();

        foreach ($staffs as $staff) {
            $params = array(
                'url' => route('admin.staffs.update', $staff->id),
                'checked' => ($staff->active == 0) ? "checked" : "",
                'getaction' => '',
                'class' => '',
                'id' => $staff->id
            );
            $cat_name = '';
            if ($staff->category_id == 0) {
                $cat_name = 'cake';
            } else if ($staff->category_id == 1) {
                $cat_name = 'bakery';
            } else {
                $cat_name = 'Other';
            }
//        dd(unserialize($staff->permissions));

            $records['data'][] = [
                'checkbox' => view('admin.shared.checkbox')->with('id', $staff->id)->render(),
                'name' => $staff->name,
                'type' => $staff->type,
                'mobile' => $staff->mobile,
                'email' => $staff->email,
                'category_id' => $cat_name,
                'profile' => '<img src="' . ($staff->profile) . '" alt="Image" class="img-thumbnail" />',
                'active' => view('admin.shared.switch')->with(['params' => $params])->render(),
                'action' => view('admin.shared.actions')->with('id', $staff->id)->render(),
            ];
        }
        return $records;
    }
}
