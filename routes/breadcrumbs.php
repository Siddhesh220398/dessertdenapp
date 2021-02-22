<?php

/* Dashboard */
Breadcrumbs::register('dashboard', function($breadcrumbs)
{
    $breadcrumbs->push('Dashboard', route('admin.dashboard.index'));
});

/* Change Password */
Breadcrumbs::register('change_pass', function($breadcrumbs)
{
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('Change Password', route('admin.showChangePass'));
});

/* Settings */
Breadcrumbs::register('settings', function($breadcrumbs)
{
	$breadcrumbs->parent('dashboard');
    $breadcrumbs->push('Settings', route('admin.showChangePass'));
});

/* Edit Profile */
Breadcrumbs::register('my_profile', function($breadcrumbs){
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('Profile', route('admin.showProfile'));
});

/* Users */
Breadcrumbs::register('users', function($breadcrumbs)
{
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('Users', route('admin.users.index'));
});
Breadcrumbs::register('add_user', function($breadcrumbs)
{
    $breadcrumbs->parent('users');
    $breadcrumbs->push('Add New User', route('admin.users.create'));
});
Breadcrumbs::register('user_notification', function($breadcrumbs)
{
    $breadcrumbs->parent('users');
    $breadcrumbs->push('Send Notification', route('admin.users.send_notification'));
});
Breadcrumbs::register('edit_user', function($breadcrumbs, $user)
{
    $breadcrumbs->parent('users');
    $breadcrumbs->push('Edit User', route('admin.users.edit', $user->id));
});
Breadcrumbs::register('user_details', function($breadcrumbs, $user)
{
    $breadcrumbs->parent('users');
    $breadcrumbs->push('View User Detail', route('admin.users.show', $user->id));
});
Breadcrumbs::register('user_chat_messages', function($breadcrumbs, $user_id, $receiver_id)
{
    $breadcrumbs->parent('users');
    $breadcrumbs->push('View User Chat Messages', route('admin.users.chat_messages', [$user_id, $receiver_id]));
});

/* Cities */
Breadcrumbs::register('cities', function($breadcrumbs)
{
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('Cities', route('admin.cities.index'));
});
Breadcrumbs::register('add_city', function($breadcrumbs)
{
    $breadcrumbs->parent('cities');
    $breadcrumbs->push('Add New City', route('admin.cities.create'));
});
Breadcrumbs::register('edit_city', function($breadcrumbs, $city)
{
    $breadcrumbs->parent('cities');
    $breadcrumbs->push('Edit City', route('admin.cities.edit', $city->id));
});
Breadcrumbs::register('city_details', function($breadcrumbs, $city)
{
    $breadcrumbs->parent('cities');
    $breadcrumbs->push('View City', route('admin.cities.show', $city->id));
});

/* Categories */
Breadcrumbs::register('categories', function($breadcrumbs)
{
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('Categories', route('admin.categories.index'));
});
Breadcrumbs::register('add_category', function($breadcrumbs)
{
    $breadcrumbs->parent('categories');
    $breadcrumbs->push('Add New Category', route('admin.categories.create'));
});
Breadcrumbs::register('edit_category', function($breadcrumbs, $category)
{
    $breadcrumbs->parent('categories');
    $breadcrumbs->push('Edit Category', route('admin.categories.edit', $category->id));
});
Breadcrumbs::register('category_details', function($breadcrumbs, $category)
{
    $breadcrumbs->parent('categories');
    $breadcrumbs->push('View Category', route('admin.categories.show', $category->id));
});

/* Categories */
Breadcrumbs::register('subcategories', function($breadcrumbs)
{
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('SubCategories', route('admin.subcategories.index'));
});
Breadcrumbs::register('add_subcategory', function($breadcrumbs)
{
    $breadcrumbs->parent('subcategories');
    $breadcrumbs->push('Add New Sub Category', route('admin.subcategories.create'));
});
Breadcrumbs::register('edit_subcategory', function($breadcrumbs, $subcategory)
{
    $breadcrumbs->parent('subcategories');
    $breadcrumbs->push('Edit Sub Category', route('admin.subcategories.edit', $subcategory->id));
});
Breadcrumbs::register('subcategory_details', function($breadcrumbs, $subcategory)
{
    $breadcrumbs->parent('subcategories');
    $breadcrumbs->push('View Sub Category', route('admin.subcategories.show', $subcategory->id));
});



/* Banners */
Breadcrumbs::register('banners', function($breadcrumbs)
{
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('banners', route('admin.banners.index'));
});
Breadcrumbs::register('add_banners', function($breadcrumbs)
{
    $breadcrumbs->parent('banners');
    $breadcrumbs->push('Add New Banners', route('admin.banners.create'));
});

