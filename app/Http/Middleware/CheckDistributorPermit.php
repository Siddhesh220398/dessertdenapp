<?php

namespace App\Http\Middleware;

use App\Models\DistributorRoleModel;
use Closure;
use Illuminate\Support\Facades\Route;


class CheckDistributorPermit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $routeName = Route::currentRouteName();
        $method = substr($routeName, strrpos($routeName, '.') + 1);
        $routeName = substr($routeName, 0, strrpos($routeName, '.'));
        $method = ($method != '' ? $method : $routeName);
        $permission = '';
        $role = DistributorRoleModel::where('route', 'like', $routeName.'%')->first();

        $access = ['index', 'listing', 'showSetting', 'booking_details'];
        $add = ['store', 'create', 'import'];
        $update = ['edit', 'update', 'changesetting','sequence', 'edit_sequence', 'block', 'send_notification', 'submit_notification'];
        $view = ['show', 'download', 'export', 'chat_messages'];
        $delete = ['destroy'];

        if(in_array($method, $add))
            $permission = 'add';
        elseif(in_array($method, $update))
            $permission = 'edit';
        elseif(in_array($method, $view))
            $permission ='view';
        elseif(in_array($method, $delete))
            $permission = 'delete';
        elseif(in_array($method, $access))
            $permission = 'access';

        if($permission != ''){
            $user_permissions = unserialize($request->user()->permissions);

            if(!empty($role) && array_key_exists($role->id, $user_permissions)){
                if(!empty($user_permissions[$role->id]['permissions']) && in_array($permission, explode(',', $user_permissions[$role->id]['permissions']))){
                    return $next($request);
                }
            }
        }

        return redirect('distributor/dashboard');
    }
}
