<?php

namespace App\Http\Middleware;

use App\Models\FranchiseToken;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckFranchise
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
                $franchise_id=FranchiseToken::where('token',$token)->value('franchise_id');
                if(!empty($franchise_id)){
                    Auth::guard('franchise')->loginUsingId($franchise_id);
                    // dd(Auth::guard('admin')->user());
                    return $next($request);
                }
            }
        }

        return response()->json(['message' => 'Unauthenticated'], 401);
    }
}