Breadcrumbs::register('banners_details', function($breadcrumbs, $banners)
{
    $breadcrumbs->parent('banners');
    $breadcrumbs->push('View Banners', route('admin.banners.show', $banners->id));
});


/*Times*/
Breadcrumbs::register('times', function($breadcrumbs)
{
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('times', route('admin.times.index'));
});
Breadcrumbs::register('add_times', function($breadcrumbs)
{
    $breadcrumbs->parent('times');
    $breadcrumbs->push('Add New Times', route('admin.times.create'));
});

Breadcrumbs::register('times_details', function($breadcrumbs, $times)
{
    $breadcrumbs->parent('times');
    $breadcrumbs->push('View Times', route('admin.times.show', $times->id));
});

Breadcrumbs::register('edit_times', function($breadcrumbs, $times)
{
    $breadcrumbs->parent('times');
    $breadcrumbs->push('Edit Time', route('admin.times.edit', $times->id));
});


/*flavours*/
Breadcrumbs::register('flavours', function($breadcrumbs)
{
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('flavours', route('admin.flavours.index'));
});
Breadcrumbs::register('add_flavours', function($breadcrumbs)
{
    $breadcrumbs->parent('flavours');
    $breadcrumbs->push('Add New Flavours', route('admin.flavours.create'));
});

Breadcrumbs::register('flavours_details', function($breadcrumbs, $flavours)
{
    $breadcrumbs->parent('flavours');
    $breadcrumbs->push('View Flavours', route('admin.flavours.show', $flavours->id));
});

Breadcrumbs::register('edit_flavours', function($breadcrumbs, $flavours)
{
    $breadcrumbs->parent('flavours');
    $breadcrumbs->push('Edit Flavour', route('admin.flavours.edit', $flavours->id));
});


/*franchise*/
Breadcrumbs::register('franchises', function($breadcrumbs)
{
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('franchises', route('admin.franchises.index'));
});
Breadcrumbs::register('add_franchises', function($breadcrumbs)
{
    $breadcrumbs->parent('franchises');
    $breadcrumbs->push('Add New franchises', route('admin.franchises.create'));
});

Breadcrumbs::register('franchises_details', function($breadcrumbs, $franchises)
{
    $breadcrumbs->parent('franchises');
    $breadcrumbs->push('View franchises', route('admin.franchises.show', $franchises->id));
});

Breadcrumbs::register('edit_franchises', function($breadcrumbs, $franchises)
{
    $breadcrumbs->parent('franchises');
    $breadcrumbs->push('Edit franchises', route('admin.franchises.edit', $franchises->id));
});

/*franchiseprice*/
Breadcrumbs::register('franchisesprice', function($breadcrumbs)
{
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('franchisesprice', route('admin.franchisesprice.index'));
});
Breadcrumbs::register('add_franchisesprice', function($breadcrumbs)
{
    $breadcrumbs->parent('franchises');
    $breadcrumbs->push('Add New franchises', route('admin.franchisesprice.create'));
});

Breadcrumbs::register('franchisesprice_details', function($breadcrumbs, $franchisesprice)
{
    $breadcrumbs->parent('franchisesprice');
    $breadcrumbs->push('View franchises price', route('admin.franchisesprice.show', $franchisesprice->id));
});

Breadcrumbs::register('edit_franchisesprice', function($breadcrumbs, $franchisesprice)
{
    $breadcrumbs->parent('franchisesprice');
    $breadcrumbs->push('Edit franchisesprice', route('admin.franchisesprice.edit', $franchisesprice->id));
});

/*Product*/
Breadcrumbs::register('products', function($breadcrumbs)
{
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('products', route('admin.products.index'));
});
Breadcrumbs::register('importproduct', function($breadcrumbs)
{
    $breadcrumbs->parent('products');
    $breadcrumbs->push('importproduct', route('admin.products.import'));
});
Breadcrumbs::register('add_products', function($breadcrumbs)
{
    $breadcrumbs->parent('products');
    $breadcrumbs->push('Add New products', route('admin.products.create'));
});

Breadcrumbs::register('products_details', function($breadcrumbs, $products)
{
    $breadcrumbs->parent('products');
    $breadcrumbs->push('View products', route('admin.products.show', $products->id));
});

Breadcrumbs::register('edit_products', function($breadcrumbs, $products)
{
    $breadcrumbs->parent('products');
    $breadcrumbs->push('Edit products', route('admin.products.edit', $products->id));
});

