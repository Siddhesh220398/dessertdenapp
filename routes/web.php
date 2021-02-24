<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Route::get('test', function(){
//  \Artisan::call('storage:link');
// });

Route::get('/', function () {
    return redirect(route('login'));
});

Route::get('test', function () {
    \Artisan::call('storage:link');
});

Route::group(['prefix' => 'admin'], function () {
    Route::get('/login', 'AdminAuth\LoginController@showLoginForm')->name('login');
    Route::post('/login', 'AdminAuth\LoginController@login');
    Route::post('/logout', 'AdminAuth\LoginController@logout')->name('logout');

    Route::get('/register', 'AdminAuth\RegisterController@showRegistrationForm')->name('register');
    Route::post('/register', 'AdminAuth\RegisterController@register');

    Route::post('/password/email', 'AdminAuth\ForgotPasswordController@sendResetLinkEmail')->name('password.request');
    Route::post('/password/reset', 'AdminAuth\ResetPasswordController@reset')->name('password.email');
    Route::get('/password/reset', 'AdminAuth\ForgotPasswordController@showLinkRequestForm')->name('password.reset');
    Route::get('/password/reset/{token}', 'AdminAuth\ResetPasswordController@showResetForm');
//

});

Route::group(['prefix' => 'admin', 'middleware' => 'auth:admin', 'namespace' => 'Admin'], function () {


    Route::get('products/importproduct', 'ProductController@importProductIndex')->name('admin.products.import');
    Route::post('products/importproduct', 'ProductController@importProduct')->name('admin.products.imports');
    Route::get('product/filter', 'ProductController@report')->name('admin.products.report');

    Route::post('invoices/getOrder', 'MainInvoiceController@getOrder')->name('admin.invoices.getorder');
    Route::post('invoices/getOrderedit', 'MainInvoiceController@getOrderedit')->name('admin.invoices.getOrderedit');
    Route::get('invoices/getOrders', 'MainInvoiceController@getOrders')->name('admin.invoices.getorders');
    Route::get('invoices/orderdetails', 'MainInvoiceController@orderdetails')->name('admin.invoices.orderdetails');
    Route::get('invoices/getitems', 'MainInvoiceController@getitems')->name('admin.invoices.getitems');
    Route::post('invoices/print', 'MainInvoiceController@print')->name('admin.invoices.print');
    Route::post('orders/print', 'OrderController@print')->name('admin.orders.print');

    Route::get('orders/filter', 'OrderController@filter')->name('admin.order.filter');
    Route::post('pricetype/select', 'FranchisePriceController@select')->name('admin.pricetype.select');
    Route::post('orders/search', 'OrderController@search')->name('admin.orders.search');
    Route::post('products/search', 'ProductController@search')->name('admin.products.search');
    Route::get('invoices/series', 'InvoiceController@series')->name('admin.invoices.series');
//    Route::get('orders/items/{id}', 'OrderController@orderitemedit')->name('admin.orders.item.edit');
});

Route::group(['prefix' => 'franchise'], function () {
    Route::get('/login', 'FranchiseAuth\LoginController@showLoginForm')->name('franchise.login');
    Route::post('/login', 'FranchiseAuth\LoginController@login');
    Route::post('/logout', 'FranchiseAuth\LoginController@logout')->name('franchise.logout');

    Route::get('/register', 'FranchiseAuth\RegisterController@showRegistrationForm')->name('register');
    Route::post('/register', 'FranchiseAuth\RegisterController@register');

    Route::post('/password/email', 'FranchiseAuth\ForgotPasswordController@sendResetLinkEmail')->name('password.request');
    Route::post('/password/reset', 'FranchiseAuth\ResetPasswordController@reset')->name('password.email');
    Route::get('/password/reset', 'FranchiseAuth\ForgotPasswordController@showLinkRequestForm')->name('password.reset');
    Route::get('/password/reset/{token}', 'FranchiseAuth\ResetPasswordController@showResetForm');
});

Route::group(['prefix' => 'distributor'], function () {
    Route::get('/login', 'DistributorAuth\LoginController@showLoginForm')->name('distributor.login');
    Route::post('/login', 'DistributorAuth\LoginController@login');
    Route::post('/logout', 'DistributorAuth\LoginController@logout')->name('distributor.logout');


    Route::get('/register', 'DistributorAuth\RegisterController@showRegistrationForm')->name('register');
    Route::post('/register', 'DistributorAuth\RegisterController@register');

    Route::post('/password/email', 'DistributorAuth\ForgotPasswordController@sendResetLinkEmail')->name('password.request');
    Route::post('/password/reset', 'DistributorAuth\ResetPasswordController@reset')->name('password.email');
    Route::get('/password/reset', 'DistributorAuth\ForgotPasswordController@showLinkRequestForm')->name('password.reset');
    Route::get('/password/reset/{token}', 'DistributorAuth\ResetPasswordController@showResetForm');
});

Route::group(['prefix' => 'distributor', 'middleware' => 'auth:distributor', 'namespace' => 'Distributor'], function () {
    Route::get('/dashboard', 'DashboardController@index')->name('distributor.dashboard.index');

});
