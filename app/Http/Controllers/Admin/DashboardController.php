<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index(){
        $count = [];
        return view('admin.pages.dashboard',compact('count'));
    }

    public function showChangePassword(){
        return view('admin.pages.change_password')->with('headTitle', 'Change Password');
    }

    public function changePassword(Request $request){
        $rules = [
            'old_password'=>'required|min:6',
            'password'=>'required|min:6',
            'password_confirmation'=>'required|min:6'
        ];
        $this->validateForm($request->all(), $rules);

        if(Hash::check($request->old_password, Auth::user()->password)){
            $user = Auth::user();
            $user->password = Hash::make($request->password);
            flash('Password changed successfully.')->success();
            $user->save();
        }else{
            flash('Invalid old password.')->error();
        }
        return redirect()->route('admin.changepass');
    }

    public function showProfile(){
        $user = Auth::user();       
        return view('admin.pages.edit_profile', compact('user'))->with(['custom_title' => 'Edit Profile']);
    }

    public function editProfile(Request $request){
        $user = Auth::user();
        
        $rules = [
            'name' => 'required|max:50',
            'email' => 'required|max:80|unique:admins,email,' . $user->id,
            'mobile' => 'required|max:14|min:10',
            'profile' => 'sometimes|image|mimes:jpeg,jpg,png',
        ];

        $this->validateForm($request->all(), $rules);

        if ($request->hasFile('profile')){
            Storage::delete($user->profile);
            $user->profile = $request->file('profile')->store('admins');
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        $user->save();
        
        flash('Profile updated successfully.')->success();
        return redirect()->route('admin.showProfile');
    }

    public function checkUniqueEmail(Request $request){
        $id = (isset($request->id) && $request->id > 0) ? $request->id : 0;
        if( User::where('email',$request->email)->where('id','<>',$id)->count() > 0 )
            return "false";
        else 
            return "true";
    }

    public function checkOldPassword(Request $request){
        if(Hash::check($request->current_password, Auth::user()->password)){
            return "true";
        }else{
            return "false";
        }
    }

    public function checkUniqueAdminEmail(Request $request){
        $user = Admin::where('id' , '!=', $request->id)->where('email', $request->email)->first();
        if($user)
            return "false";
        else
            return "true";
    }
}