/*Cake*/
Breadcrumbs::register('cakes', function($breadcrumbs)
{
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('cakes', route('admin.cakes.index'));
});
Breadcrumbs::register('add_cakes', function($breadcrumbs)
{
    $breadcrumbs->parent('cakes');
    $breadcrumbs->push('Add New cakes', route('admin.cakes.create'));
});

Breadcrumbs::register('cakes_details', function($breadcrumbs, $cakes)
{
    $breadcrumbs->parent('cakes');
    $breadcrumbs->push('View cakes', route('admin.cakes.show', $cakes->id));
});

Breadcrumbs::register('edit_cakes', function($breadcrumbs, $cakes)
{
    $breadcrumbs->parent('cakes');
    $breadcrumbs->push('Edit cakes', route('admin.cakes.edit', $cakes->id));
});


/*staff*/
Breadcrumbs::register('staffs', function($breadcrumbs)
{
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('staffs', route('admin.staffs.index'));
});
Breadcrumbs::register('add_staffs', function($breadcrumbs)
{
    $breadcrumbs->parent('staffs');
    $breadcrumbs->push('Add New staffs', route('admin.staffs.create'));
});

Breadcrumbs::register('staffs_details', function($breadcrumbs, $staffs)
{
    $breadcrumbs->parent('staffs');
    $breadcrumbs->push('View staffs', route('admin.staffs.show', $staffs->id));
});

Breadcrumbs::register('edit_staffs', function($breadcrumbs, $staff)
{
    $breadcrumbs->parent('staffs');
    $breadcrumbs->push('Edit staffs', route('admin.staffs.edit', $staff->id));
});

/*Coupon*/
Breadcrumbs::register('coupons', function($breadcrumbs)
{
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('coupons', route('admin.coupons.index'));
});
Breadcrumbs::register('add_coupons', function($breadcrumbs)
{
    $breadcrumbs->parent('coupons');
    $breadcrumbs->push('Add New coupons', route('admin.coupons.create'));
});

Breadcrumbs::register('coupons_details', function($breadcrumbs, $coupons)
{
    $breadcrumbs->parent('coupons');
    $breadcrumbs->push('View coupons', route('admin.coupons.show', $coupons->id));
});

Breadcrumbs::register('edit_coupons', function($breadcrumbs, $coupon)
{
    $breadcrumbs->parent('coupons');
    $breadcrumbs->push('Edit coupons', route('admin.coupons.edit', $coupon->id));
});

/*Customers*/
Breadcrumbs::register('customers', function($breadcrumbs)
{
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('customers', route('admin.customers.index'));
});
Breadcrumbs::register('add_customers', function($breadcrumbs)
{
    $breadcrumbs->parent('customers');
    $breadcrumbs->push('Add New customers', route('admin.customers.create'));
});

Breadcrumbs::register('customers_details', function($breadcrumbs, $customers)
{
    $breadcrumbs->parent('customers');
    $breadcrumbs->push('View customers', route('admin.customers.show', $customers->id));
});

Breadcrumbs::register('edit_customers', function($breadcrumbs, $customer)
{
    $breadcrumbs->parent('customers');
    $breadcrumbs->push('Edit customers', route('admin.customers.edit', $customer->id));
});

/*Order*/
Breadcrumbs::register('orders', function($breadcrumbs)
{
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('orders', route('admin.orders.index'));
});
Breadcrumbs::register('add_orders', function($breadcrumbs)
{
    $breadcrumbs->parent('orders');
    $breadcrumbs->push('Add New orders', route('admin.orders.create'));
});

Breadcrumbs::register('orders_details', function($breadcrumbs, $orders)
{
    $breadcrumbs->parent('orders');
    $breadcrumbs->push('View orders', route('admin.orders.show', $orders->id));
});

Breadcrumbs::register('edit_orders', function($breadcrumbs, $order)
{
    $breadcrumbs->parent('orders');
    $breadcrumbs->push('Edit orders', route('admin.orders.edit', $order->id));
});


/*prices*/

Breadcrumbs::register('prices', function($breadcrumbs)
{
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('prices', route('admin.prices.index'));
});
Breadcrumbs::register('add_prices', function($breadcrumbs)
{
    $breadcrumbs->parent('prices');
    $breadcrumbs->push('Add New prices', route('admin.prices.create'));
});

Breadcrumbs::register('prices_details', function($breadcrumbs, $prices)
{
    $breadcrumbs->parent('prices');
    $breadcrumbs->push('View prices', route('admin.prices.show', $prices->id));
});

