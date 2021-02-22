<?php
Route::group(['middleware' => ['distributorpermission', 'distributorrevalidate'], 'namespace' => 'Distributor'] , function () {

//    Route::get('/dashboard', 'DashboardController@index')->name('dashboard.index');

});
