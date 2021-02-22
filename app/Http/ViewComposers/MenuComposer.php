<?php

namespace App\Http\ViewComposers;

use App\Models\DistributorRoleModel;
use App\Models\DistributorSectionModel;
use App\Models\Role;
use App\Models\Section;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class MenuComposer
{
    public $menuItems = [];
    public $permissions = [];
    public $routeName;
    public $title = '';
    public $icon = '';

    public function __construct()
    {

        // print_r(serialize(getPermissions('superadmin'))); exit;
        if (!Auth::guest()) {
            $routeName = Route::currentRouteName();
            $route_parameters = (!empty(Route::current()) ? Route::current()->parameters() : null);
            $this->routeName = $routeName = substr($routeName, 0, strrpos($routeName, '.'));
            $role = Role::where('route', 'like', $routeName . '%');
            $rname=substr($this->routeName, 0, strrpos($this->routeName, '.'));

            if (!empty($route_parameters['slug'])) {
                $role->where('params', $route_parameters['slug']);
            }

            $role = $role->first();

//            if (empty($role)) {
//                $role = DistributorRoleModel::where('route', 'like', $routeName . '%');
//                $role = $role->first();
//            }
            $userPermissions = unserialize(Auth::user()->permissions);
//            if(empty($userPermissions)){
//                $userPermissions = unserialize(Auth::guard('distributor')->user()->permissions);
//            }

            if ($role) {
                $roleId = $role->id;
                $this->title = $role->title;
                $this->icon = $role->image;
            }



//            if ($rname=="distributor") {
//                $sections = DistributorSectionModel::where('active', 1)->orderBy('sequence', 'asc')->with(['distributorroles'])->get()->toArray();
//            }else{
                $sections = Section::where('active', 1)->orderBy('sequence', 'asc')->with(['roles'])->get()->toArray();
//            }
            foreach ($sections as $section) {
                $temp = [];

                if (array_has($section, 'roles')) {
                    $temp['name'] = $section['name'];
                    $temp['image'] = $section['image'];
                    $temp['roles'] = [];

                    foreach ($section['roles'] as $menu) {
                        $li_active = '';
                        if (array_key_exists($menu['id'], $userPermissions) && !empty($userPermissions[$menu['id']])) {
                            if ($menu['id'] == $roleId) {
                                $this->permissions = explode(',', $userPermissions[$role->id]['permissions']);
                                if (!empty($menu['params'])) {
                                    if (!empty($route_parameters['slug'])) {
                                        if ($route_parameters['slug'] == $menu['params']) {
                                            $li_active = 'active';
                                        }
                                    } else {
                                        $li_active = 'active';
                                    }
                                } else {
                                    $li_active = 'active';
                                }
                            }

                            $temp['roles'][] = array_merge(['class' => $li_active], array_except($menu, ['sequence', 'active', 'created_at', 'updated_at']));
                        }
                    }

                    if (count($temp['roles']) > 0)
                        $this->menuItems[] = $temp;
                }
//                else if (array_has($section, 'distributorroles')){
//
//                    $temp['name'] = $section['name'];
//                    $temp['image'] = $section['image'];
//                    $temp['distributorroles'] = [];
//
//                    foreach ($section['distributorroles'] as $menu) {
////                        dd($userPermissions);
//                        $li_activ = '';
//                        if (array_key_exists($menu['id'], $userPermissions) && !empty($userPermissions[$menu['id']])) {
//                            if ($menu['id'] == $roleId) {
//                                $this->permissions = explode(',', $userPermissions[$role->id]['permissions']);
//                                if (!empty($menu['params'])) {
//                                    if (!empty($route_parameters['slug'])) {
//                                        if ($route_parameters['slug'] == $menu['params']) {
//                                            $li_active = 'active';
//                                        }
//                                    } else {
//                                        $li_active = 'active';
//                                    }
//                                } else {
//                                    $li_active = 'active';
//                                }
//                            }
//
//                            $temp['distributorroles'][] = array_merge(['class' => $li_active], array_except($menu, ['sequence', 'active', 'created_at', 'updated_at']));
//                        }
//                    }
//
//                    if (count($temp['distributorroles']) > 0)
//                        $this->menuItems[] = $temp;
//
//                }
            }
        }
    }

    /**
     * Bind data to the view.
     *
     * @param View $view
     * @return void
     */
    public function compose(View $view)
    {
        $data = [
            'menu' => $this->menuItems,
            'permissions' => $this->permissions,
            'routeName' => $this->routeName,
            'title' => $this->title,
            'icon' => $this->icon,
            'mend_sign' => '<span class="mendatory">*</span>',
        ];
        $view->with($data);
    }
}