Breadcrumbs::register('edit_prices', function($breadcrumbs, $price)
{
    $breadcrumbs->parent('prices');
    $breadcrumbs->push('Edit prices', route('admin.prices.edit', $price->id));
});

/*Cake Price*/
Breadcrumbs::register('cakeprices', function($breadcrumbs)
{
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('cakeprices', route('admin.cakeprices.index'));
});
Breadcrumbs::register('add_cakeprices', function($breadcrumbs)
{
    $breadcrumbs->parent('cakeprices');
    $breadcrumbs->push('Add New prices', route('admin.cakeprices.create'));
});

Breadcrumbs::register('cakeprices_details', function($breadcrumbs, $cakeprices)
{
    $breadcrumbs->parent('cakeprices');
    $breadcrumbs->push('View prices', route('admin.cakeprices.show', $cakeprices->id));
});

Breadcrumbs::register('edit_cakeprices', function($breadcrumbs, $cakeprice)
{
    $breadcrumbs->parent('cakeprices');
    $breadcrumbs->push('Edit prices', route('admin.cakeprices.edit', $cakeprice->id));
});


/*Invoices*/
Breadcrumbs::register('invoices', function($breadcrumbs)
{
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('invoice', route('admin.invoices.index'));
});
Breadcrumbs::register('add_invoices', function($breadcrumbs)
{
    $breadcrumbs->parent('invoices');
    $breadcrumbs->push('Add New Invoice', route('admin.invoices.create'));
});

Breadcrumbs::register('invoices_details', function($breadcrumbs, $invoices)
{
    $breadcrumbs->parent('invoices');
    $breadcrumbs->push('View invoice', route('admin.invoices.show', $invoices));
});

Breadcrumbs::register('edit_invoices', function($breadcrumbs, $invoice)
{
    $breadcrumbs->parent('invoices');
    $breadcrumbs->push('Edit invoice', route('admin.invoices.edit', $invoice->id));
});


/*Balances*/
Breadcrumbs::register('balances', function($breadcrumbs)
{
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('balance', route('admin.balances.index'));
});
Breadcrumbs::register('add_balances', function($breadcrumbs)
{
    $breadcrumbs->parent('balances');
    $breadcrumbs->push('Add New Balance', route('admin.balances.create'));
});

Breadcrumbs::register('balances_details', function($breadcrumbs, $balances)
{
    $breadcrumbs->parent('balances');
    $breadcrumbs->push('View Balance', route('admin.balances.show', $balances));
});

Breadcrumbs::register('edit_balances', function($breadcrumbs, $balance)
{
    $breadcrumbs->parent('balances');
    $breadcrumbs->push('Edit balance', route('admin.balances.edit', $balance->id));
});

/* Sales Return*/
Breadcrumbs::register('salereturns', function($breadcrumbs)
{
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('salereturn', route('admin.salereturns.index'));
});
Breadcrumbs::register('add_salereturns', function($breadcrumbs)
{
    $breadcrumbs->parent('salereturns');
    $breadcrumbs->push('Add New Sales Return', route('admin.salereturns.create'));
});

Breadcrumbs::register('salereturns_details', function($breadcrumbs, $salereturns)
{
    $breadcrumbs->parent('salereturns');
    $breadcrumbs->push('View Sales Return', route('admin.salereturns.show', $salereturns));
});

Breadcrumbs::register('edit_salereturns', function($breadcrumbs, $salereturn)
{
    $breadcrumbs->parent('salereturns');
    $breadcrumbs->push('Edit Sales return', route('admin.salereturns.edit', $salereturn->id));
});

/*SaleReturnInvoices*/
Breadcrumbs::register('salereturninvoices', function($breadcrumbs)
{
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('salereturninvoice', route('admin.salereturninvoices.index'));
});
Breadcrumbs::register('add_salereturninvoices', function($breadcrumbs)
{
    $breadcrumbs->parent('salereturninvoices');
    $breadcrumbs->push('Add New Invoice', route('admin.salereturninvoices.create'));
});

Breadcrumbs::register('salereturninvoices_details', function($breadcrumbs, $salereturninvoices)
{
    $breadcrumbs->parent('salereturninvoices');
    $breadcrumbs->push('View Invoice', route('admin.salereturninvoices.show', $salereturninvoices));
});

Breadcrumbs::register('edit_salereturninvoices', function($breadcrumbs, $salereturninvoice)
{
    $breadcrumbs->parent('salereturninvoices');
    $breadcrumbs->push('Edit Invoice', route('admin.salereturninvoices.edit', $salereturninvoice->id));
});
