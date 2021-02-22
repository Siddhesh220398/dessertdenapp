<?php

use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $menus = array(
            [
                'section_id' => 'Dashboard',
                'title' => 'Dashboard',
                'route' => 'admin.dashboard.index',
                'image' => 'fa fa-home',
                'sequence' => 1,
                'permissions' => 'access',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ],
            [
                'section_id' => 'Manage Banners',
                'title' => 'Manage Banners',
                'route' => 'admin.banners.index',
                'image' => 'fa fa-image',
                'sequence' => 1,
                'permissions' => 'access,add,delete',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ],
            [
                'section_id' => 'Manage Cities',
                'title' => 'Manage Cities',
                'route' => 'admin.cities.index',
                'image' => 'fa fa-map',
                'sequence' => 1,
                'permissions' => 'access,add,edit,delete',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ],
            [
                'section_id' => 'Manage Delivery times',
                'title' => 'Manage Delivery times',
                'route' => 'admin.times.index',
                'image' => 'fa fa-clock-o',
                'sequence' => 1,
                'permissions' => 'access,add,edit,delete',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ],
            [
                'section_id' => 'Manage Categories',
                'title' => 'Manage Categories',
                'route' => 'admin.categories.index',
                'image' => 'fa fa-list',
                'sequence' => 1,
                'permissions' => 'access,add,edit,delete',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ],
            [
                'section_id' => 'Manage Sub Categories',
                'title' => 'Manage Sub Categories',
                'route' => 'admin.subcategories.index',
                'image' => 'fa fa-list',
                'sequence' => 1,
                'permissions' => 'access,add,edit,delete',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ],
            [
                'section_id' => 'Manage Franchises',
                'title' => 'Manage Franchises',
                'route' => 'admin.franchises.index',
                'image' => 'fa fa-bank',
                'sequence' => 1,
                'permissions' => 'access,add,edit,delete',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ],
            [
                'section_id' => 'Manage Franchises Price',
                'title' => 'Manage Franchises Price',
                'route' => 'admin.franchisesprice.index',
                'image' => 'fa fa-bank',
                'sequence' => 1,
                'permissions' => 'access,add,edit,delete',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ],
            [
                'section_id' => 'Manage Flavours',
                'title' => 'Manage Flavours',
                'route' => 'admin.flavours.index',
                'image' => 'fa fa-list-ol',
                'sequence' => 1,
                'permissions' => 'access,add,edit,delete',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ],
            [
                'section_id' => 'Manage Products',
                'title' => 'Manage Products',
                'route' => 'admin.products.index',
                'image' => 'fa fa-birthday-cake',
                'sequence' => 1,
                'permissions' => 'access,add,edit,delete',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ],
            [
                'section_id' => 'Manage Price',
                'title' => 'Manage Price',
                'route' => 'admin.prices.index',
                'image' => 'fa fa-shopping-cart',
                'sequence' => 1,
                'permissions' => 'access,add,edit,delete',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ],
            [
                'section_id' => 'Manage Size Price',
                'title' => 'Manage Size Price',
                'route' => 'admin.cakeprices.index',
                'image' => 'fa fa-image',
                'sequence' => 1,
                'permissions' => 'access,add,edit,delete',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ],

            [
                'section_id' => 'Manage Staff',
                'title' => 'Manage Staff',
                'route' => 'admin.staffs.index',
                'image' => 'fa fa-users',
                'sequence' => 1,
                'permissions' => 'access,add,edit,delete',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ],
            [
                'section_id' => 'Manage Coupon',
                'title' => 'Manage Coupon',
                'route' => 'admin.coupons.index',
                'image' => 'fa fa-ticket',
                'sequence' => 1,
                'permissions' => 'access,add,edit,delete',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ],
            [
                'section_id' => 'Manage Order',
                'title' => 'Manage Order',
                'route' => 'admin.orders.index',
                'image' => 'fa fa-shopping-cart',
                'sequence' => 1,
                'permissions' => 'access,add,edit,delete',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ],
            [
                'section_id' => 'Manage Customer',
                'title' => 'Manage Customer',
                'route' => 'admin.customers.index',
                'image' => 'fa fa-user',
                'sequence' => 1,
                'permissions' => 'access,add,edit,delete',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ],
            [
                'section_id' => 'Invoice',
                'title' => 'Invoice',
                'route' => 'admin.invoices.index',
                'image' => 'fa fa-user',
                'sequence' => 1,
                'permissions' => 'access,add,edit,delete',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ],

        );

        foreach ($menus as $role) {
            $section_id = DB::table('sections')->where('name', $role['section_id'])->value('id');

            $role['section_id'] = ($section_id > 0 ? $section_id : 1);

            DB::table('roles')->insert($role);
        }
    }
}
