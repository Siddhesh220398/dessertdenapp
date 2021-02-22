<?php

namespace App\Http\Controllers\Distributor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index(){

        $count = [];
        return view('distributor.pages.dashboard',compact('count'));
    }
}
