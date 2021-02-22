<?php

use Illuminate\Database\Seeder;

class SectionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sections = [
            [
                'name' => 'Dashboard',
                'image' => 'fa fa-home',
                'sequence' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'name' => 'Manage Banners',
                'image' => 'fa fa-image',
                'sequence' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'name' => 'Manage Cities',
                'image' => 'fa fa-map',
                'sequence' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'name' => 'Manage Delivery times',
                'image' => 'fa fa-clock-o',
                'sequence' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'name' => 'Manage Categories',
                'image' => 'fa fa-list',
                'sequence' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'name' => 'Manage Sub Categories',
                'image' => 'fa fa-list',
                'sequence' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'name' => 'Manage Franchises',
                'image' => 'fa fa-bank',
                'sequence' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'name' => 'Manage Franchises Price',
                'image' => 'fa fa-bank',
                'sequence' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'name' => 'Manage Flavours',
                'image' => 'fa fa-list-ol',
                'sequence' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'name' => 'Manage Products',
                'image' => 'fa fa-birthday-cake',
                'sequence' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'name' => 'Manage Prices',
                'image' => 'fa fa-shopping-cart',
                'sequence' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'name' => 'Manage Size Price',
                'image' => 'fa fa-shopping-cart',
                'sequence' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
             [
                 'name' => 'Manage Staff',
                 'image' => 'fa fa-users',
                 'sequence' => 1,
                 'created_at' => \Carbon\Carbon::now(),
                 'updated_at' => \Carbon\Carbon::now(),
             ],
             [
                 'name' => 'Manage Coupon',
                 'image' => 'fa fa-ticket',
                 'sequence' => 1,
                 'created_at' => \Carbon\Carbon::now(),
                 'updated_at' => \Carbon\Carbon::now(),
             ],

             [
                 'name' => 'Manage Order',
                 'image' => 'fa fa-shopping-cart',
                 'sequence' => 1,
                 'created_at' => \Carbon\Carbon::now(),
                 'updated_at' => \Carbon\Carbon::now(),
             ],
            [
                'name' => 'Manage Customer',
                'image' => 'fa fa-user',
                'sequence' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],

            [
                'name' => 'Invoice',
                'image' => 'fa fa-file',
                'sequence' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
        ];
        foreach($sections as $section){
	        DB::table('sections')->insert($section);
		}
    }
}
