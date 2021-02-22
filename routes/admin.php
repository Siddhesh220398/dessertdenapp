<?php

Route::group(['middleware' => ['permission', 'revalidate'], 'namespace' => 'Admin'] , function () {

    Route::get('/', function(){
        return redirect()->route('login');
    });

    /* Dashboard */
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard.index');;
    /* Banners */
    Route::get('banners/listing', 'BannerController@listing')->name('banners.listing');
    Route::resource('banners', 'BannerController');

    /* Citites */
    Route::get('cities/listing', 'CityController@listing')->name('cities.listing');
    Route::resource('cities', 'CityController');

    /* Delivery Times */
    Route::get('times/listing', 'TimeController@listing')->name('times.listing');
    Route::resource('times', 'TimeController');

    /* Categories */
    Route::get('categories/listing', 'CategoryController@listing')->name('categories.listing');
    Route::resource('categories', 'CategoryController');

    /* Flavours */
    Route::get('flavours/listing', 'FlavourController@listing')->name('flavours.listing');
    Route::resource('flavours', 'FlavourController');

    /* Franchises */
    Route::get('franchises/listing', 'FranchiseController@listing')->name('franchises.listing');
    Route::resource('franchises', 'FranchiseController');

    Route::get('franchisesprice/listing', 'FranchisePriceController@listing')->name('franchisesprice.listing');
    Route::resource('franchisesprice', 'FranchisePriceController');

    /* Cakes */
    Route::get('cakes/listing', 'CakeController@listing')->name('cakes.listing');
    Route::resource('cakes', 'CakeController');

    Route::get('products/listing', 'ProductController@listing')->name('products.listing');
    Route::resource('products', 'ProductController');

    /*Staff*/
    Route::get('staffs/listing', 'StaffController@listing')->name('staffs.listing');
    Route::resource('staffs', 'StaffController');

    Route::get('assignorders/listing', 'AssignOrderController@listing')->name('assignorders.listing');
    Route::resource('assignorders', 'AssignOrderController');

    /*Coupons*/
    Route::get('coupons/listing', 'CouponController@listing')->name('coupons.listing');
    Route::resource('coupons', 'CouponController');

    /*User*/
    Route::get('customers/listing', 'CustomerController@listing')->name('customers.listing');
    Route::resource('customers', 'CustomerController');

    /*prices*/
    Route::get('prices/listing', 'PricesController@listing')->name('prices.listing');
    Route::resource('prices', 'PricesController');

    /*Order*/
    Route::get('orders/listing', 'OrderController@listing')->name('orders.listing');
    Route::resource('orders', 'OrderController');

    /*Cake Price*/
    Route::get('cakeprices/listing', 'PriceCategoryController@listing')->name('cakeprices.listing');
    Route::resource('cakeprices', 'PriceCategoryController');

    /* SubCategories */
    Route::get('subcategories/listing', 'SubCategoryController@listing')->name('subcategories.listing');
    Route::resource('subcategories', 'SubCategoryController');

    /*Invoices*/
    Route::get('invoices/listing', 'MainInvoiceController@listing')->name('invoices.listing');
    Route::resource('invoices', 'MainInvoiceController');

    /*BalanceSheet*/
    Route::get('balances/listing', 'BalanceSheetController@listing')->name('balances.listing');
    Route::resource('balances', 'BalanceSheetController');

  /*SaleReturns*/
    Route::get('salereturns/listing', 'SaleReturnController@listing')->name('salereturns.listing');
    Route::resource('salereturns', 'SaleReturnController');


    Route::get('salereturninvoices/listing', 'SaleReturnInvoiceController@listing')->name('salereturninvoices.listing');
    Route::resource('salereturninvoices', 'SaleReturnInvoiceController');

});

Route::group(['middleware' => ['revalidate'], 'namespace' => 'Admin'] , function () {
    /* Change Password */
    Route::get('change-password', 'DashboardController@showChangePassword')->name('showChangePass');
    Route::post('change-password', 'DashboardController@changePassword')->name('changepass');

    /* Profile */
    Route::get('myprofile', 'DashboardController@showProfile')->name('showProfile');
    Route::post('myprofile', 'DashboardController@editProfile')->name('editProfile');

    /* Unique E-mail */
    Route::post('unique-email', 'DashboardController@checkUniqueEmail')->name('uniqueemail');
    /* Check Unique Admin Email */
    Route::post('unique-admin-email', 'DashboardController@checkUniqueAdminEmail')->name('uniqueAdminemail');
    /* Check Old Password */
    Route::post('check-password', 'DashboardController@checkOldPassword')->name('checkoldpass');
});

