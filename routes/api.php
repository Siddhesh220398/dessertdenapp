<?php



use Illuminate\Http\Request;



/*

|--------------------------------------------------------------------------

| API Routes

|--------------------------------------------------------------------------

|

| Here is where you can register API routes for your application. These

| routes are loaded by the RouteServiceProvider within a group which

| is assigned the "api" middleware group. Enjoy building your API!

|

*/



Route::group(['namespace' => 'Api\V1', 'prefix' => 'v1'], function () {

	Route::get('general/categories', 'GeneralController@categories');

	Route::post('general/subcategories', 'GeneralController@subcategories');
	Route::get('general/subcategoriescake', 'GeneralController@subcategoriesCake');

	Route::get('general/cities', 'GeneralController@cities');

	Route::get('general/franchises', 'GeneralController@franchises');

	Route::get('general/banners', 'GeneralController@banners');

	Route::get('general/delivery/times', 'GeneralController@deliveryTimes');

	Route::get('general/flavours', 'GeneralController@flavours');
	Route::get('general/price', 'GeneralController@price');
	Route::get('general/pricecategory', 'GeneralController@priceCat');
	Route::get('general/pricephoto', 'GeneralController@pricePhoto');

	Route::get('general/allproducts','ProductController@productsname');
	Route::post('product/update', 'ProductController@updateProduct');
	Route::post('product/todayspecialproduct', 'ProductController@updateTodaysSpecialProduct');



	Route::post('cake/list', 'CakeController@cakes');

	Route::post('cake/details', 'CakeController@cakeDetails');

	Route::post('cake/today/special', 'ProductController@todaySpecial');



	// Route::post('user/cart/add','UserController@addToCart');


	// Route::post('user/cart/list','UserController@cartList');

	// Route::post('user/cart/delete','UserController@cartRemove');

	Route::post('user/cart/add','UserController@addToCart');
	Route::post('user/cart/list','UserController@cartList');
	Route::post('user/cart/delete','UserController@cartRemove');
	Route::post('user/cart/edit','UserController@cartEdit');
	Route::post('user/wishlist','UserController@wishLists');
	Route::post('user/wishlistremove','UserController@wishListRemove');
	Route::post('user/wishlistDetail','UserController@wishlistDetail');
	Route::post('user/cart/update','UserController@cartUpdate');
	Route::post('apply/coupon','UserController@applyCoupon');
	Route::post('users/login','UserController@login');
	Route::post('users/signUp','UserController@register');
	Route::post('users/check/mobile', 'UserController@checkMobile');
	Route::post('users/social/login', 'UserController@socialLogin');
	Route::post('user/customorder','UserController@customPlaceOrder');
	Route::post('customorder/customPayment','UserController@customPayment');
	Route::post('customorder/customOrderEdit','UserController@editCustomorder');

	Route::post('admin/user/addBalance', 'AdminController@addUserBalance');
	Route::post('admin/user/minusBalance', 'AdminController@minusBalance');
Route::post('user/addVallet','UserController@addVallet');


	Route::post('user/placeorder','UserController@placeOrder');
	Route::post('user/orderlist','UserController@orderList');







	Route::post('admin/login','AdminController@login');
	Route::post('admin/order/delete', 'AdminController@orderDelete');
	Route::post('admin/order/edit', 'AdminController@orderEdit');
	Route::get('user/details','AdminController@getUser');
	Route::post('user/activebalance','AdminController@activeBalance');



	Route::post('franchise/login','FranchiseController@login');
// 	Route::post('franchise/getProfile','FranchiseController@getProfile');
	Route::post('franchise/updateProfile','FranchiseController@editProfile');
	Route::post('franchise/cart/add','FranchiseController@addToCart');
	Route::post('franchise/cart/list','FranchiseController@cartList');
	Route::post('franchise/cart/update','FranchiseController@cartUpdate');
	Route::post('franchise/cart/delete','FranchiseController@cartRemove');
	Route::post('franchise/cart/edit','FranchiseController@cartEdit');
	Route::post('franchise/discount','FranchiseController@franchiseDiscount');
	Route::post('franchise/wishlist','FranchiseController@wishLists');
	Route::post('franchise/wishlistremove','FranchiseController@wishListRemove');
	Route::post('franchise/wishlistDetail','FranchiseController@wishlistDetail');
	Route::post('franchise/placeorder','FranchiseController@placeOrder');
	// Route::post('franchise/customorder','FranchiseController@customPlaceOrder');

	Route::post('franchise/customOrderEdit','FranchiseController@editCustomorder');

	Route::post('franchise/addVallet','FranchiseController@addVallet');
	Route::post('franchise/assignorderList','FranchiseController@assignorderList');
	Route::post('franchise/addaddress','FranchiseController@additionalAddress');
	Route::post('franchise/orderlist','FranchiseController@orderList');
	Route::post('franchise/customorder/customPayment','FranchiseController@customPayment');

	Route::post('franchisenew/customorder','FranchiseController@customPlaceOrder');
	Route::post('franchise/salesReturn','FranchiseController@salesReturn');
	Route::post('franchise/salesReturnHistory','FranchiseController@salesReturnHistory');
	Route::post('franchise/addStock','FranchiseController@addStock');
	Route::post('franchise/viewStock','FranchiseController@viewStock');

	Route::post('franchise/addBalance', 'FranchiseAdminController@addBalance');
	Route::post('franchise/minusBalance', 'FranchiseAdminController@minusBalance');

	Route::post('product/products','ProductController@products');
	Route::post('product/productDetails','ProductController@productDetails');

//Route::post('user/orderInvoice', 'AdminNController@orderInvoice');


Route::post('user/saveLater', 'SaveLaterController@saveLater');
Route::post('user/AddCart', 'SaveLaterController@addToCart');
Route::post('user/getSaveLater', 'SaveLaterController@getSaveLater');





});


