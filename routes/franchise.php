<?php

Route::get('/home', function () {
    $users[] = Auth::user();
    $users[] = Auth::guard()->user();
    $users[] = Auth::guard('franchise')->user();

    //dd($users);

    return view('franchise.home');
})->name('home');

