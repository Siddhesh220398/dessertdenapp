<?php

namespace App\Http\Middleware;

use App\Models\AdminToken;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckAdmin
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
        $header = $request->header('Authorization');

        if (!empty($header)) {
            if (strpos($header, 'Bearer ') !== FALSE) {
                $token = str_replace('Bearer ', '', $header);
                $admin_id=AdminToken::where('token',$token)->value('admin_id');
                if(!empty($admin_id)){
                    Auth::guard('admin')->loginUsingId($admin_id);
                    // dd(Auth::guard('admin')->user());
                    return $next($request);
                }
            }
        }

        return response()->json(['message' => 'Unauthenticated'], 401);
    }
}