Route::group(['namespace' => 'Api\V1', 'prefix' => 'v1', 'middleware' => 'auth:api'], function () {

	// Route::post('cart/order','UserController@placeOrder');

	// Route::get('cart/orderlist','UserController@orderList');

	Route::post('cart/orderDetail','UserController@orderDetail');



	// Route::post('cart/customorder','UserController@customPlaceOrder');





	Route::get('users/profile', 'UserController@getProfile');

	Route::post('users/update/profile', 'UserController@updateProfile');

	Route::post('users/update/profile/image', 'UserController@editProfileImage');



});

Route::group(['namespace' => 'Api\V1', 'prefix' => 'v1/franchise/', 'middleware' => 'apiauthfranchise'], function () {
    Route::get('getProfile','FranchiseController@getProfile');
});



Route::group(['namespace' => 'Api\V1', 'prefix' => 'v1/admin/', 'middleware' => 'apiauthadmin'], function () {


    Route::any('admindetails', 'AdminNewController@getAllAdmin');
    Route::post('changeCustomPermission', 'AdminNewController@changeCustomPermission');
    Route::post('franchiseAssign', 'AdminNewController@franchiseAssign');
    Route::get('profile', 'AdminController@getProfile');
	Route::get('chef/list', 'AdminController@chefList');
	Route::get('deliveryBoy/list', 'AdminController@deliveryBoyList');

	Route::get('order/setTimer', 'AdminController@setTimer');
	Route::get('order/stopTimer', 'AdminController@stopTimer');
	Route::get('customorder/setTimerCustom', 'AdminController@setTimerCustom');
	Route::get('customorder/stopTimerCustom', 'AdminController@stopTimerCustom');

	Route::post('user/orders','AdminController@orders');
	Route::post('user/chef/assign', 'AdminController@chefAssign');


	Route::post('user/chef/multiorderassign', 'AdminController@multiplechefAssign');

	Route::get('user/chef/assign/list', 'AdminController@assignList');
	Route::post('user/order/status', 'AdminController@orderStatus');
	Route::post('user/order/multipleorderStatus', 'AdminController@multipleorderStatus');

	Route::post('user/deliveryBoy/assign', 'AdminController@deliveryBoyAssign');
	Route::post('user/deliveryBoy/multiorderassign', 'AdminController@multipledeliveryBoyAssign');

	Route::post('user/generatePdf', 'AdminController@generatePdf');
	Route::post('user/normalgeneratePdf', 'AdminController@normalgeneratePdf');
	Route::post('user/customorder/reject', 'AdminController@adminReject');
	Route::post('user/order/search', 'AdminController@userOrdersearch');
	Route::post('user/chef/orderImage', 'AdminController@orderImage');
	Route::post('user/order/customAmount', 'AdminController@customAmount');
	Route::get('user/order/todayTotal', 'AdminController@todayTotal');

	Route::post('franchise/orders','FranchiseAdminController@orders');
	// Route::get('franchise/profile', 'AdminController@getProfile');
	Route::get('franchise/chef/list', 'AdminController@chefList');
	Route::post('franchise/chef/assign', 'FranchiseAdminController@chefAssign');
	Route::post('franchise/chef/multiorderassign', 'FranchiseAdminController@multiplechefAssign');


	Route::get('assign/list', 'FranchiseAdminController@assignList');
	Route::post('franchise/order/status', 'FranchiseAdminController@orderStatus');
	Route::post('franchise/order/multipleorderStatus', 'FranchiseAdminController@multipleorderStatus');
	Route::get('franchise/deliveryBoy/list', 'AdminController@deliveryBoyList');
	Route::post('franchise/deliveryBoy/assign', 'FranchiseAdminController@deliveryBoyAssign');
	Route::post('franchise/deliveryBoy/multiorderassign', 'FranchiseAdminController@multipledeliveryBoyAssign');
	// Route::post('franchise/generatePdf', 'FranchiseAdminController@generatePdf');
	Route::post('franchise/customorder/reject', 'FranchiseAdminController@adminReject');
	Route::post('franchise/order/search', 'FranchiseAdminController@franchiseOrdersearch');
	Route::post('franchise/chef/orderImage', 'FranchiseAdminController@orderImage');
	Route::post('franchise/order/customAmount', 'FranchiseAdminController@customAmount');
	Route::get('franchise/order/todayTotal', 'FranchiseAdminController@todayTotal');

    Route::post('franchise/salesReturnHistory','FranchiseAdminController@salesReturnHistory');
    Route::post('franchise/salesReturnStatus','FranchiseAdminController@salesReturnStatus');


// 	Route::post('user/chef/multipleassign', 'AdminNewController@multipleschefAssign');
// 	Route::get('assignlist', 'AdminNewController@assignList');

		Route::post('filter/order', 'AdminNewController@ordersForFranchise');





});





