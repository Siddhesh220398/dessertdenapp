<?php

namespace App\Http\Middleware;

use App\Distributor;

use App\Models\DistributorToken;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckDistributor
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
                $admin_id=DistributorToken::where('token',$token)->value('admin_id');
                if(!empty($admin_id)){
                    Auth::guard('distributor')->loginUsingId($admin_id);
                    // dd(Auth::guard('admin')->user());
                    return $next($request);
                }
            }
        }

        return response()->json(['message' => 'Unauthenticated'], 401);
    }
}
